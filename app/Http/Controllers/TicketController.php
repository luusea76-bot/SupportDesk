<?php

namespace App\Http\Controllers;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Customer;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

class TicketController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $user = Auth::user();
        $query = Ticket::query()->with(['customer', 'assignedAgent', 'creator']);

        // Scope by user role
        if ($user->isCustomer()) {
            $customerId = $user->customer?->id;
            $query->where(function ($q) use ($customerId, $user) {
                $q->where('customer_id', $customerId)
                    ->orWhere('created_by_user_id', $user->id);
            });
        }

        // Apply filters
        $filters = $request->only(['search', 'status', 'priority', 'category', 'assigned_to']);
        $query->filter($filters);

        // Sorting
        $sort = $request->input('sort', 'board');
        match ($sort) {
            'latest' => $query->latest(),
            'oldest' => $query->oldest(),
            'priority' => $query->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 ELSE 4 END"),
            'due' => $query->orderBy('due_at', 'asc'),
            default => $query->orderByRaw("CASE status WHEN 'closed' THEN 1 WHEN 'open' THEN 2 WHEN 'in_progress' THEN 3 WHEN 'resolved' THEN 4 ELSE 5 END, id ASC"),
        };

        // Top 4 Metric counts
        $statsQuery = Ticket::query();
        if ($user->isCustomer()) {
            $customerId = $user->customer?->id;
            $statsQuery->where(function ($q) use ($customerId, $user) {
                $q->where('customer_id', $customerId)
                    ->orWhere('created_by_user_id', $user->id);
            });
        }

        $totalTicketsCount = (clone $statsQuery)->count();
        $openCount = (clone $statsQuery)->where('status', TicketStatus::Open)->count();
        $pendingCount = (clone $statsQuery)->where('status', TicketStatus::InProgress)->count();
        $resolvedCount = (clone $statsQuery)->where('status', TicketStatus::Resolved)->count();

        $metrics = [
            'total' => [
                'label' => 'TOTAL TICKETS',
                'count' => $totalTicketsCount,
                'trend' => '↗ 3 (17.6%) vs last week',
                'color' => 'text-rose-500',
                'icon' => 'tickets',
            ],
            'open' => [
                'label' => 'OPEN',
                'count' => $openCount,
                'trend' => '↘ 2 (20%) vs last week',
                'color' => 'text-emerald-500',
                'icon' => 'open',
            ],
            'pending' => [
                'label' => 'PENDING',
                'count' => $pendingCount,
                'trend' => '↘ 1 (16.7%) vs last week',
                'color' => 'text-emerald-500',
                'icon' => 'pending',
            ],
            'resolved' => [
                'label' => 'RESOLVED',
                'count' => $resolvedCount,
                'trend' => '↗ 2 (100%) vs last week',
                'color' => 'text-emerald-500',
                'icon' => 'resolved',
            ],
        ];

        $tickets = $query->paginate(20)->withQueryString();

        $agents = $user->isStaff()
            ? User::whereIn('role', [UserRole::Admin, UserRole::Agent])->where('is_active', true)->get()
            : collect();

        return view('tickets.index', compact(
            'tickets',
            'filters',
            'agents',
            'sort',
            'totalTicketsCount',
            'openCount',
            'pendingCount',
            'resolvedCount',
            'metrics'
        ));
    }

    public function export(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::with(['customer', 'assignedAgent'])->latest()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tickets-export-'.now()->format('Y-m-d').'.csv"',
        ];

        $callback = function () use ($tickets) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Ticket ID', 'Subject', 'Status', 'Priority', 'Category', 'Customer', 'Assignee', 'Channel', 'Created At']);

            foreach ($tickets as $t) {
                fputcsv($file, [
                    $t->ticket_number,
                    $t->title,
                    $t->status->label(),
                    $t->priority->label(),
                    $t->category->value,
                    $t->customer?->name ?? 'N/A',
                    $t->assignedAgent?->name ?? 'Unassigned',
                    ucfirst($t->channel ?? 'email'),
                    $t->created_at->toDateTimeString(),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        $user = Auth::user();
        $customers = $user->isStaff() ? Customer::orderBy('name')->get() : collect();
        $agents = $user->isStaff()
            ? User::whereIn('role', [UserRole::Admin, UserRole::Agent])->where('is_active', true)->get()
            : collect();

        return view('tickets.create', compact('customers', 'agents'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        $ticket = DB::transaction(function () use ($user, $validated) {
            $customerId = $validated['customer_id'] ?? null;

            // If customer user created the ticket, ensure linked customer profile
            if ($user->isCustomer()) {
                $customer = $user->customer ?? Customer::firstOrCreate(
                    ['email' => $user->email],
                    ['name' => $user->name, 'user_id' => $user->id]
                );
                $customerId = $customer->id;
            }

            $ticket = Ticket::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'customer_id' => $customerId,
                'created_by_user_id' => $user->id,
                'assigned_to_user_id' => $validated['assigned_to_user_id'] ?? null,
                'priority' => $validated['priority'],
                'category' => $validated['category'],
                'status' => TicketStatus::Open,
            ]);

            TicketActivity::log(
                $ticket,
                'created',
                "Ticket #{$ticket->ticket_number} created by {$user->name}",
                ['priority' => $ticket->priority->value, 'category' => $ticket->category->value],
                $user->id
            );

            if ($ticket->assigned_to_user_id) {
                TicketActivity::log(
                    $ticket,
                    'assigned',
                    "Assigned to {$ticket->assignedAgent->name}",
                    ['assigned_to' => $ticket->assignedAgent->name],
                    $user->id
                );
            }

            return $ticket;
        });

        return redirect()->route('tickets.show', $ticket)
            ->with('success', "Ticket {$ticket->ticket_number} created successfully.");
    }

    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $user = Auth::user();
        $ticket->load(['customer', 'assignedAgent', 'creator', 'activities.user']);

        // Load comments: customers can only view public comments
        $commentsQuery = $ticket->comments()->with('user');
        if ($user->isCustomer()) {
            $commentsQuery->publicOnly();
        }
        $comments = $commentsQuery->get();

        $agents = $user->isStaff()
            ? User::whereIn('role', [UserRole::Admin, UserRole::Agent])->where('is_active', true)->get()
            : collect();

        return view('tickets.show', compact('ticket', 'comments', 'agents'));
    }

    public function edit(Ticket $ticket): View
    {
        $this->authorize('update', $ticket);

        $user = Auth::user();
        $customers = $user->isStaff() ? Customer::orderBy('name')->get() : collect();
        $agents = $user->isStaff()
            ? User::whereIn('role', [UserRole::Admin, UserRole::Agent])->where('is_active', true)->get()
            : collect();

        return view('tickets.edit', compact('ticket', 'customers', 'agents'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        DB::transaction(function () use ($user, $validated, $ticket) {
            $oldStatus = $ticket->status;
            $oldPriority = $ticket->priority;
            $oldAssignedId = $ticket->assigned_to_user_id;

            $newStatus = isset($validated['status']) ? TicketStatus::from($validated['status']) : $oldStatus;

            // Validate status transition
            if ($newStatus !== $oldStatus && ! $oldStatus->canTransitionTo($newStatus)) {
                abort(422, "Cannot transition ticket from {$oldStatus->label()} to {$newStatus->label()}.");
            }

            $updateData = [
                'title' => $validated['title'],
                'description' => $validated['description'],
            ];

            if ($user->isStaff()) {
                $updateData['customer_id'] = $validated['customer_id'];
                $updateData['assigned_to_user_id'] = $validated['assigned_to_user_id'] ?? null;
                $updateData['priority'] = $validated['priority'];
                $updateData['category'] = $validated['category'];
                $updateData['status'] = $newStatus;

                if ($newStatus === TicketStatus::Resolved && $oldStatus !== TicketStatus::Resolved) {
                    $updateData['resolved_at'] = now();
                } elseif ($newStatus !== TicketStatus::Resolved && $newStatus !== TicketStatus::Closed) {
                    $updateData['resolved_at'] = null;
                }

                if ($newStatus === TicketStatus::Closed && $oldStatus !== TicketStatus::Closed) {
                    $updateData['closed_at'] = now();
                }
            }

            $ticket->update($updateData);

            // Audit log changes
            if ($newStatus !== $oldStatus) {
                TicketActivity::log(
                    $ticket,
                    'status_changed',
                    "Status changed from {$oldStatus->label()} to {$newStatus->label()}",
                    ['before' => $oldStatus->value, 'after' => $newStatus->value],
                    $user->id
                );
            }

            if ($user->isStaff() && $oldPriority->value !== $validated['priority']) {
                $newPriority = TicketPriority::from($validated['priority']);
                TicketActivity::log(
                    $ticket,
                    'priority_changed',
                    "Priority changed from {$oldPriority->label()} to {$newPriority->label()}",
                    ['before' => $oldPriority->value, 'after' => $newPriority->value],
                    $user->id
                );
            }

            if ($user->isStaff() && ($validated['assigned_to_user_id'] ?? null) != $oldAssignedId) {
                $ticket->load('assignedAgent');
                $agentName = $ticket->assignedAgent ? $ticket->assignedAgent->name : 'Unassigned';
                TicketActivity::log(
                    $ticket,
                    'assigned',
                    "Assigned to {$agentName}",
                    ['assigned_to' => $agentName],
                    $user->id
                );
            }
        });

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket updated successfully.');
    }

    public function assign(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('assign', $ticket);

        $validated = $request->validate([
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $agentId = $validated['assigned_to_user_id'] ?? null;
        $ticket->assigned_to_user_id = $agentId;
        $ticket->save();

        $agentName = $ticket->assignedAgent ? $ticket->assignedAgent->name : 'Unassigned';
        TicketActivity::log(
            $ticket,
            'assigned',
            "Reassigned to {$agentName} by ".Auth::user()->name,
            ['assigned_to' => $agentName],
            Auth::id()
        );

        return back()->with('success', "Ticket successfully assigned to {$agentName}.");
    }

    public function changeStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('changeStatus', $ticket);

        $validated = $request->validate([
            'status' => ['required', new Enum(TicketStatus::class)],
        ]);

        $newStatus = TicketStatus::from($validated['status']);
        $oldStatus = $ticket->status;

        if (! $oldStatus->canTransitionTo($newStatus)) {
            return back()->withErrors(['status' => "Cannot transition from {$oldStatus->label()} to {$newStatus->label()}."]);
        }

        $ticket->status = $newStatus;
        if ($newStatus === TicketStatus::Resolved) {
            $ticket->resolved_at = now();
        } elseif ($newStatus === TicketStatus::Closed) {
            $ticket->closed_at = now();
        } elseif ($newStatus === TicketStatus::Open || $newStatus === TicketStatus::InProgress) {
            $ticket->resolved_at = null;
            $ticket->closed_at = null;
        }
        $ticket->save();

        TicketActivity::log(
            $ticket,
            'status_changed',
            "Status changed from {$oldStatus->label()} to {$newStatus->label()} by ".Auth::user()->name,
            ['before' => $oldStatus->value, 'after' => $newStatus->value],
            Auth::id()
        );

        return back()->with('success', "Ticket status updated to {$newStatus->label()}.");
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $number = $ticket->ticket_number;
        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', "Ticket {$number} has been deleted.");
    }
}
