<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketActivity>
 */
class TicketActivityFactory extends Factory
{
    protected $model = TicketActivity::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory()->agent(),
            'activity_type' => 'status_changed',
            'description' => 'Ticket status updated',
            'properties' => ['before' => 'open', 'after' => 'in_progress'],
            'created_at' => now(),
        ];
    }
}
