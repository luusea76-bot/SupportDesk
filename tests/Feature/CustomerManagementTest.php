<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_customers_list(): void
    {
        $agent = User::factory()->agent()->create();
        Customer::factory()->count(3)->create();

        $response = $this->actingAs($agent)->get('/customers');
        $response->assertStatus(200);
        $response->assertSee('Customer Directory');
    }

    public function test_customer_user_cannot_access_customer_management(): void
    {
        $customerUser = User::factory()->customer()->create();

        $response = $this->actingAs($customerUser)->get('/customers');
        $response->assertStatus(403);
    }

    public function test_staff_can_create_customer(): void
    {
        $agent = User::factory()->agent()->create();

        $response = $this->actingAs($agent)->post('/customers', [
            'name' => 'Acme Corporation Inc',
            'email' => 'billing@acmecorp.com',
            'phone' => '+1 (555) 333-4444',
            'company_name' => 'Acme Global',
            'notes' => 'Priority VIP Account',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('customers', [
            'name' => 'Acme Corporation Inc',
            'email' => 'billing@acmecorp.com',
        ]);
    }

    public function test_customer_creation_validates_unique_email(): void
    {
        $agent = User::factory()->agent()->create();
        Customer::factory()->create(['email' => 'duplicate@domain.com']);

        $response = $this->actingAs($agent)->post('/customers', [
            'name' => 'Another Customer',
            'email' => 'duplicate@domain.com',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
