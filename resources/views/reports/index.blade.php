@extends('layouts.app')

@section('title', 'Reports & Analytics')
@section('breadcrumb', 'Reports & Analytics')

@section('content')
<div class="space-y-6">
    <!-- Header with Period Selector -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Support Analytics & Reports</h1>
            <p class="text-sm text-slate-500 mt-0.5">Performance metrics, resolution velocity, and team workload</p>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="flex items-center gap-2">
            <span class="text-xs font-semibold text-slate-500 uppercase">Period:</span>
            <select name="period" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white border border-slate-200 rounded-xl focus:border-indigo-500 outline-none shadow-xs font-medium text-slate-700">
                <option value="7" {{ $days == 7 ? 'selected' : '' }}>Last 7 Days</option>
                <option value="30" {{ $days == 30 ? 'selected' : '' }}>Last 30 Days</option>
                <option value="90" {{ $days == 90 ? 'selected' : '' }}>Last 90 Days</option>
                <option value="365" {{ $days == 365 ? 'selected' : '' }}>Past Year</option>
            </select>
        </form>
    </div>

    <!-- 3 High-Level Metrics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">New Tickets Created</span>
            <div class="text-3xl font-bold text-slate-900">{{ $ticketsCreated }}</div>
            <div class="text-xs text-slate-500 mt-1">Incoming volume during the selected period</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Tickets Resolved</span>
            <div class="text-3xl font-bold text-emerald-600">{{ $ticketsResolved }}</div>
            <div class="text-xs text-slate-500 mt-1">Successfully addressed by the support team</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-1">Average Resolution Time</span>
            <div class="text-3xl font-bold text-indigo-600">
                {{ $avgResolutionHours !== null ? round($avgResolutionHours, 1) . ' hrs' : 'N/A' }}
            </div>
            <div class="text-xs text-slate-500 mt-1">Calculated from creation to resolution</div>
        </div>
    </div>

    <!-- Breakdowns Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- By Category -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-base font-bold text-slate-900 mb-4">Volume by Category</h2>
            <div class="space-y-3">
                @foreach($byCategory as $cat => $count)
                    @php $pct = $ticketsCreated > 0 ? round(($count / $ticketsCreated) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-medium text-slate-700 mb-1">
                            <span>{{ $cat }}</span>
                            <span class="text-slate-600">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- By Priority -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-base font-bold text-slate-900 mb-4">Volume by Priority</h2>
            <div class="space-y-3">
                @foreach($byPriority as $p => $count)
                    @php $pct = $ticketsCreated > 0 ? round(($count / $ticketsCreated) * 100) : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between text-xs font-medium text-slate-700 mb-1">
                            <span class="capitalize">{{ $p }}</span>
                            <span class="text-slate-600">{{ $count }} ({{ $pct }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Current Status Snapshot -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <h2 class="text-base font-bold text-slate-900 mb-4">Current Queue Snapshot</h2>
            <div class="grid grid-cols-2 gap-3 text-center">
                @foreach($byStatus as $st => $count)
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="text-xs font-medium text-slate-600 capitalize">{{ str_replace('_', ' ', $st) }}</div>
                        <div class="text-xl font-bold text-slate-900 mt-1">{{ $count }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Agent Workload Performance Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <h2 class="text-base font-bold text-slate-900 mb-4">Agent Performance & Velocity</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Support Agent</th>
                        <th class="py-3 px-4 font-semibold">Role</th>
                        <th class="py-3 px-4 font-semibold text-center">Assigned in Period</th>
                        <th class="py-3 px-4 font-semibold text-center">Resolved in Period</th>
                        <th class="py-3 px-4 font-semibold text-center">Resolution Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($agents as $agent)
                        @php
                            $rate = $agent->assigned_count > 0 ? round(($agent->resolved_count / $agent->assigned_count) * 100) : 100;
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-3.5 px-4 font-semibold text-slate-900 flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-slate-100 font-bold text-[10px] flex items-center justify-center text-slate-700">
                                    {{ $agent->initials() }}
                                </div>
                                <span>{{ $agent->name }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $agent->role->label() }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-slate-800">{{ $agent->assigned_count }}</td>
                            <td class="py-3.5 px-4 text-center font-bold text-emerald-600">{{ $agent->resolved_count }}</td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $rate >= 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $rate }}%
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
