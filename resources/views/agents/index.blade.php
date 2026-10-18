@extends('layouts.app')

@section('title', 'Team & Agents')
@section('breadcrumb', 'Team & Agents')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Support Team & Agents</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage agent accounts, workload distribution, and role permissions</p>
        </div>
        <a href="{{ route('agents.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
            </svg>
            <span>Add Team Member</span>
        </a>
    </div>

    <!-- Agent Cards Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="py-3 px-4 font-semibold">Team Member</th>
                        <th class="py-3 px-4 font-semibold">Role</th>
                        <th class="py-3 px-4 font-semibold">Contact</th>
                        <th class="py-3 px-4 font-semibold text-center">Active Queue</th>
                        <th class="py-3 px-4 font-semibold text-center">Resolved</th>
                        <th class="py-3 px-4 font-semibold">Account Status</th>
                        <th class="py-3 px-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($agents as $agent)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ $agent->initials() }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $agent->name }}</div>
                                        <div class="text-[11px] text-slate-500">{{ $agent->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold {{ $agent->isAdmin() ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $agent->role->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $agent->phone ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-md font-bold text-slate-900 bg-slate-100">
                                    {{ $agent->active_tickets_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="font-semibold text-emerald-600">
                                    {{ $agent->resolved_tickets_count }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($agent->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-xs text-emerald-700 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-500"></span> Deactivated
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('agents.edit', $agent) }}" class="p-1 text-slate-500 hover:text-indigo-600" title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @if($agent->id !== auth()->id())
                                        <form method="POST" action="{{ route('agents.destroy', $agent) }}" onsubmit="return confirm('Remove agent {{ $agent->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 text-slate-500 hover:text-rose-600" title="Delete">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
