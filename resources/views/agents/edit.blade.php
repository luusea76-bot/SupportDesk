@extends('layouts.app')

@section('title', "Edit {$agent->name}")
@section('breadcrumb', "Team & Agents / {$agent->name} / Edit")

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Member Profile</h1>
            <p class="text-sm text-slate-500 mt-0.5">Update team member role, contact info, or active status</p>
        </div>
        <a href="{{ route('agents.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900">
            &larr; Back to team
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('agents.update', $agent) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $agent->name) }}" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email', $agent->email) }}" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Role</label>
                    <select id="role" name="role" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        <option value="agent" {{ $agent->role->value === 'agent' ? 'selected' : '' }}>Support Agent</option>
                        <option value="admin" {{ $agent->role->value === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $agent->phone) }}"
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>

            <div>
                <label for="is_active" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Account Status</label>
                <select id="is_active" name="is_active" required
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                    <option value="1" {{ $agent->is_active ? 'selected' : '' }}>Active (Can log in and manage tickets)</option>
                    <option value="0" {{ ! $agent->is_active ? 'selected' : '' }}>Deactivated (Access revoked)</option>
                </select>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">New Password (leave empty to keep current)</label>
                <input type="password" id="password" name="password" placeholder="Optional password reset"
                    class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('agents.index') }}" class="px-4 py-2.5 text-xs font-semibold text-slate-600 hover:text-slate-800 rounded-xl">
                    Cancel
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                    Update Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
