<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}Admin Panel | {{ config('app.name', 'Kosmetik Store') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased text-slate-900 bg-cover bg-center bg-no-repeat bg-fixed admin-layout-body" style="background-image: url('{{ asset('images/BG.png') }}')">

        <div x-data="{ sidebarOpen: false }" class="flex h-screen overflow-hidden">

            {{-- Sidebar Overlay (mobile) --}}
            <div
                x-show="sidebarOpen"
                x-transition:enter="transition-opacity ease-linear duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="sidebarOpen = false"
                class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
                aria-hidden="true"
            ></div>

            {{-- Sidebar --}}
            <aside
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
                class="fixed inset-y-0 left-0 z-30 w-64 bg-white/80 backdrop-blur-md text-slate-900 transform transition-transform duration-200 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col shadow-lg border-r border-[#FFD1DC]/30"
                aria-label="Navigasi admin"
            >
                {{-- Sidebar Header --}}
                <div class="flex items-center justify-between h-16 px-6 border-b border-[#FFD1DC]/40 flex-shrink-0">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : '#' }}" class="flex items-center gap-2.5 group">
                        {{-- Logo Beutify --}}
                        <div class="relative">
                            <svg class="w-10 h-10" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="20" cy="15" r="6" fill="#FFD1DC"/>
                                <circle cx="14" cy="22" r="6" fill="#F8BBD0"/>
                                <circle cx="26" cy="22" r="6" fill="#F8BBD0"/>
                                <circle cx="14" cy="22" r="6" fill="#89CFF0" opacity="0.5"/>
                                <circle cx="26" cy="22" r="6" fill="#89CFF0" opacity="0.5"/>
                                <circle cx="20" cy="20" r="3" fill="#E86FA3"/>
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <h2 class="text-sm font-extrabold text-[#E86FA3] group-hover:text-[#D9578F] transition">Beutify</h2>
                            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold">Admin</p>
                        </div>
                    </a>
                    <button
                        @click="sidebarOpen = false"
                        class="lg:hidden text-gray-400 hover:text-white transition-colors"
                        aria-label="Tutup sidebar"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Navigation Links --}}
                <nav class="flex-1 overflow-y-auto py-4 px-3" aria-label="Menu admin">
                    @php
                        $adminNavItems = [
                            ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
                            ['route' => 'admin.products.index', 'label' => 'Produk', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
                            ['route' => 'admin.brands.index', 'label' => 'Merek', 'icon' => 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'],
                            ['route' => 'admin.categories.index', 'label' => 'Kategori', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                            ['route' => 'admin.orders.index', 'label' => 'Pesanan', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                            ['route' => 'admin.users.index', 'label' => 'Pengguna', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['route' => 'admin.vouchers.index', 'label' => 'Voucher', 'icon' => 'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z'],
                            ['route' => 'admin.reports.index', 'label' => 'Laporan', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
                        ];
                    @endphp

                    <ul class="space-y-1" role="list">
                        @foreach ($adminNavItems as $item)
                            @php
                                $routeExists = \Illuminate\Support\Facades\Route::has($item['route']);
                                $isActive = $routeExists && request()->routeIs(rtrim($item['route'], '.index') . '*');
                            @endphp
                            <li>
                                @if ($routeExists)
                                    <a
                                        href="{{ route($item['route']) }}"
                                        class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ $isActive ? 'bg-[#E86FA3] text-white' : 'text-slate-600 hover:bg-[#FFF0F6] hover:text-[#E86FA3]' }}"
                                        aria-current="{{ $isActive ? 'page' : 'false' }}"
                                    >
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                        </svg>
                                        {{ $item['label'] }}
                                    </a>
                                @else
                                    <span class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-500 cursor-not-allowed">
                                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/>
                                        </svg>
                                        {{ $item['label'] }}
                                    </span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Sidebar Footer --}}
                <div class="flex-shrink-0 border-t border-gray-700 p-4">
                    <a href="{{ route('catalog.index') }}" class="flex items-center gap-2 text-xs text-gray-400 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Lihat Toko
                    </a>
                </div>
            </aside>

            {{-- Main Content Area --}}
            <div class="flex-1 flex flex-col overflow-hidden">

                {{-- Top Header Bar --}}
                <header class="bg-white/80 backdrop-blur-md border-b border-[#FFD1DC]/40 h-16 flex items-center justify-between px-4 sm:px-6 flex-shrink-0">
                    {{-- Mobile: Hamburger --}}
                    <button
                        @click="sidebarOpen = true"
                        class="lg:hidden p-2 rounded-md text-slate-500 hover:text-slate-700 hover:bg-[#FFF0F6] transition-colors focus:outline-none focus:ring-2 focus:ring-[#E86FA3]"
                        aria-label="Buka sidebar"
                    >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    {{-- Page Title (optional slot) --}}
                    <div class="hidden lg:block">
                        @isset($pageTitle)
                            <h1 class="text-lg font-semibold text-gray-800">{{ $pageTitle }}</h1>
                        @endisset
                    </div>

                    {{-- Admin Account --}}
                    <div class="flex items-center gap-4 ml-auto">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name ?? 'Admin' }}</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>

                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button
                                @click="open = !open"
                                class="w-9 h-9 bg-gradient-to-br from-[#F8BBD0] to-[#89CFF0] rounded-full flex items-center justify-center hover:from-[#F8A0C2] hover:to-[#7CC1F7] transition-colors focus:outline-none focus:ring-2 focus:ring-[#E86FA3]"
                                aria-haspopup="true"
                                :aria-expanded="open"
                                aria-label="Menu akun admin"
                            >
                                <span class="text-pink-600 font-semibold text-sm">
                                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                                </span>
                            </button>

                            <div
                                x-show="open"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50"
                                role="menu"
                            >
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors text-left" role="menuitem">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </header>

                {{-- Page Content --}}
                <main class="flex-1 overflow-y-auto p-4 sm:p-6">
                    {{-- Toast Notifications --}}
                    <x-toast />

                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
