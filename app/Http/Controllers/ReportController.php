<?php

namespace App\Http\Controllers;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $days = (int) $request->input('period', 30);
        $since = now()->subDays($days);

        $ticketsCreated = Ticket::where('created_at', '>=', $since)->count();
        $ticketsResolved = Ticket::where('resolved_at', '>=', $since)->count();

        $isMysql = DB::connection()->getDriverName() === 'mysql';
        if ($isMysql) {
            $avgResolutionHours = Ticket::whereNotNull('resolved_at')
                ->where('created_at', '>=', $since)
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, created_at, resolved_at)) / 60 as avg_hours')
                ->value('avg_hours');
        } else {
            $avgResolutionHours = Ticket::whereNotNull('resolved_at')
                ->where('created_at', '>=', $since)
                ->selectRaw('AVG(strftime("%s", resolved_at) - strftime("%s", created_at)) / 3600 as avg_hours')
                ->value('avg_hours');
        }

        // Distribution by category
        $byCategory = [];
        foreach (TicketCategory::cases() as $category) {
            $byCategory[$category->value] = Ticket::where('created_at', '>=', $since)
                ->where('category', $category)
                ->count();
        }

        // Distribution by priority
        $byPriority = [];
        foreach (TicketPriority::cases() as $priority) {
            $byPriority[$priority->value] = Ticket::where('created_at', '>=', $since)
                ->where('priority', $priority)
                ->count();
        }

        // Distribution by status
        $byStatus = [];
        foreach (TicketStatus::cases() as $status) {
            $byStatus[$status->value] = Ticket::where('status', $status)->count();
        }

        // Agent performance
        $agents = User::whereIn('role', [UserRole::Admin, UserRole::Agent])
            ->withCount(['assignedTickets as assigned_count' => function ($q) use ($since) {
                $q->where('created_at', '>=', $since);
            }])
            ->withCount(['assignedTickets as resolved_count' => function ($q) use ($since) {
                $q->whereNotNull('resolved_at')->where('resolved_at', '>=', $since);
            }])
            ->get();

        return view('reports.index', compact(
            'days',
            'ticketsCreated',
            'ticketsResolved',
            'avgResolutionHours',
            'byCategory',
            'byPriority',
            'byStatus',
            'agents'
        ));
    }
}
