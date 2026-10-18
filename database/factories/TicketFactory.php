<?php

namespace Database\Factories;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'ticket_number' => null, // Generated automatically in booted()
            'title' => fake()->sentence(5),
            'description' => fake()->paragraphs(2, true),
            'customer_id' => Customer::factory(),
            'assigned_to_user_id' => User::factory()->agent(),
            'created_by_user_id' => User::factory()->admin(),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'status' => TicketStatus::Open,
            'category' => fake()->randomElement(TicketCategory::cases()),
            'due_at' => now()->addDays(2),
            'resolved_at' => null,
            'closed_at' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Open,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::InProgress,
            'resolved_at' => null,
            'closed_at' => null,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Resolved,
            'resolved_at' => now()->subHours(rand(1, 12)),
            'closed_at' => null,
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Closed,
            'resolved_at' => now()->subDays(2),
            'closed_at' => now()->subDay(),
        ]);
    }

    public function urgent(): static
    {
        return $this->state(fn () => [
            'priority' => TicketPriority::Urgent,
            'due_at' => now()->addHours(4),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Open,
            'due_at' => now()->subHours(12),
        ]);
    }

    public function unassigned(): static
    {
        return $this->state(fn () => [
            'assigned_to_user_id' => null,
        ]);
    }
}
