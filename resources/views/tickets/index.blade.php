@extends('layouts.app')

@section('title', 'Tickets')
@section('breadcrumb', 'Tickets')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Support Tickets</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage customer inquiries, assignments, and ticket lifecycles</p>
        </div>
        <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <span>Create Ticket</span>
        </a>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
        <form method="GET" action="{{ route('tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Search</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="ID, subject, customer..."
                    class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">All Statuses</option>
                    @foreach(\App\Enums\TicketStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ ($filters['status'] ?? '') === $status->value ? 'selected' : '' }}>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Priority</label>
                <select name="priority" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">All Priorities</option>
                    @foreach(\App\Enums\TicketPriority::cases() as $priority)
                        <option value="{{ $priority->value }}" {{ ($filters['priority'] ?? '') === $priority->value ? 'selected' : '' }}>
                            {{ $priority->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Category</label>
                <select name="category" class="w-full px-3 py-1.5 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">All Categories</option>
                    @foreach(\App\Enums\TicketCategory::cases() as $cat)
                        <option value="{{ $cat->value }}" {{ ($filters['category'] ?? '') === $cat->value ? 'selected' : '' }}>
                            {{ $cat->value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-1.5 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition-colors">
                    Filter
                </button>
                <a href="{{ route('tickets.index') }}" class="py-1.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-lg transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tickets Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/75 border-b border-slate-200/80 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Ticket ID</th>
                        <th class="py-3 px-4 font-semibold">Subject</th>
                        <th class="py-3 px-4 font-semibold">Customer</th>
                        <th class="py-3 px-4 font-semibold">Priority</th>
                        <th class="py-3 px-4 font-semibold">Status</th>
                        <th class="py-3 px-4 font-semibold">Assigned To</th>
                        <th class="py-3 px-4 font-semibold">SLA Due</th>
                        <th class="py-3 px-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <!-- Ticket ID -->
                            <td class="py-3.5 px-4 font-mono font-semibold text-indigo-600">
                                <a href="{{ route('tickets.show', $ticket) }}" class="hover:underline">
                                    {{ $ticket->ticket_number }}
                                </a>
                            </td>

                            <!-- Subject & Category -->
                            <td class="py-3.5 px-4 max-w-xs">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-slate-900 hover:text-indigo-600 line-clamp-1">
                                    {{ $ticket->title }}
                                </a>
                                <span class="text-[10px] text-slate-600 font-medium">{{ $ticket->category->value }}</span>
                            </td>

                            <!-- Customer -->
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-900">{{ $ticket->customer?->name ?? 'N/A' }}</div>
                                <div class="text-[10px] text-slate-600">{{ $ticket->customer?->company_name ?? 'Individual' }}</div>
                            </td>

                            <!-- Priority -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $ticket->priority->badgeClasses() }}">
                                    {{ $ticket->priority->label() }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $ticket->status->badgeClasses() }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>

                            <!-- Assigned To -->
                            <td class="py-3.5 px-4">
                                @if($ticket->assignedAgent)
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-5 h-5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold flex items-center justify-center">
                                            {{ $ticket->assignedAgent->initials() }}
                                        </div>
                                        <span class="text-slate-800 font-medium">{{ $ticket->assignedAgent->name }}</span>
                                    </div>
                                @else
                                    <span class="text-slate-600 italic">Unassigned</span>
                                @endif
                            </td>

                            <!-- SLA Due -->
                            <td class="py-3.5 px-4">
                                @if($ticket->isOverdue())
                                    <span class="text-rose-600 font-semibold flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Overdue
                                    </span>
                                @elseif($ticket->due_at)
                                    <span class="text-slate-600">{{ $ticket->due_at->diffForHumans() }}</span>
                                @else
                                    <span class="text-slate-600">—</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="p-1 text-slate-500 hover:text-indigo-600" title="View Ticket">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @can('update', $ticket)
                                    <a href="{{ route('tickets.edit', $ticket) }}" class="p-1 text-slate-500 hover:text-indigo-600" title="Edit Ticket">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endcan

                                    @can('delete', $ticket)
                                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Permanently delete ticket {{ $ticket->ticket_number }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-500 hover:text-rose-600" title="Delete Ticket">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-600">
                                <svg class="w-8 h-8 mx-auto text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span>No tickets found matching your query.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
