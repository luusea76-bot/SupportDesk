<?php

namespace Tests\Feature;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_number_is_automatically_generated(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = Customer::factory()->create();

        $ticket = Ticket::create([
            'title' => 'Database connection timeout',
            'description' => 'Cannot connect to primary read replica.',
            'customer_id' => $customer->id,
            'created_by_user_id' => $admin->id,
            'priority' => TicketPriority::High,
            'category' => TicketCategory::Technical,
            'status' => TicketStatus::Open,
        ]);

        $year = now()->format('Y');
        $this->assertNotNull($ticket->ticket_number);
        $this->assertStringStartsWith("SD-{$year}-", $ticket->ticket_number);
    }

    public function test_agent_can_create_ticket_with_valid_data(): void
    {
        $agent = User::factory()->agent()->create();
        $customer = Customer::factory()->create();

        $response = $this->actingAs($agent)->post('/tickets', [
            'title' => 'Email server SSL handshake failed',
            'description' => 'Port 587 rejecting TLS connections.',
            'customer_id' => $customer->id,
            'priority' => TicketPriority::Urgent->value,
            'category' => TicketCategory::Technical->value,
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('tickets', [
            'title' => 'Email server SSL handshake failed',
            'customer_id' => $customer->id,
            'priority' => TicketPriority::Urgent->value,
        ]);

        // Audit log was created
        $this->assertDatabaseHas('ticket_activities', [
            'activity_type' => 'created',
            'user_id' => $agent->id,
        ]);
    }

    public function test_ticket_creation_requires_mandatory_fields(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->post('/tickets', []);

        $response->assertSessionHasErrors(['title', 'description', 'customer_id', 'priority', 'category']);
    }

    public function test_customer_can_only_view_their_own_tickets(): void
    {
        $customerUser = User::factory()->customer()->create();
        $customerProfile = Customer::factory()->create(['user_id' => $customerUser->id]);

        $otherCustomer = Customer::factory()->create();
        $otherUser = User::factory()->customer()->create();

        // Own ticket
        $ownTicket = Ticket::factory()->create([
            'customer_id' => $customerProfile->id,
            'created_by_user_id' => $customerUser->id,
        ]);

        // Other customer ticket
        $otherTicket = Ticket::factory()->create([
            'customer_id' => $otherCustomer->id,
            'created_by_user_id' => $otherUser->id,
        ]);

        // Customer views own ticket: OK
        $response1 = $this->actingAs($customerUser)->get("/tickets/{$ownTicket->id}");
        $response1->assertStatus(200);

        // Customer tries to view other's ticket: 403 Forbidden
        $response2 = $this->actingAs($customerUser)->get("/tickets/{$otherTicket->id}");
        $response2->assertStatus(403);
    }

    public function test_ticket_assignment_records_audit_activity(): void
    {
        $admin = User::factory()->admin()->create();
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['assigned_to_user_id' => null]);

        $response = $this->actingAs($admin)->post("/tickets/{$ticket->id}/assign", [
            'assigned_to_user_id' => $agent->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to_user_id' => $agent->id,
        ]);

        $this->assertDatabaseHas('ticket_activities', [
            'ticket_id' => $ticket->id,
            'activity_type' => 'assigned',
            'user_id' => $admin->id,
        ]);
    }

    public function test_ticket_status_transitions_are_enforced(): void
    {
        $agent = User::factory()->agent()->create();
        $ticket = Ticket::factory()->create(['status' => TicketStatus::Open]);

        // Valid transition: Open -> In Progress
        $response1 = $this->actingAs($agent)->post("/tickets/{$ticket->id}/status", [
            'status' => TicketStatus::InProgress->value,
        ]);
        $response1->assertSessionHasNoErrors();
        $this->assertEquals(TicketStatus::InProgress, $ticket->fresh()->status);

        // Valid transition: In Progress -> Resolved
        $response2 = $this->actingAs($agent)->post("/tickets/{$ticket->id}/status", [
            'status' => TicketStatus::Resolved->value,
        ]);
        $response2->assertSessionHasNoErrors();
        $this->assertEquals(TicketStatus::Resolved, $ticket->fresh()->status);
        $this->assertNotNull($ticket->fresh()->resolved_at);
    }

    public function test_internal_comments_are_hidden_from_customers(): void
    {
        $agent = User::factory()->agent()->create();
        $customerUser = User::factory()->customer()->create();
        $customerProfile = Customer::factory()->create(['user_id' => $customerUser->id]);

        $ticket = Ticket::factory()->create([
            'customer_id' => $customerProfile->id,
            'created_by_user_id' => $customerUser->id,
        ]);

        // Agent posts internal note
        $this->actingAs($agent)->post("/tickets/{$ticket->id}/comments", [
            'body' => 'Secret customer internal credit check: high risk.',
            'is_internal' => 1,
        ]);

        // Agent posts public reply
        $this->actingAs($agent)->post("/tickets/{$ticket->id}/comments", [
            'body' => 'Hello! We are working on your ticket now.',
            'is_internal' => 0,
        ]);

        // Customer views ticket
        $customerView = $this->actingAs($customerUser)->get("/tickets/{$ticket->id}");
        $customerView->assertSee('Hello! We are working on your ticket now.');
        $customerView->assertDontSee('Secret customer internal credit check: high risk.');

        // Agent views ticket
        $agentView = $this->actingAs($agent)->get("/tickets/{$ticket->id}");
        $agentView->assertSee('Secret customer internal credit check: high risk.');
    }
}
