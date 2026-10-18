@extends('layouts.app')

@section('title', 'Customers')
@section('breadcrumb', 'Customers')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Customer Directory</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage enterprise accounts, clients, and support history</p>
        </div>
        <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Add Customer</span>
        </a>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
        <form method="GET" action="{{ route('customers.index') }}" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, company, email, or phone..."
                class="flex-1 px-3.5 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
            <button type="submit" class="py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl transition-colors">
                Search
            </button>
            @if($search)
                <a href="{{ route('customers.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold rounded-xl">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Customer Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($customers as $c)
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 hover:border-slate-300 transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-sm shadow-indigo-500/20">
                                {{ $c->initials() }}
                            </div>
                            <div>
                                <a href="{{ route('customers.show', $c) }}" class="font-bold text-sm text-slate-900 hover:text-indigo-600 block">
                                    {{ $c->name }}
                                </a>
                                <span class="text-xs text-slate-500">{{ $c->company_name ?? 'Individual Client' }}</span>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700">
                            {{ $c->tickets_count }} tickets
                        </span>
                    </div>

                    <div class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                        <div class="flex items-center gap-2 truncate">
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <span class="truncate">{{ $c->email }}</span>
                        </div>
                        @if($c->phone)
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span>{{ $c->phone }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 mt-4 border-t border-slate-100 text-xs">
                    <a href="{{ route('customers.show', $c) }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                        View Profile &rarr;
                    </a>
                    <a href="{{ route('customers.edit', $c) }}" class="text-slate-500 hover:text-slate-800">
                        Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200/80 p-12 text-center text-slate-500">
                No customer records found.
            </div>
        @endforelse
    </div>

    @if($customers->hasPages())
        <div class="p-4 bg-white rounded-2xl border border-slate-200/80">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
