<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'RPP Generator') }} - Modul Ajar AI</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-[hsl(var(--background))]">
            <!-- Sidebar -->
            <aside class="sidebar hidden lg:block">
                <div class="flex flex-col h-full">
                    <!-- Logo -->
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-[hsl(var(--border))]">
                        <img src="{{ asset('logo.png') }}" alt="Logo" class="w-8 h-8 object-contain">
                        <span class="font-semibold text-[hsl(var(--foreground))]">RPP Generator</span>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                        <a href="{{ route('dashboard') }}" 
                           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            Dashboard
                        </a>

                        <a href="{{ route('rpp.index') }}" 
                           class="sidebar-link {{ request()->routeIs('rpp.*') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            RPP Saya
                        </a>

                        <a href="{{ route('rpp.create') }}" 
                           class="sidebar-link {{ request()->routeIs('rpp.create') ? 'active' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Buat RPP Baru
                        </a>

                        @if(auth()->user()->isAdmin())
                        <div class="pt-4 mt-4 border-t border-[hsl(var(--border))]">
                            <p class="px-4 mb-2 text-xs font-medium text-[hsl(var(--muted-foreground))] uppercase tracking-wider">Admin</p>
                            <a href="{{ route('admin.users.index') }}" 
                               class="sidebar-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Kelola User
                            </a>
                            <a href="{{ route('settings.index') }}" 
                               class="sidebar-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Pengaturan Sekolah
                            </a>
                        </div>
                        @endif
                    </nav>

                    <!-- User Menu -->
                    <div class="px-4 py-4 border-t border-[hsl(var(--border))]">
                        <div class="flex items-center gap-3 mb-3 px-2">
                            <div class="w-8 h-8 rounded-full bg-[hsl(var(--secondary))] flex items-center justify-center">
                                <span class="text-sm font-medium text-[hsl(var(--secondary-foreground))]">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[hsl(var(--foreground))] truncate">
                                    {{ auth()->user()->name }}
                                </p>
                                <p class="text-xs text-[hsl(var(--muted-foreground))] truncate">
                                    {{ ucfirst(auth()->user()->role) }}
                                </p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <a href="{{ route('profile.edit') }}" class="sidebar-link">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="sidebar-link w-full text-left text-[hsl(var(--destructive))]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <div class="lg:pl-64">
                <!-- Top Header -->
                <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-6 border-b border-[hsl(var(--border))] bg-[hsl(var(--background))/0.8] backdrop-blur-lg">
                    <!-- Mobile Menu Button -->
                    <button type="button" class="lg:hidden p-2 -ml-2 rounded-lg hover:bg-[hsl(var(--accent))]" x-data @click="$dispatch('toggle-sidebar')">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <!-- Page Title -->
                    @isset($header)
                        <h1 class="text-lg font-semibold text-[hsl(var(--foreground))]">
                            {{ $header }}
                        </h1>
                    @else
                        <div></div>
                    @endisset

                    <!-- Right Side -->
                    <div class="flex items-center gap-4">
                        <x-ui.badge variant="secondary">{{ ucfirst(auth()->user()->role) }}</x-ui.badge>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="p-6">
                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-6">
                            <x-ui.alert type="success" dismissible>
                                {{ session('success') }}
                            </x-ui.alert>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6">
                            <x-ui.alert type="error" dismissible>
                                {{ session('error') }}
                            </x-ui.alert>
                        </div>
                    @endif

                    {{ $slot }}
                </main>
            </div>
        </div>

        <!-- Mobile Sidebar -->
        <div x-data="{ open: false }" @toggle-sidebar.window="open = !open" x-show="open" x-cloak class="lg:hidden">
            <div class="fixed inset-0 z-40 bg-black/50" @click="open = false"></div>
            <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-[hsl(var(--border))]">
                <!-- Same sidebar content as desktop -->
            </aside>
        </div>
    </body>
</html>
