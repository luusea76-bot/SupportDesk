@extends('layouts.app')

@section('title', "Customer - {$customer->name}")
@section('breadcrumb', "Customers / {$customer->name}")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-bold text-xl flex items-center justify-center shadow-md shadow-indigo-500/20">
                {{ $customer->initials() }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $customer->name }}</h1>
                <p class="text-sm text-slate-500">{{ $customer->company_name ?? 'Individual Customer' }} &bull; Customer since {{ $customer->created_at->format('M Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('customers.edit', $customer) }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-xs">
                Edit Profile
            </a>
            <a href="{{ route('tickets.create', ['customer_id' => $customer->id]) }}" class="px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-sm">
                + New Ticket For Customer
            </a>
        </div>
    </div>

    <!-- Contact & Stats Details -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Contact Details</span>
            <div class="space-y-2 text-xs text-slate-700">
                <div>Email: <a href="mailto:{{ $customer->email }}" class="text-indigo-600 font-medium hover:underline">{{ $customer->email }}</a></div>
                <div>Phone: <span class="font-medium text-slate-900">{{ $customer->phone ?? 'Not provided' }}</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Account Notes</span>
            <div class="text-xs text-slate-600">
                {{ $customer->notes ?? 'No internal notes on this customer profile.' }}
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block mb-2">Support Activity</span>
            <div class="text-2xl font-bold text-slate-900">{{ $customer->tickets->count() }}</div>
            <div class="text-xs text-slate-500 mt-1">Total lifetime tickets submitted</div>
        </div>
    </div>

    <!-- Customer Ticket History -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
        <h2 class="text-base font-bold text-slate-900 mb-4">Ticket History</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-2.5 px-3">Ticket ID</th>
                        <th class="py-2.5 px-3">Subject</th>
                        <th class="py-2.5 px-3">Priority</th>
                        <th class="py-2.5 px-3">Status</th>
                        <th class="py-2.5 px-3">Assigned To</th>
                        <th class="py-2.5 px-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($customer->tickets as $t)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-3 font-mono font-semibold text-indigo-600">
                                <a href="{{ route('tickets.show', $t) }}" class="hover:underline">{{ $t->ticket_number }}</a>
                            </td>
                            <td class="py-3 px-3 font-medium text-slate-900">
                                <a href="{{ route('tickets.show', $t) }}" class="hover:text-indigo-600">{{ $t->title }}</a>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $t->priority->badgeClasses() }}">
                                    {{ $t->priority->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold border {{ $t->status->badgeClasses() }}">
                                    {{ $t->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-3 text-slate-600">
                                {{ $t->assignedAgent?->name ?? 'Unassigned' }}
                            </td>
                            <td class="py-3 px-3 text-slate-500">
                                {{ $t->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">No tickets found for this customer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
