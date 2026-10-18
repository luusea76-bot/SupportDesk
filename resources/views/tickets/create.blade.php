@extends('layouts.app')

@section('title', 'Create Ticket')
@section('breadcrumb', 'Tickets / Create')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Support Ticket</h1>
            <p class="text-sm text-slate-500 mt-0.5">Submit a new incident or service inquiry</p>
        </div>
        <a href="{{ route('tickets.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to tickets
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
            @csrf

            <!-- Subject / Title -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Subject / Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Cannot access VPN after certificate expiration"
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition-all">
                @error('title')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Selection (Staff only) -->
            @if(auth()->user()->isStaff())
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label for="customer_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Customer / Organization</label>
                    <a href="{{ route('customers.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">+ Add New Customer</a>
                </div>
                <select id="customer_id" name="customer_id" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">Select a customer...</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->company_name ?? 'Individual' }} - {{ $c->email }})
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endif

            <!-- 2-Column Grid for Priority & Category -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Priority</label>
                    <select id="priority" name="priority" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        @foreach(\App\Enums\TicketPriority::cases() as $p)
                            <option value="{{ $p->value }}" {{ old('priority', 'medium') === $p->value ? 'selected' : '' }}>
                                {{ $p->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category</label>
                    <select id="category" name="category" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        @foreach(\App\Enums\TicketCategory::cases() as $cat)
                            <option value="{{ $cat->value }}" {{ old('category', 'Technical') === $cat->value ? 'selected' : '' }}>
                                {{ $cat->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Assigned Agent (Staff only) -->
            @if(auth()->user()->isStaff())
            <div>
                <label for="assigned_to_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Assign To Agent (Optional)</label>
                <select id="assigned_to_user_id" name="assigned_to_user_id"
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">Leave Unassigned (Triage Queue)</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ old('assigned_to_user_id') == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }} ({{ $agent->role->label() }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Detailed Description</label>
                <textarea id="description" name="description" rows="5" required placeholder="Describe the problem, steps to reproduce, expected outcome..."
                    class="w-full p-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none transition-all">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('tickets.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                    Submit Ticket
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
