<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - ICCT Cainta Campus MIS</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @include('partials.pwa-meta')
    <style>
        [x-cloak] { display: none !important; }
        .scrollbar-thin::-webkit-scrollbar { width: 4px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex">

        {{-- Mobile overlay --}}
        <div x-show="sidebarOpen" x-cloak
             @@click="sidebarOpen = false"
             class="fixed inset-0 z-30 bg-black/50 lg:hidden transition-opacity">
        </div>

        {{-- Sidebar --}}
        <aside
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed lg:static inset-y-0 left-0 z-40 w-64 shrink-0 bg-[#1a237e] flex flex-col
                   transition-transform duration-300 ease-in-out lg:!translate-x-0">
            {{-- Brand with close button --}}
            <div class="px-6 py-5 border-b border-white/10 flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=2" alt="ICCT MIS" class="w-8 h-8 object-contain">
                    <div>
                        <div class="text-white font-bold text-sm leading-tight">ICCT CAINTA</div>
                        <div class="text-indigo-300 text-xs">Campus MIS</div>
                    </div>
                </a>
                <button @@click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white transition p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- User Info --}}
            <div class="px-6 py-4 border-b border-white/10">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white/20 flex items-center justify-center text-white font-bold text-sm">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-white text-sm font-medium truncate">{{ Auth::user()->name }}</div>
                        <div class="text-indigo-300 text-xs">{{ ucfirst(Auth::user()->role) }}</div>
                    </div>
                </div>
            </div>

            {{-- Nav Items --}}
            <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto scrollbar-thin">
                @auth
                    @if(auth()->user()->isAdmin())
                    <a href="{{ route('dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                       {{ request()->routeIs('dashboard') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Overview</span>
                    </a>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.resources.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                           {{ request()->routeIs('admin.resources.*') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            <span>Resources</span>
                        </a>

                        <a href="{{ route('admin.borrows.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                           {{ request()->routeIs('admin.borrows.*') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <span>Transactions</span>
                        </a>

                        <a href="{{ route('admin.users.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                           {{ request()->routeIs('admin.users.*') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span>Users</span>
                        </a>

                    @else
                        <a href="{{ route('faculty.resources.index') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                           {{ request()->routeIs('faculty.resources.*') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <span>Browse Resources</span>
                        </a>

                        <a href="{{ route('faculty.history') }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                           {{ request()->routeIs('faculty.history') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>My History</span>
                        </a>
                    @endif

                    <a href="{{ route('notifications.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition
                       {{ request()->routeIs('notifications.*') ? 'bg-white/15 text-white' : 'text-indigo-200 hover:bg-white/10 hover:text-white' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                        <span>Notifications</span>
                    </a>
                @endauth
            </nav>

            {{-- Logout --}}
            <div class="px-3 pb-4 border-t border-white/10 pt-3">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-indigo-300 hover:bg-white/10 hover:text-white transition">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Top Bar --}}
            <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
                <div class="flex items-center justify-between px-3 lg:px-6 py-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @@click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700 transition p-1 -ml-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-base lg:text-lg font-bold text-gray-900 truncate">@yield('header', 'Dashboard')</h1>
                            <p class="text-xs text-gray-500 truncate">@yield('subheader', '')</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 lg:gap-3 shrink-0">
                        <a href="{{ route('notifications.index') }}" class="relative inline-flex items-center justify-center text-gray-600 hover:text-gray-900 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span id="notif-count-top" class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center hidden shadow-sm px-1 ring-2 ring-white">0</span>
                        </a>
                        <x-dropdown align="right" width="72">
                            <x-slot name="trigger">
                                <button class="flex items-center gap-1 lg:gap-2 px-1 lg:px-2 py-1 rounded-lg hover:bg-gray-100 transition text-sm">
                                    <div class="w-7 h-7 rounded-full bg-[#1a237e] flex items-center justify-center text-white text-xs font-bold">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <span class="text-gray-700 font-medium hidden lg:inline">{{ Auth::user()->name }}</span>
                                    <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ Auth::user()->email }}</div>
                                    @if(Auth::user()->department)
                                        <div class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->department }}</div>
                                    @endif
                                </div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                            Log Out
                                        </div>
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="px-4 lg:px-6 pt-3 lg:pt-4">
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="px-4 lg:px-6 pt-3 lg:pt-4">
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
            @endif



            {{-- Toast notifications container --}}
            <div id="toast-container" class="fixed top-0 left-1/2 -translate-x-1/2 z-50 w-full max-w-lg pt-3 px-4 space-y-2 pointer-events-none"></div>

            {{-- Page Content --}}
            <main class="flex-1 p-3 lg:p-6">
                @yield('content')
                {{-- Watermark Logo (mobile only, inside the app) --}}
                <div class="lg:hidden flex justify-center mt-10 pt-6 border-t border-gray-200">
                    <img src="{{ asset('assets/images/icct-mis-logo.png') }}?v=5" alt="ICCT MIS" class="w-40 h-40 object-contain opacity-30">
                </div>
            </main>
        </div>
    </div>

    <script>
    var prevNotifCount = 0;
    var seenNotifIds = new Set();
    var toastQueue = [];
    var showingToast = false;
    var isFirstNotifPoll = true;

    function showToast(title, message) {
        toastQueue.push({ title: title, message: message });
        processToastQueue();
    }

    function processToastQueue() {
        if (showingToast || toastQueue.length === 0) return;
        showingToast = true;
        var item = toastQueue.shift();
        var container = document.getElementById('toast-container');
        if (!container) return;

        var toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-white border-l-4 border-indigo-500 rounded-xl shadow-lg px-5 py-4 flex items-start gap-3 transform transition-all duration-300 translate-y-[-20px] opacity-0';
        toast.innerHTML =
            '<div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center shrink-0">' +
            '<svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
            '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>' +
            '<div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900">' + escapeHtml(title) + '</p>' +
            (message ? '<p class="text-xs text-gray-500 mt-0.5">' + escapeHtml(message) + '</p>' : '') + '</div>' +
            '<button onclick="this.closest(\'[data-toast]\').remove(); processToastQueue()" class="text-gray-300 hover:text-gray-600 shrink-0">' +
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        toast.setAttribute('data-toast', '');
        container.appendChild(toast);

        requestAnimationFrame(function() {
            toast.classList.remove('translate-y-[-20px]', 'opacity-0');
        });

        setTimeout(function() {
            toast.classList.add('translate-y-[-20px]', 'opacity-0');
            setTimeout(function() {
                toast.remove();
                showingToast = false;
                processToastQueue();
            }, 300);
        }, 8000);
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(str || ''));
        return div.innerHTML;
    }

    function updateNotifCount() {
        fetch('{{ route("notifications.unreadCount") }}')
            .then(r => r.json())
            .then(d => {
                const el = document.getElementById('notif-count-top');
                if (el) {
                    if (d.count > 0) {
                        el.textContent = d.count;
                        el.classList.remove('hidden');
                    } else {
                        el.classList.add('hidden');
                    }
                }
                if (d.count > prevNotifCount && prevNotifCount > 0) {
                    var bell = document.querySelector('[href*="notifications"] svg, a[href*="notification"] svg');
                    if (bell) {
                        bell.classList.add('animate-ping');
                        setTimeout(function() { bell.classList.remove('animate-ping'); }, 1000);
                    }
                }
                prevNotifCount = d.count;
            });
    }

    function pollLatestNotifications() {
        var after = 0;
        var ids = Array.from(seenNotifIds);
        if (ids.length > 0) after = Math.max.apply(null, ids);

        fetch('/notifications/latest?after=' + after)
            .then(function(r) { return r.json(); })
            .then(function(d) {
                d.notifications.forEach(function(n) {
                    if (!seenNotifIds.has(n.id)) {
                        seenNotifIds.add(n.id);
                        if (!isFirstNotifPoll) {
                            showToast(n.title, n.message);
                        }
                    }
                });
                isFirstNotifPoll = false;
            });
    }

    updateNotifCount();
    setInterval(updateNotifCount, 30000);
    setInterval(pollLatestNotifications, 20000);
    </script>
    <script defer src="{{ asset('assets/js/app.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
