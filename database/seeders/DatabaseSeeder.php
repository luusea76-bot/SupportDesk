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
        // 1. Staff Users
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

        $jason = User::firstOrCreate(
            ['email' => 'jason@supportdesk.test'],
            [
                'name' => 'Jason Duong',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 012-7811',
                'is_active' => true,
            ]
        );

        $lam = User::firstOrCreate(
            ['email' => 'lam@supportdesk.test'],
            [
                'name' => 'Lam Tran',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 013-4422',
                'is_active' => true,
            ]
        );

        $nhi = User::firstOrCreate(
            ['email' => 'nhi@supportdesk.test'],
            [
                'name' => 'Nhi Pham',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 014-9901',
                'is_active' => true,
            ]
        );

        $thanh = User::firstOrCreate(
            ['email' => 'thanh@supportdesk.test'],
            [
                'name' => 'Thanh Le',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 015-8832',
                'is_active' => true,
            ]
        );

        $annie = User::firstOrCreate(
            ['email' => 'annie@supportdesk.test'],
            [
                'name' => 'Annie Nguyen',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 016-3391',
                'is_active' => true,
            ]
        );

        $bao = User::firstOrCreate(
            ['email' => 'bao@supportdesk.test'],
            [
                'name' => 'Bao Truong',
                'password' => Hash::make('password'),
                'role' => UserRole::Agent,
                'phone' => '+1 (555) 017-6623',
                'is_active' => true,
            ]
        );

        // 2. Customer Users
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

        // 3. Customers
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

        // 4. Exact 20 Tickets from the Screenshot
        // To ensure ticket numbers match #-001 through #-020 cleanly, clear existing demo tickets
        Ticket::query()->delete();

        $ticketsData = [
            // Row 1: #-007 (Closed)
            [
                'ticket_number' => 'SD-2026-000007',
                'title' => 'Deactivate unused seats without closing the acco...',
                'description' => 'Need help adjusting our active seat licensing count for the upcoming billing cycle.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => $lam->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Closed,
                'category' => TicketCategory::Other,
                'channel' => 'chat',
                'due_at' => now()->subDays(5),
                'resolved_at' => now()->subDays(4),
                'closed_at' => now()->subDays(3),
                'created_at' => now()->subDays(6),
            ],
            // Row 2: #-017 (Closed)
            [
                'ticket_number' => 'SD-2026-000017',
                'title' => 'Help center article link broken in onboarding email',
                'description' => 'The getting started hyperlink in the automated welcome email returns 404.',
                'customer_id' => $c4->id,
                'assigned_to_user_id' => $nhi->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Closed,
                'category' => TicketCategory::Other,
                'channel' => 'chat',
                'due_at' => now()->subDays(4),
                'resolved_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
                'created_at' => now()->subDays(5),
            ],
            // Row 3: #-020 (Closed)
            [
                'ticket_number' => 'SD-2026-000020',
                'title' => 'Finance asks for separate VAT line per region',
                'description' => 'EU billing compliance requires separate breakdown of VAT per destination country.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => $thanh->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Closed,
                'category' => TicketCategory::Billing,
                'channel' => 'email',
                'due_at' => now()->subDays(3),
                'resolved_at' => now()->subDays(2),
                'closed_at' => now()->subDay(),
                'created_at' => now()->subDays(4),
            ],
            // Row 4: #-001 (Open)
            [
                'ticket_number' => 'SD-2026-000001',
                'title' => 'SSO SAML configuration failing on IdP callback',
                'description' => 'SAML response assertions from corporate Okta tenant fail signature verification.',
                'customer_id' => $c1->id,
                'assigned_to_user_id' => $jason->id,
                'created_by_user_id' => $customerUser->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Technical,
                'channel' => 'email',
                'due_at' => now()->addHours(6), // Warning
                'created_at' => now()->subHours(12),
            ],
            // Row 5: #-002 (Open)
            [
                'ticket_number' => 'SD-2026-000002',
                'title' => 'Webhook delivery delays to our Salesforce endpoi...',
                'description' => 'Events are queuing up and arriving with 20-30 minute delays during peak ingestion hours.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $jason->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Technical,
                'channel' => 'slack',
                'due_at' => now()->addHours(24), // On Track
                'created_at' => now()->subHours(2),
            ],
            // Row 6: #-004 (Open)
            [
                'ticket_number' => 'SD-2026-000004',
                'title' => 'Rate limiting our batch export API calls unexpecte...',
                'description' => 'Our nightly ETL process is hitting HTTP 429 errors at 3 AM UTC.',
                'customer_id' => $c1->id,
                'assigned_to_user_id' => $annie->id,
                'created_by_user_id' => $customerUser->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Technical,
                'channel' => 'email',
                'due_at' => now()->addHours(36), // On Track
                'created_at' => now()->subHours(4),
            ],
            // Row 7: #-005 (Open)
            [
                'ticket_number' => 'SD-2026-000005',
                'title' => 'Need proration breakdown for yearly invoice',
                'description' => 'Please provide the line-item credit breakdown for our mid-term enterprise plan upgrade.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => null,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Billing,
                'channel' => 'chat',
                'due_at' => now()->addHours(8), // Warning
                'created_at' => now()->subHours(6),
            ],
            // Row 8: #-008 (Open)
            [
                'ticket_number' => 'SD-2026-000008',
                'title' => 'Customer cannot regenerate API key after role up...',
                'description' => 'Security permissions modal freezes when clicking Generate New Secret Key.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => null,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Access,
                'channel' => 'chat',
                'due_at' => now()->addHours(3), // Warning
                'created_at' => now()->subHours(1),
            ],
            // Row 9: #-012 (Open)
            [
                'ticket_number' => 'SD-2026-000012',
                'title' => 'Mobile app push notifications missing after latest ...',
                'description' => 'FCM push notifications are not received on iOS 18 devices.',
                'customer_id' => $c4->id,
                'assigned_to_user_id' => $jason->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Technical,
                'channel' => 'slack',
                'due_at' => now()->subHours(2), // Breached
                'created_at' => now()->subHours(14),
            ],
            // Row 10: #-014 (Open)
            [
                'ticket_number' => 'SD-2026-000014',
                'title' => 'SLA breach report export has timezone mismatch',
                'description' => 'CSV exports calculate resolution timestamp in UTC instead of company account timezone.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $bao->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Technical,
                'channel' => 'email',
                'due_at' => now()->addHours(5), // Warning
                'created_at' => now()->subHours(10),
            ],
            // Row 11: #-016 (Open)
            [
                'ticket_number' => 'SD-2026-000016',
                'title' => 'Cannot verify ownership after domain migration',
                'description' => 'DNS TXT records verified on Cloudflare but custom domain status remains unverified.',
                'customer_id' => $c1->id,
                'assigned_to_user_id' => null,
                'created_by_user_id' => $customerUser->id,
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::Open,
                'category' => TicketCategory::Access,
                'channel' => 'slack',
                'due_at' => now()->subHours(1), // Breached
                'created_at' => now()->subHours(8),
            ],
            // Row 12: #-003 (Pending)
            [
                'ticket_number' => 'SD-2026-000003',
                'title' => 'Can we get custom roles and permissions on Gro...',
                'description' => 'We need granular read-only access for external audit team members.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $jason->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Access,
                'channel' => 'email',
                'due_at' => now()->subHours(3), // Breached
                'created_at' => now()->subHours(15),
            ],
            // Row 13: #-009 (Pending)
            [
                'ticket_number' => 'SD-2026-000009',
                'title' => 'Bulk import failed with UTF-8 CSV names',
                'description' => 'Non-ASCII characters in customer user names caused row parsing failure.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => $nhi->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Technical,
                'channel' => 'email',
                'due_at' => now()->addHours(10), // Warning
                'created_at' => now()->subHours(12),
            ],
            // Row 14: #-011 (Pending)
            [
                'ticket_number' => 'SD-2026-000011',
                'title' => 'Ask for invoice by legal entity split',
                'description' => 'Please provide multi-subsidiary billing breakdowns on current quarter invoices.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => $thanh->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Billing,
                'channel' => 'chat',
                'due_at' => now()->addHours(7), // Warning
                'created_at' => now()->subHours(5),
            ],
            // Row 15: #-013 (Pending)
            [
                'ticket_number' => 'SD-2026-000013',
                'title' => 'Custom webhook headers not passed in test payload',
                'description' => 'Authorization header missing when testing via dashboard test runner.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $annie->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Technical,
                'channel' => 'slack',
                'due_at' => now()->addHours(22), // On Track
                'created_at' => now()->subHours(7),
            ],
            // Row 16: #-015 (Pending)
            [
                'ticket_number' => 'SD-2026-000015',
                'title' => 'Request sandbox account credentials reset for testing',
                'description' => 'Need password reset on staging tenant sandbox for QA team.',
                'customer_id' => $c4->id,
                'assigned_to_user_id' => $lam->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::InProgress,
                'category' => TicketCategory::Access,
                'channel' => 'email',
                'due_at' => now()->addHours(40), // On Track
                'created_at' => now()->subHours(3),
            ],
            // Row 17: #-006 (Resolved)
            [
                'ticket_number' => 'SD-2026-000006',
                'title' => 'SSO configuration assistance with Okta SAML 2.0',
                'description' => 'Need help verifying identity provider metadata XML and mapping custom user attributes.',
                'customer_id' => $c4->id,
                'assigned_to_user_id' => $jason->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Resolved,
                'category' => TicketCategory::Access,
                'channel' => 'email',
                'due_at' => now()->addHours(30),
                'resolved_at' => now()->subHours(4),
                'created_at' => now()->subDays(2),
            ],
            // Row 18: #-010 (Resolved)
            [
                'ticket_number' => 'SD-2026-000010',
                'title' => 'Update company billing address and tax ID for Q4',
                'description' => 'Updated address and tax ID have been successfully applied to account profile.',
                'customer_id' => $c1->id,
                'assigned_to_user_id' => $thanh->id,
                'created_by_user_id' => $customerUser->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Resolved,
                'category' => TicketCategory::Billing,
                'channel' => 'email',
                'due_at' => now()->addHours(20),
                'resolved_at' => now()->subHours(6),
                'created_at' => now()->subDays(2),
            ],
            // Row 19: #-018 (Resolved)
            [
                'ticket_number' => 'SD-2026-000018',
                'title' => 'Export audit logs failed with timeout on year-to-date range',
                'description' => 'Async batching query has been updated and test exports succeeded.',
                'customer_id' => $c3->id,
                'assigned_to_user_id' => $annie->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::High,
                'status' => TicketStatus::Resolved,
                'category' => TicketCategory::Technical,
                'channel' => 'email',
                'due_at' => now()->addHours(15),
                'resolved_at' => now()->subHours(8),
                'created_at' => now()->subDays(3),
            ],
            // Row 20: #-019 (Resolved)
            [
                'ticket_number' => 'SD-2026-000019',
                'title' => 'Clarification on API rate limits for webhook events',
                'description' => 'Shared rate limit whitepaper and added burst allocation documentation.',
                'customer_id' => $c2->id,
                'assigned_to_user_id' => $bao->id,
                'created_by_user_id' => $sarah->id,
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Resolved,
                'category' => TicketCategory::Other,
                'channel' => 'chat',
                'due_at' => now()->addHours(48),
                'resolved_at' => now()->subHours(11),
                'created_at' => now()->subDays(3),
            ],
        ];

        foreach ($ticketsData as $data) {
            $ticket = Ticket::create($data);

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
