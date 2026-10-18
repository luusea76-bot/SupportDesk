<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_dashboard_renders_accurate_metrics(): void
    {
        $admin = User::factory()->admin()->create();

        // 2 open tickets
        Ticket::factory()->count(2)->open()->create();

        // 1 in-progress ticket
        Ticket::factory()->inProgress()->create();

        // 1 resolved ticket
        Ticket::factory()->resolved()->create();

        // 1 overdue ticket
        Ticket::factory()->overdue()->create();

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Overview of your support operations');
        $response->assertSee('My Queue');
        $response->assertSee('Activity Feed');
    }
}
