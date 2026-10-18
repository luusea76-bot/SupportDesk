@extends('layouts.app')

@section('title', 'My Profile')
@section('breadcrumb', 'Profile')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Account Settings</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage your profile details and security credentials</p>
    </div>

    <!-- User Profile Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 flex items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-500 to-violet-500 text-white font-bold text-2xl flex items-center justify-center shadow-md shadow-indigo-500/20 shrink-0">
            {{ $user->initials() }}
        </div>
        <div>
            <h2 class="text-lg font-bold text-slate-900">{{ $user->name }}</h2>
            <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                <span>{{ $user->email }}</span>
                <span>&bull;</span>
                <span class="px-2 py-0.5 rounded-full font-semibold bg-indigo-50 text-indigo-700">
                    {{ $user->role->label() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                    @error('name')
                        <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000"
                        class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                </div>
            </div>

            <!-- Change Password Section -->
            <div class="pt-6 border-t border-slate-100">
                <h3 class="text-sm font-bold text-slate-900 mb-1">Update Password</h3>
                <p class="text-xs text-slate-500 mb-4">Ensure your account is using a long, random password to stay secure.</p>

                <div class="space-y-4">
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Current Password</label>
                        <input type="password" id="current_password" name="current_password"
                            class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        @error('current_password')
                            <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">New Password</label>
                            <input type="password" id="new_password" name="new_password"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                            @error('new_password')
                                <p class="text-rose-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Confirm New Password</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                                class="w-full px-3.5 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:border-indigo-500 outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                    Save Profile Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
