@extends('layouts.app')

@section('title', "Ticket {$ticket->ticket_number}")
@section('breadcrumb', "Tickets / {$ticket->ticket_number}")

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="font-mono font-bold text-lg text-indigo-600">{{ $ticket->ticket_number }}</span>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $ticket->status->badgeClasses() }}">
                    {{ $ticket->status->label() }}
                </span>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold border {{ $ticket->priority->badgeClasses() }}">
                    {{ $ticket->priority->label() }}
                </span>
                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                    {{ $ticket->category->value }}
                </span>
                @if($ticket->isOverdue())
                    <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                        Overdue SLA
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $ticket->title }}</h1>
        </div>

        <div class="flex items-center gap-2">
            @can('update', $ticket)
            <a href="{{ route('tickets.edit', $ticket) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-semibold rounded-xl shadow-xs transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Ticket</span>
            </a>
            @endcan

            @can('delete', $ticket)
            <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Are you sure you want to permanently delete this ticket?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 text-xs font-semibold rounded-xl transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Delete</span>
                </button>
            </form>
            @endcan
        </div>
    </div>

    <!-- 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left / Main Column (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Description Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between text-xs text-slate-500 pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-[10px]">
                            {{ $ticket->creator ? $ticket->creator->initials() : 'U' }}
                        </div>
                        <span class="font-medium text-slate-800">{{ $ticket->creator ? $ticket->creator->name : 'Customer' }}</span>
                        <span>submitted ticket</span>
                    </div>
                    <span>{{ $ticket->created_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
                <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $ticket->description }}
                </div>
            </div>

            <!-- Conversation & Comments -->
            <div id="comments" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-base font-bold text-slate-900">Discussion & Notes</h2>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $comments->count() }} messages</span>
                </div>

                <div class="space-y-4 mb-6">
                    @forelse($comments as $comment)
                        <div class="p-4 rounded-xl {{ $comment->is_internal ? 'bg-amber-50/70 border border-amber-200/80' : 'bg-slate-50 border border-slate-100' }}">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full {{ $comment->is_internal ? 'bg-amber-200 text-amber-900' : 'bg-indigo-100 text-indigo-700' }} font-bold text-[10px] flex items-center justify-center">
                                        {{ $comment->user->initials() }}
                                    </div>
                                    <span class="font-semibold text-slate-900">{{ $comment->user->name }}</span>
                                    <span class="text-[11px] text-slate-500">({{ $comment->user->role->label() }})</span>

                                    @if($comment->is_internal)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-200 text-amber-900">
                                            Internal Staff Note
                                        </span>
                                    @endif
                                </div>
                                <span class="text-slate-600 text-[11px]">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line pl-8">
                                {{ $comment->body }}
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center text-slate-600 text-xs">
                            No comments yet. Start the conversation below.
                        </div>
                    @endforelse
                </div>

                <!-- Add Comment Form -->
                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" class="pt-4 border-t border-slate-100">
                    @csrf
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">Add Message or Note</label>
                    <textarea name="body" rows="3" required placeholder="Type your message or internal observation..."
                        class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none transition-all"></textarea>

                    <div class="flex items-center justify-between mt-3">
                        @if(auth()->user()->isStaff())
                            <label class="flex items-center gap-2 cursor-pointer text-xs text-amber-800 font-medium">
                                <input type="checkbox" name="is_internal" value="1" class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                                <span>Post as internal staff note (hidden from customer)</span>
                            </label>
                        @else
                            <div></div>
                        @endif

                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-xs transition-colors">
                            Send Reply
                        </button>
                    </div>
                </form>
            </div>

            <!-- Activity Audit Trail -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
                <h2 class="text-base font-bold text-slate-900 mb-4">Audit History & Activity Log</h2>
                <div class="space-y-3">
                    @foreach($ticket->activities as $act)
                        <div class="flex items-start gap-3 text-xs">
                            <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                            <div class="flex-1">
                                <span class="font-medium text-slate-900">{{ $act->user ? $act->user->name : 'System' }}</span>
                                <span class="text-slate-600">{{ $act->description }}</span>
                                <span class="text-[11px] text-slate-600 ml-1">&bull; {{ $act->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Sidebar (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- Ticket Lifecycle & Status -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Status & Workflow</h3>
                
                @can('changeStatus', $ticket)
                <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Update Status</label>
                        <select name="status" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-500 outline-none">
                            @foreach(\App\Enums\TicketStatus::cases() as $st)
                                <option value="{{ $st->value }}" {{ $ticket->status === $st ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-1.5 px-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-lg transition-colors">
                        Change Status
                    </button>
                </form>
                @endcan

                <!-- Agent Assignment (Staff only) -->
                @can('assign', $ticket)
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-3 pt-4 mt-4 border-t border-slate-100">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Assign Agent</label>
                        <select name="assigned_to_user_id" class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-lg focus:bg-white focus:border-indigo-500 outline-none">
                            <option value="">Unassigned</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ $ticket->assigned_to_user_id === $agent->id ? 'selected' : '' }}>
                                    {{ $agent->name }} ({{ $agent->role->label() }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-1.5 px-3 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg transition-colors">
                        Reassign Ticket
                    </button>
                </form>
                @endcan
            </div>

            <!-- Customer Details Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Customer Information</h3>
                @if($ticket->customer)
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                            {{ $ticket->customer->initials() }}
                        </div>
                        <div>
                            <div class="font-bold text-xs text-slate-900">{{ $ticket->customer->name }}</div>
                            <div class="text-[11px] text-slate-500">{{ $ticket->customer->company_name ?? 'Individual Customer' }}</div>
                        </div>
                    </div>
                    <div class="space-y-2 text-xs text-slate-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            <a href="mailto:{{ $ticket->customer->email }}" class="text-indigo-600 hover:underline">{{ $ticket->customer->email }}</a>
                        </div>
                        @if($ticket->customer->phone)
                            <div class="flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                <span>{{ $ticket->customer->phone }}</span>
                            </div>
                        @endif
                    </div>

                    @if(auth()->user()->isStaff())
                        <div class="mt-4 pt-3 border-t border-slate-100">
                            <a href="{{ route('customers.show', $ticket->customer) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">
                                View Customer History &rarr;
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-xs text-slate-600">No customer attached.</div>
                @endif
            </div>

            <!-- SLA & Resolution Timeline Card -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-3">Service Level Agreement (SLA)</h3>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500">Target Resolution:</span>
                        <span class="font-semibold text-slate-800">{{ $ticket->due_at ? $ticket->due_at->format('M d, h:i A') : 'N/A' }}</span>
                    </div>

                    @if($ticket->resolved_at)
                        <div class="flex items-center justify-between text-emerald-600">
                            <span class="font-medium">Resolved Date:</span>
                            <span class="font-semibold">{{ $ticket->resolved_at->format('M d, h:i A') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Resolution Time:</span>
                            <span class="font-semibold text-slate-800">{{ $ticket->resolutionTimeHours() }} hours</span>
                        </div>
                    @elseif($ticket->isOverdue())
                        <div class="p-2.5 rounded-lg bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                            SLA breached by {{ $ticket->due_at->diffForHumans(null, true) }}. Prioritize this request.
                        </div>
                    @else
                        <div class="flex items-center justify-between text-indigo-600">
                            <span>Time remaining:</span>
                            <span class="font-semibold">{{ $ticket->due_at->diffForHumans(null, true) }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
