@extends('layouts.app')

@section('title', "Edit Ticket {$ticket->ticket_number}")
@section('breadcrumb', "Tickets / {$ticket->ticket_number} / Edit")

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Ticket {{ $ticket->ticket_number }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Update ticket details, status, or assignment</p>
        </div>
        <a href="{{ route('tickets.show', $ticket) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to ticket
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Subject / Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $ticket->title) }}" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none transition-all">
                @error('title')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if(auth()->user()->isStaff())
            <!-- Customer -->
            <div>
                <label for="customer_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Customer</label>
                <select id="customer_id" name="customer_id" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('customer_id', $ticket->customer_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->company_name ?? 'Individual' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status, Priority, Category 3-col Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Status</label>
                    <select id="status" name="status" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        @foreach(\App\Enums\TicketStatus::cases() as $st)
                            <option value="{{ $st->value }}" {{ old('status', $ticket->status->value) === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Priority</label>
                    <select id="priority" name="priority" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        @foreach(\App\Enums\TicketPriority::cases() as $p)
                            <option value="{{ $p->value }}" {{ old('priority', $ticket->priority->value) === $p->value ? 'selected' : '' }}>
                                {{ $p->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category</label>
                    <select id="category" name="category" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        @foreach(\App\Enums\TicketCategory::cases() as $cat)
                            <option value="{{ $cat->value }}" {{ old('category', $ticket->category->value) === $cat->value ? 'selected' : '' }}>
                                {{ $cat->value }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Assigned Agent -->
            <div>
                <label for="assigned_to_user_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Assigned Agent</label>
                <select id="assigned_to_user_id" name="assigned_to_user_id"
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="">Unassigned</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" {{ old('assigned_to_user_id', $ticket->assigned_to_user_id) == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }} ({{ $agent->role->label() }})
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Description</label>
                <textarea id="description" name="description" rows="5" required
                    class="w-full p-3.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none transition-all">{{ old('description', $ticket->description) }}</textarea>
                @error('description')
                    <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('tickets.show', $ticket) }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
