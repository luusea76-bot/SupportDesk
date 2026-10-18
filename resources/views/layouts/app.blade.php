<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SupportDesk CRM') }} - @yield('title', 'IT Helpdesk')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-50 flex">

    <!-- Mobile Sidebar Backdrop -->
    <div id="mobile-backdrop" class="fixed inset-0 bg-slate-900/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-300 flex flex-col transition-transform duration-200 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto lg:z-auto">
        <!-- Brand Header -->
        <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800">
            <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center text-white shadow-md shadow-indigo-500/20">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <div>
                <span class="font-bold text-white tracking-tight text-base">SupportDesk</span>
                <span class="block text-[11px] font-medium text-indigo-400 -mt-1 tracking-wider uppercase">CRM Platform</span>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
            <div>
                <div class="px-3 text-[11px] font-semibold text-slate-400 tracking-wider uppercase mb-2">Navigation</div>
                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span>Dashboard</span>
                        </div>
                    </a>

                    <!-- Tickets -->
                    @php
                        $openCount = \App\Models\Ticket::whereIn('status', ['open', 'in_progress'])->count();
                    @endphp
                    <a href="{{ route('tickets.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('tickets.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>Tickets</span>
                        </div>
                        @if($openCount > 0)
                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ request()->routeIs('tickets.*') ? 'bg-indigo-700 text-indigo-100' : 'bg-slate-800 text-indigo-400' }}">{{ $openCount }}</span>
                        @endif
                    </a>

                    <!-- Customers (Staff only) -->
                    @if(auth()->check() && auth()->user()->isStaff())
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('customers.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Customers</span>
                    </a>
                    @endif
                </nav>
            </div>

            <!-- Management Section -->
            @if(auth()->check() && auth()->user()->isStaff())
            <div>
                <div class="px-3 text-[11px] font-semibold text-slate-400 tracking-wider uppercase mb-2">Management</div>
                <nav class="space-y-1">
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Reports</span>
                    </a>

                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('agents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('agents.*') ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Team & Agents</span>
                    </a>
                    @endif
                </nav>
            </div>
            @endif
        </div>

        <!-- User Profile Bar -->
        @auth
        <div class="p-3 border-t border-slate-800">
            <div class="flex items-center justify-between p-2 rounded-lg bg-slate-800/60 hover:bg-slate-800 transition-colors">
                <a href="{{ route('profile') }}" class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-amber-500 to-rose-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                        {{ auth()->user()->initials() }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400 truncate">{{ auth()->user()->role->label() }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="shrink-0">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-1.5 text-slate-400 hover:text-rose-400 rounded-md transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
        @endauth
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Navbar (Matching Design) -->
        <header class="h-16 bg-white border-b border-slate-200/90 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs gap-4">
            <!-- Left Side: Mobile Menu Button & Pill Search Bar -->
            <div class="flex items-center gap-3 flex-1 max-w-xl">
                <button type="button" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 shrink-0" onclick="toggleSidebar()">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <!-- Pill-Shaped Rounded Search Bar -->
                <form method="GET" action="{{ route('tickets.index') }}" class="relative w-full max-w-md">
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                            <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search"
                            class="block w-full rounded-full border border-slate-200 bg-white py-2 pl-10 pr-4 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/15 shadow-xs transition-all">
                    </div>
                </form>
            </div>

            <!-- Right Side Controls: New Ticket, Notification Bell, User Chip -->
            <div class="flex items-center gap-3 sm:gap-4 shrink-0">
                <!-- New Ticket Quick Button -->
                <a href="{{ route('tickets.create') }}" class="hidden md:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full shadow-xs transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>New Ticket</span>
                </a>

                <!-- Notification Bell with Active Red Dot Badge -->
                <div class="relative" id="notifications-wrapper">
                    <button type="button" onclick="toggleDropdown('notifications-dropdown')" class="relative p-2 rounded-full text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors focus:outline-none">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Red Notification Dot -->
                        <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-rose-500 rounded-full ring-2 ring-white"></span>
                    </button>

                    <!-- Notifications Dropdown Popup -->
                    <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 uppercase tracking-wider">Notifications</span>
                            <span class="text-[10px] text-indigo-600 font-semibold cursor-pointer hover:underline">Mark all read</span>
                        </div>
                        <div class="max-h-72 overflow-y-auto divide-y divide-slate-50">
                            @php
                                $recentAlerts = \App\Models\TicketActivity::with(['ticket', 'user'])->latest('created_at')->take(4)->get();
                            @endphp
                            @forelse($recentAlerts as $alert)
                                <a href="{{ $alert->ticket ? route('tickets.show', $alert->ticket) : '#' }}" class="block px-4 py-2.5 hover:bg-slate-50 transition-colors">
                                    <div class="flex items-start gap-2.5">
                                        <span class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-slate-800 line-clamp-2">
                                                <span class="font-semibold">{{ $alert->user ? $alert->user->name : 'System' }}</span>
                                                {{ $alert->description }}
                                            </p>
                                            <span class="text-[10px] text-slate-600 mt-0.5 block">{{ $alert->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-slate-600">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- User Profile Chip with Avatar, Name, and Role -->
                @auth
                <div class="relative" id="user-menu-wrapper">
                    <button type="button" onclick="toggleDropdown('user-dropdown')" class="flex items-center gap-2.5 p-1 sm:pr-2.5 rounded-full hover:bg-slate-100 transition-colors focus:outline-none">
                        @if(auth()->user()->avatarUrl())
                            <img src="{{ auth()->user()->avatarUrl() }}" alt="{{ auth()->user()->name }}"
                                class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover ring-1 ring-slate-200 shadow-xs">
                        @else
                            <div class="w-8 h-8 sm:w-9 sm:h-9 rounded-full bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center ring-1 ring-indigo-200 shadow-xs">
                                {{ auth()->user()->initials() }}
                            </div>
                        @endif
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-slate-600 capitalize leading-tight">{{ auth()->user()->role->label() }}</div>
                        </div>
                        <svg class="hidden sm:block w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- User Dropdown Menu -->
                    <div id="user-dropdown" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 z-50">
                        <div class="px-4 py-2 border-b border-slate-100 sm:hidden">
                            <div class="text-xs font-bold text-slate-900">{{ auth()->user()->name }}</div>
                            <div class="text-[11px] text-slate-600">{{ auth()->user()->role->label() }}</div>
                        </div>

                        <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>Profile Settings</span>
                        </a>

                        <a href="{{ route('tickets.index') }}" class="flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 hover:text-indigo-600 transition-colors">
                            <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            <span>My Tickets</span>
                        </a>

                        <div class="border-t border-slate-100 my-1"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2 text-xs font-medium text-rose-600 hover:bg-rose-50 transition-colors text-left">
                                <svg class="w-4 h-4 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-blue-600 hover:text-blue-900">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="mx-4 sm:mx-6 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-xs">
                <div class="font-semibold mb-1 flex items-center gap-1.5">
                    <svg class="w-5 h-5 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Please review the following errors:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-0.5 ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8">
            @yield('content')
        </main>
    </div>

    <!-- Script for mobile drawer and dropdowns -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('mobile-backdrop');
            sidebar.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        function toggleDropdown(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const isHidden = el.classList.contains('hidden');
            
            // Close other dropdowns first
            document.querySelectorAll('#notifications-dropdown, #user-dropdown, #table-options-menu, #statuses-filter-menu').forEach(d => {
                if (d.id !== id) d.classList.add('hidden');
            });

            if (isHidden) {
                el.classList.remove('hidden');
            } else {
                el.classList.add('hidden');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const notifWrapper = document.getElementById('notifications-wrapper');
            const userWrapper = document.getElementById('user-menu-wrapper');
            const tableOptionsWrapper = document.getElementById('table-options-wrapper');
            const statusesFilterWrapper = document.getElementById('statuses-filter-wrapper');

            if (notifWrapper && !notifWrapper.contains(e.target)) {
                const notifDropdown = document.getElementById('notifications-dropdown');
                if (notifDropdown) notifDropdown.classList.add('hidden');
            }
            if (userWrapper && !userWrapper.contains(e.target)) {
                const userDropdown = document.getElementById('user-dropdown');
                if (userDropdown) userDropdown.classList.add('hidden');
            }
            if (tableOptionsWrapper && !tableOptionsWrapper.contains(e.target)) {
                const tableMenu = document.getElementById('table-options-menu');
                if (tableMenu) tableMenu.classList.add('hidden');
            }
            if (statusesFilterWrapper && !statusesFilterWrapper.contains(e.target)) {
                const statusesMenu = document.getElementById('statuses-filter-menu');
                if (statusesMenu) statusesMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
