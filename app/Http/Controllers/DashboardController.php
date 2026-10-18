<?php

namespace App\Http\Controllers;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Base query scoped by user role
        $ticketQuery = Ticket::query();
        if ($user->isCustomer()) {
            $customerId = $user->customer?->id;
            $ticketQuery->where(function ($q) use ($customerId, $user) {
                $q->where('customer_id', $customerId)
                    ->orWhere('created_by_user_id', $user->id);
            });
        }

        // Metrics calculations
        $totalTickets = (clone $ticketQuery)->count();
        $openTickets = (clone $ticketQuery)->where('status', TicketStatus::Open)->count();
        $inProgressTickets = (clone $ticketQuery)->where('status', TicketStatus::InProgress)->count();
        $resolvedTickets = (clone $ticketQuery)->where('status', TicketStatus::Resolved)->count();
        $closedTickets = (clone $ticketQuery)->where('status', TicketStatus::Closed)->count();
        $unassignedTickets = (clone $ticketQuery)->whereNull('assigned_to_user_id')
            ->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
            ->count();

        // Accurate Overdue count: active tickets where due_at is in the past
        $overdueTickets = (clone $ticketQuery)
            ->whereNotNull('due_at')
            ->where('due_at', '<', now())
            ->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
            ->count();

        // Accurate Average Resolution Time (in hours)
        $isMysql = DB::connection()->getDriverName() === 'mysql';
        if ($isMysql) {
            $avgResolutionHours = (clone $ticketQuery)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) / 60 as avg_hours')
                ->value('avg_hours');
        } else {
            // SQLite compatible
            $avgResolutionHours = (clone $ticketQuery)
                ->whereNotNull('resolved_at')
                ->selectRaw('AVG(strftime("%s", resolved_at) - strftime("%s", created_at)) / 3600 as avg_hours')
                ->value('avg_hours');
        }

        $avgResolutionTimeDisplay = $avgResolutionHours !== null
            ? ($avgResolutionHours < 24 ? round($avgResolutionHours, 1).'h' : round($avgResolutionHours / 24, 1).'d')
            : 'N/A';

        // SLA Compliance rate: percentage of resolved tickets resolved before due_at
        $resolvedCount = (clone $ticketQuery)->whereNotNull('resolved_at')->count();
        $resolvedOnTimeCount = (clone $ticketQuery)->whereNotNull('resolved_at')
            ->whereColumn('resolved_at', '<=', 'due_at')
            ->count();

        $slaComplianceRate = $resolvedCount > 0
            ? round(($resolvedOnTimeCount / $resolvedCount) * 100, 1)
            : 100.0;

        // My Queue (Tickets assigned to current user, or tickets created by customer)
        $myQueueQuery = Ticket::query()->with(['customer', 'assignedAgent']);
        if ($user->isCustomer()) {
            $myQueueQuery->where(function ($q) use ($user) {
                $q->where('customer_id', $user->customer?->id)
                    ->orWhere('created_by_user_id', $user->id);
            });
        } else {
            $myQueueQuery->where('assigned_to_user_id', $user->id);
        }
        $now = now()->toDateTimeString();
        $myQueue = $myQueueQuery
            ->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress])
            ->orderByRaw('CASE WHEN due_at < ? THEN 0 ELSE 1 END', [$now])
            ->orderBy('due_at', 'asc')
            ->take(6)
            ->get();

        // Activity Feed (Recent audit entries)
        $activityFeed = TicketActivity::with(['user', 'ticket'])
            ->latest('created_at')
            ->take(8)
            ->get();

        // Tickets by Category (for Donut Chart)
        $categoryBreakdown = [];
        foreach (TicketCategory::cases() as $category) {
            $categoryBreakdown[$category->value] = (clone $ticketQuery)
                ->where('category', $category)
                ->count();
        }

        // Tickets by Priority
        $priorityBreakdown = [];
        foreach (TicketPriority::cases() as $priority) {
            $priorityBreakdown[$priority->value] = (clone $ticketQuery)
                ->where('priority', $priority)
                ->count();
        }

        // Agent Workload (Active tickets per agent)
        $agentWorkload = [];
        if ($user->isStaff()) {
            $agentWorkload = User::whereIn('role', [UserRole::Admin, UserRole::Agent])
                ->where('is_active', true)
                ->withCount(['assignedTickets' => function ($q) {
                    $q->whereIn('status', [TicketStatus::Open, TicketStatus::InProgress]);
                }])
                ->get();
        }

        // Recent Customers
        $recentCustomers = Customer::withCount('tickets')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact(
            'totalTickets',
            'openTickets',
            'inProgressTickets',
            'resolvedTickets',
            'closedTickets',
            'unassignedTickets',
            'overdueTickets',
            'avgResolutionTimeDisplay',
            'slaComplianceRate',
            'myQueue',
            'activityFeed',
            'categoryBreakdown',
            'priorityBreakdown',
            'agentWorkload',
            'recentCustomers'
        ));
    }
}
