<?php

namespace Database\Seeders;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Core Users for testing and demonstration
        $sarah = User::firstOrCreate(
            ['email' => 'sarah@helpdesk.com'],
            [
                'name' => 'Sarah Chen',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'phone' => '+1 (555) 019-2834',
                'is_active' => true,
            ]
        );

        $alex = User::firstOrCreate(
            ['email' => 'alex@supportdesk.test'],
            [
                'name' => 'Alex Miller',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 014-9821',
                'is_active' => true,
            ]
        );

        $maya = User::firstOrCreate(
            ['email' => 'maya@supportdesk.test'],
            [
                'name' => 'Maya Singh',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 012-3498',
                'is_active' => true,
            ]
        );

        $customerUser = User::firstOrCreate(
            ['email' => 'daniel@acme.com'],
            [
                'name' => 'Daniel Okonkwo',
                'password' => Hash::make('password'),
                'role' => UserRole::Customer,
                'phone' => '+1 (555) 018-7711',
                'is_active' => true,
            ]
        );

        // 2. Customers
        $c1 = Customer::firstOrCreate(
            ['email' => 'daniel@acme.com'],
            [
                'user_id' => $customerUser->id,
                'name' => 'Daniel Okonkwo',
                'phone' => '+1 (555) 018-7711',
                'company_name' => 'Acme Corporation',
                'notes' => 'Enterprise SLA Tier 1 account',
            ]
        );

        $c2 = Customer::firstOrCreate(
            ['email' => 'olivia@techflow.io'],
            [
                'name' => 'Olivia Martinez',
                'phone' => '+1 (555) 017-4422',
                'company_name' => 'TechFlow Inc',
                'notes' => 'SaaS subscription - Growth tier',
            ]
        );

        $c3 = Customer::firstOrCreate(
            ['email' => 'chris@cloudscale.net'],
            [
                'name' => 'Chris Taylor',
                'phone' => '+1 (555) 013-8833',
                'company_name' => 'CloudScale Systems',
                'notes' => 'Dedicated infrastructure customer',
            ]
        );

        $c4 = Customer::firstOrCreate(
            ['email' => 'sophia@apexglobal.org'],
            [
                'name' => 'Sophia Patel',
                'phone' => '+1 (555) 016-5599',
                'company_name' => 'Apex Global',
                'notes' => 'Standard business support',
            ]
        );

        // 3. Realistic Demo Tickets
        $ticketsData = [
            [
                'title' => 'API rate limiting errors in production',
                'description' => 'Our production backend is receiving unexpected 429 Too Many Requests responses even though our request rate is well below the 10,000 req/min enterprise quota.',
                'customer_id' => $c1->id,
                'assigned_to_user_id' => $sarah->id,
                'created_by_user_id' => $customerUser->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Integration,
                'due_at' => now()->subHours(6), // Overdue
                'created_at' => now()->subHours(18),
            ],
            [
                'title' => 'SLA breach notification not triggering',
                'description' => 'The automated webhook for SLA breach warnings failed to send notifications for high priority ticket queues.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $alex->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Technical,
                'due_at' => now()->subHours(2), // Overdue
                'created_at' => now()->subHours(20),
            ],
            [
                'title' => 'Two-factor authentication lockout',
                'description' => 'Lead DevOps engineer lost access to their authenticator device and backup recovery codes are not accepted by the portal.',
                'customer_id' => $c1->id,
                'assigned_to_user_id' => $maya->id,
                'created_by_user_id' => $customerUser->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Account,
                'due_at' => now()->subHour(), // Overdue
                'created_at' => now()->subHours(21),
            ],
            [
                'title' => 'Invoice discrepancy on enterprise plan',
                'description' => 'Annual billing statement shows charge for 150 seats, but our active contracted seat count was downgraded to 120 in Q3.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => $sarah->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Billing,
                'due_at' => now()->addHours(24),
                'created_at' => now()->subHours(5),
            ],
            [
                'title' => 'Mobile app crashing on Android 15',
                'description' => 'Users report an immediate crash on app launch after updating their Samsung Galaxy devices to Android 15 release build.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => null, // Unassigned
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Technical,
                'due_at' => now()->addHours(12),
                'created_at' => now()->subHours(18),
            ],
            [
                'title' => 'Webhook delivery failures to custom endpoint',
                'description' => 'Payloads sent to https://api.cloudscale.net/webhooks/tickets are returning 504 Gateway Timeout from intermediary proxy.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $maya->id,
                'created_by_user_id' => $alex->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Integration,
                'due_at' => now()->addHours(18),
                'created_at' => now()->subHours(19),
            ],
            [
                'title' => 'SSO configuration assistance with Okta SAML 2.0',
                'description' => 'Need help verifying identity provider metadata XML and mapping custom user attributes for single sign-on.',
                'customer_id' => $c4->id,
                'assigned_to_user_id' => $alex->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Resolved,
                'category' => TicketCategory::Account,
                'due_at' => now()->addHours(30),
                'resolved_at' => now()->subHours(4),
                'created_at' => now()->subDays(2),
            ],
            [
                'title' => 'Custom domain SSL certificate renewal assistance',
                'description' => 'Certificate for portal.apexglobal.org will expire in 7 days; requested automated Let\'s Encrypt verification.',
                'customer_id' => $c4->id,
                'assigned_to_user_id' => $maya->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Closed,
                'category' => TicketCategory::Technical,
                'due_at' => now()->addDays(3),
                'resolved_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
                'created_at' => now()->subDays(4),
            ],
        ];

        foreach ($ticketsData as $data) {
            $ticket = Ticket::create($data);

            // Add activities and comments
            TicketActivity::log(
                $ticket,
                'created',
                "Ticket {$ticket->ticket_number} created by ".($ticket->creator ? $ticket->creator->name : 'System'),
                ['priority' => $ticket->priority->value, 'status' => $ticket->status->value],
                $ticket->created_by_user_id
            );

            if ($ticket->assigned_to_user_id) {
                TicketActivity::log(
                    $ticket,
                    'assigned',
                    "Assigned to {$ticket->assignedAgent->name}",
                    ['assigned_to' => $ticket->assignedAgent->name],
                    $sarah->id
                );
            }

            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $ticket->created_by_user_id,
                'body' => 'Customer initial inquiry recorded. Diagnostics logs attached to internal queue.',
                'is_internal' => false,
                'created_at' => $ticket->created_at->addMinutes(15),
            ]);

            if ($ticket->status === TicketStatus::InProgress || $ticket->status === TicketStatus::Resolved) {
                TicketComment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $sarah->id,
                    'body' => 'Investigating gateway metrics and replicating issue on staging cluster.',
                    'is_internal' => true,
                    'created_at' => $ticket->created_at->addMinutes(45),
                ]);
            }
        }
    }
}
