@extends('layouts.app')

@section('title', 'Tickets')
@section('breadcrumb', 'Tickets')

@section('content')
<div class="space-y-5">
    <!-- Header: Title & Action Buttons -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Tickets</h1>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('tickets.export') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg shadow-2xs transition-colors">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Export</span>
            </a>
            <a href="{{ route('tickets.create') }}" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-[#f59e0b] hover:bg-[#d97706] text-white text-xs font-bold rounded-lg shadow-2xs transition-colors">
                <span>+ New Ticket</span>
            </a>
        </div>
    </div>

    <!-- Top 4 KPI Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- TOTAL TICKETS -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <rect x="3" y="4" width="18" height="16" rx="2" stroke-width="2"/>
                    <line x1="3" y1="10" x2="21" y2="10" stroke-width="2"/>
                </svg>
                <span class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ $metrics['total']['label'] }}</span>
            </div>
            <div class="text-3xl font-bold text-slate-900 mt-2 tracking-tight">{{ $metrics['total']['count'] }}</div>
            <div class="text-xs font-medium {{ $metrics['total']['color'] }} mt-2 flex items-center gap-1">
                <span>{{ $metrics['total']['trend'] }}</span>
            </div>
        </div>

        <!-- OPEN -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2.5 2.5"/>
                </svg>
                <span class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ $metrics['open']['label'] }}</span>
            </div>
            <div class="text-3xl font-bold text-slate-900 mt-2 tracking-tight">{{ $metrics['open']['count'] }}</div>
            <div class="text-xs font-medium {{ $metrics['open']['color'] }} mt-2 flex items-center gap-1">
                <span>{{ $metrics['open']['trend'] }}</span>
            </div>
        </div>

        <!-- PENDING -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/>
                </svg>
                <span class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ $metrics['pending']['label'] }}</span>
            </div>
            <div class="text-3xl font-bold text-slate-900 mt-2 tracking-tight">{{ $metrics['pending']['count'] }}</div>
            <div class="text-xs font-medium {{ $metrics['pending']['color'] }} mt-2 flex items-center gap-1">
                <span>{{ $metrics['pending']['trend'] }}</span>
            </div>
        </div>

        <!-- RESOLVED -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs">
            <div class="flex items-center gap-1.5 text-slate-400">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ $metrics['resolved']['label'] }}</span>
            </div>
            <div class="text-3xl font-bold text-slate-900 mt-2 tracking-tight">{{ $metrics['resolved']['count'] }}</div>
            <div class="text-xs font-medium {{ $metrics['resolved']['color'] }} mt-2 flex items-center gap-1">
                <span>{{ $metrics['resolved']['trend'] }}</span>
            </div>
        </div>
    </div>

    <!-- Search & Toolbar Row -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-1">
        <!-- Search Pill -->
        <form method="GET" action="{{ route('tickets.index') }}" class="relative w-full sm:w-80 md:w-96">
            @if(!empty($filters['status']))
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
            @endif
            @if(!empty($filters['priority']))
                <input type="hidden" name="priority" value="{{ $filters['priority'] }}">
            @endif
            @if(!empty($sort))
                <input type="hidden" name="sort" value="{{ $sort }}">
            @endif
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                </svg>
            </div>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                placeholder="Search tickets by subject, customer, or ID..."
                class="w-full pl-10 pr-4 py-2 bg-white border border-slate-200/90 rounded-full text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 shadow-2xs transition-all">
        </form>

        <!-- Toolbar Buttons -->
        <div class="flex items-center gap-2 flex-wrap">
            <!-- Board order toggle -->
            <a href="{{ route('tickets.index', array_merge(request()->query(), ['sort' => ($sort === 'board' ? 'latest' : 'board')])) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-lg shadow-2xs transition-colors">
                <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
                </svg>
                <span>Board order</span>
            </a>

            <!-- Table options modal/dropdown trigger -->
            <div class="relative" id="table-options-wrapper">
                <button type="button" onclick="toggleDropdown('table-options-menu')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-lg shadow-2xs transition-colors">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                    </svg>
                    <span>Table options</span>
                </button>
                <div id="table-options-menu" class="hidden absolute right-0 mt-1.5 w-48 bg-white border border-slate-200/90 rounded-xl shadow-lg py-1.5 z-40 text-xs">
                    <div class="px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-slate-400">Sort by</div>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['sort' => 'board'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50">Board Order</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['sort' => 'latest'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50">Newest Created</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['sort' => 'oldest'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50">Oldest Created</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['sort' => 'priority'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50">Priority (Urgent first)</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['sort' => 'due'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50">SLA Due Date</a>
                </div>
            </div>

            <!-- All Statuses Dropdown -->
            <div class="relative" id="statuses-filter-wrapper">
                <button type="button" onclick="toggleDropdown('statuses-filter-menu')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200/90 hover:bg-slate-50 text-slate-700 text-xs font-medium rounded-lg shadow-2xs transition-colors">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <span>{{ empty($filters['status']) ? 'All Statuses' : ucfirst($filters['status']) }}</span>
                    <svg class="w-3 h-3 text-slate-400 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="statuses-filter-menu" class="hidden absolute right-0 mt-1.5 w-44 bg-white border border-slate-200/90 rounded-xl shadow-lg py-1.5 z-40 text-xs">
                    <a href="{{ route('tickets.index', array_merge(request()->except('status'), [])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 {{ empty($filters['status']) ? 'font-bold bg-slate-50' : '' }}">All Statuses</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['status' => 'open'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 {{ ($filters['status'] ?? '') === 'open' ? 'font-bold bg-slate-50' : '' }}">Open</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['status' => 'in_progress'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 {{ ($filters['status'] ?? '') === 'in_progress' ? 'font-bold bg-slate-50' : '' }}">Pending</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['status' => 'resolved'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 {{ ($filters['status'] ?? '') === 'resolved' ? 'font-bold bg-slate-50' : '' }}">Resolved</a>
                    <a href="{{ route('tickets.index', array_merge(request()->query(), ['status' => 'closed'])) }}" class="block px-3 py-1.5 text-slate-700 hover:bg-slate-50 {{ ($filters['status'] ?? '') === 'closed' ? 'font-bold bg-slate-50' : '' }}">Closed</a>
                </div>
            </div>

            <!-- View switchers (List / Board icons) -->
            <div class="flex items-center gap-1 border-l border-slate-200 pl-2">
                <button type="button" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors" title="List View">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>
                <button type="button" class="p-1.5 rounded-lg text-slate-700 bg-slate-100 transition-colors" title="Table View">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <rect x="3" y="3" width="7" height="7" rx="1" stroke-width="2"/>
                        <rect x="14" y="3" width="7" height="7" rx="1" stroke-width="2"/>
                        <rect x="14" y="14" width="7" height="7" rx="1" stroke-width="2"/>
                        <rect x="3" y="14" width="7" height="7" rx="1" stroke-width="2"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-white border-b border-slate-200/80 text-slate-600">
                    <tr>
                        <th class="py-3 px-3.5 w-10 text-center">
                            <input type="checkbox" id="select-all" class="w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-0 cursor-pointer">
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-500 whitespace-nowrap"># ID</th>
                        <th class="py-3 px-4 text-[11px] font-semibold text-slate-600 whitespace-nowrap min-w-[280px]">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="text-slate-400 text-[10px]">○</span>
                                <span>Subject</span>
                            </span>
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="text-slate-400 text-[10px]">○</span>
                                <span>Status</span>
                            </span>
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <span class="text-slate-400 text-[10px]">△</span>
                                <span>Priority</span>
                            </span>
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span>Assignee</span>
                            </span>
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span>Category</span>
                            </span>
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span>Channel</span>
                            </span>
                        </th>
                        <th class="py-3 px-3 text-[11px] font-semibold text-slate-600 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <circle cx="12" cy="12" r="9" stroke-width="2"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 7v5l3 2"/>
                                </svg>
                                <span>Health</span>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($tickets as $ticket)
                        @php
                            $health = $ticket->health();
                            $prio = $ticket->priorityDisplay();
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <!-- Checkbox -->
                            <td class="py-3.5 px-3.5 text-center">
                                <input type="checkbox" class="ticket-checkbox w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-0 cursor-pointer">
                            </td>

                            <!-- # ID -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-medium text-slate-500 hover:text-slate-900 transition-colors">
                                    {{ $ticket->shortNumber() }}
                                </a>
                            </td>

                            <!-- Subject -->
                            <td class="py-3.5 px-4 min-w-[280px] max-w-md">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold text-slate-900 hover:text-indigo-600 line-clamp-1 transition-colors">
                                    {{ $ticket->title }}
                                </a>
                            </td>

                            <!-- Status Pill -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $ticket->status->badgeClasses() }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>

                            <!-- Priority -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 text-xs {{ $prio['textColor'] }}">
                                    <span class="{{ $prio['iconColor'] }}">{{ $prio['icon'] }}</span>
                                    <span>{{ $prio['label'] }}</span>
                                </span>
                            </td>

                            <!-- Assignee -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @if($ticket->assignedAgent)
                                    <div class="inline-flex items-center gap-2">
                                        @if($ticket->assignedAgent->avatarUrl())
                                            <img src="{{ $ticket->assignedAgent->avatarUrl() }}" alt="{{ $ticket->assignedAgent->name }}"
                                                class="w-5 h-5 rounded-full object-cover ring-1 ring-slate-200">
                                        @else
                                            <div class="w-5 h-5 rounded-full bg-slate-200/90 text-slate-700 text-[9px] font-bold flex items-center justify-center">
                                                {{ $ticket->assignedAgent->initials() }}
                                            </div>
                                        @endif
                                        <span class="text-xs text-slate-800 font-medium">{{ $ticket->assignedAgent->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-400">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>Unassigned</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Category -->
                            <td class="py-3.5 px-3 whitespace-nowrap text-slate-700 font-medium">
                                {{ $ticket->category->value }}
                            </td>

                            <!-- Channel -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                @php $chan = strtolower($ticket->channel ?? 'email'); @endphp
                                @if($chan === 'chat')
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span>Chat</span>
                                    </span>
                                @elseif($chan === 'slack')
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span>Slack</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span>Email</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Health -->
                            <td class="py-3.5 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $health['badge'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $health['dot'] }}"></span>
                                    <span>{{ $health['label'] }}</span>
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-500">
                                <div class="max-w-xs mx-auto">
                                    <svg class="w-10 h-10 text-slate-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-semibold text-slate-800 text-sm">No tickets found</p>
                                    <p class="text-xs text-slate-500 mt-1">Try clearing search filters or create a new ticket.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($tickets->hasPages())
            <div class="px-5 py-3.5 bg-white border-t border-slate-100 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Showing <span class="font-medium text-slate-800">{{ $tickets->firstItem() }}</span> to <span class="font-medium text-slate-800">{{ $tickets->lastItem() }}</span> of <span class="font-medium text-slate-800">{{ $tickets->total() }}</span> tickets
                </div>
                <div>
                    {{ $tickets->links() }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    // Select all checkboxes toggle
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.ticket-checkbox');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
            });
        }
    });
</script>
@endsection
