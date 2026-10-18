@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header Page Title -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard</h1>
        <p class="text-sm text-slate-500 mt-0.5">Overview of your support operations and queue performance</p>
    </div>

    <!-- Top KPI Metric Cards (6-card grid) -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <!-- Open Tickets -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Open Tickets</span>
                <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900">{{ $openTickets }}</div>
                <div class="flex items-center gap-1 text-[11px] font-medium text-emerald-600 mt-1">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                    <span>Active Queue</span>
                </div>
            </div>
        </div>

        <!-- Avg Resolution Time -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Avg Resolution</span>
                <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900">{{ $avgResolutionTimeDisplay }}</div>
                <div class="flex items-center gap-1 text-[11px] font-medium text-slate-500 mt-1">
                    <span>Average duration</span>
                </div>
            </div>
        </div>

        <!-- SLA Compliance -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">SLA Compliance</span>
                <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900">{{ $slaComplianceRate }}%</div>
                <div class="flex items-center gap-1 text-[11px] font-medium text-emerald-600 mt-1">
                    <span>Target: >90%</span>
                </div>
            </div>
        </div>

        <!-- Overdue Tickets -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Overdue SLA</span>
                <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold {{ $overdueTickets > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ $overdueTickets }}</div>
                <div class="flex items-center gap-1 text-[11px] font-medium {{ $overdueTickets > 0 ? 'text-rose-600' : 'text-slate-500' }} mt-1">
                    <span>{{ $overdueTickets > 0 ? 'Immediate action' : 'All on track' }}</span>
                </div>
            </div>
        </div>

        <!-- Unassigned Tickets -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Unassigned</span>
                <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900">{{ $unassignedTickets }}</div>
                <div class="flex items-center gap-1 text-[11px] font-medium text-amber-600 mt-1">
                    <span>Awaiting triage</span>
                </div>
            </div>
        </div>

        <!-- Resolved Tickets -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between hover:border-slate-300 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-500">Resolved</span>
                <div class="w-7 h-7 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold text-slate-900">{{ $resolvedTickets }}</div>
                <div class="flex items-center gap-1 text-[11px] font-medium text-teal-600 mt-1">
                    <span>Closed: {{ $closedTickets }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content 3-Column Layout (Matching Mockup) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Column 1: My Queue (5 cols) -->
        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">My Queue</h2>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">{{ $myQueue->count() }}</span>
                </div>
                <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($myQueue as $ticket)
                    <div class="relative p-3.5 rounded-xl border border-slate-100 hover:border-slate-200 hover:bg-slate-50/50 transition-all overflow-hidden group">
                        <!-- Left Urgency Indicator Bar -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 {{ $ticket->priority->barColor() }}"></div>

                        <div class="pl-2">
                            <div class="flex items-center justify-between text-xs text-slate-500 mb-1">
                                <div class="flex items-center gap-1.5 font-mono font-medium text-slate-700">
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-[10px] font-bold flex items-center justify-center text-slate-600">
                                        {{ $ticket->customer ? $ticket->customer->initials() : 'T' }}
                                    </span>
                                    <a href="{{ route('tickets.show', $ticket) }}" class="hover:text-indigo-600">{{ $ticket->ticket_number }}</a>
                                </div>
                                <span class="text-[11px] text-slate-600">{{ $ticket->customer?->company_name ?? 'Standard' }}</span>
                            </div>

                            <a href="{{ route('tickets.show', $ticket) }}" class="block font-semibold text-sm text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-1 mb-2">
                                {{ $ticket->title }}
                            </a>

                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-1.5">
                                    <!-- Priority Badge -->
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold border {{ $ticket->priority->badgeClasses() }}">
                                        {{ $ticket->priority->label() }}
                                    </span>
                                    <!-- Overdue Warning -->
                                    @if($ticket->isOverdue())
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-rose-50 text-rose-700 border border-rose-200 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Overdue
                                        </span>
                                    @endif
                                </div>

                                <span class="text-[11px] text-slate-600">
                                    Due {{ $ticket->due_at ? $ticket->due_at->diffForHumans() : 'No SLA' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-600 text-sm">
                        <svg class="w-8 h-8 mx-auto text-slate-500 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>No pending tickets in your queue!</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 2: Activity Feed (4 cols) -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-bold text-slate-900">Activity Feed</h2>
                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">{{ $activityFeed->count() }}</span>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($activityFeed as $activity)
                    <div class="flex items-start gap-3 text-xs">
                        <div class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] flex items-center justify-center shrink-0">
                            {{ $activity->user ? $activity->user->initials() : 'SYS' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-slate-800 leading-tight">
                                <span class="font-semibold text-slate-900">{{ $activity->user ? $activity->user->name : 'System' }}</span>
                                {{ $activity->description }}
                            </p>
                            @if($activity->ticket)
                                <a href="{{ route('tickets.show', $activity->ticket) }}" class="inline-block mt-0.5 text-[11px] font-mono font-medium text-indigo-600 hover:underline">
                                    {{ $activity->ticket->ticket_number }}
                                </a>
                            @endif
                            <div class="text-[10px] text-slate-600 mt-0.5">{{ $activity->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center text-slate-600 text-sm">
                        No activity recorded yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Column 3: Quick Stats (3 cols) -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Category Distribution Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h2 class="text-base font-bold text-slate-900 mb-3">Tickets by Category</h2>
                <div class="space-y-2.5">
                    @foreach($categoryBreakdown as $category => $count)
                        @php
                            $percentage = $totalTickets > 0 ? round(($count / $totalTickets) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs font-medium text-slate-700 mb-1">
                                <span>{{ $category }}</span>
                                <span class="text-slate-600">{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Priority Distribution Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h2 class="text-base font-bold text-slate-900 mb-3">Tickets by Priority</h2>
                <div class="grid grid-cols-2 gap-2 text-center">
                    @foreach($priorityBreakdown as $priority => $count)
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-xs font-medium text-slate-600 capitalize">{{ $priority }}</div>
                            <div class="text-lg font-bold text-slate-900 mt-0.5">{{ $count }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Customers Section -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-slate-900">Recent Customers</h2>
            @if(auth()->user()->isStaff())
                <a href="{{ route('customers.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Manage Customers &rarr;</a>
            @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($recentCustomers as $customer)
                <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-slate-50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                        {{ $customer->initials() }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-slate-900 truncate">{{ $customer->name }}</div>
                        <div class="text-[11px] text-slate-500 truncate">{{ $customer->company_name ?? 'Individual' }}</div>
                        <div class="text-[10px] text-indigo-600 font-semibold mt-0.5">{{ $customer->tickets_count }} tickets</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
