<nav x-data="{ open: false }" class="sticky top-0 z-50 glass-dark shadow-[0_8px_30px_rgba(0,0,0,0.4)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between min-h-[88px] gap-4">

            {{-- Logo Beutify --}}
            <a href="/" class="flex items-center gap-3 group">
                <div class="relative">
                    <!-- Ikon bunga rose gold -->
                    <svg class="w-14 h-14" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="15" r="6" fill="#C8956C" opacity="0.6"/>
                        <circle cx="14" cy="22" r="6" fill="#D4A87C" opacity="0.4"/>
                        <circle cx="26" cy="22" r="6" fill="#D4A87C" opacity="0.4"/>
                        <circle cx="14" cy="22" r="6" fill="#A87A55" opacity="0.3"/>
                        <circle cx="26" cy="22" r="6" fill="#A87A55" opacity="0.3"/>
                        <circle cx="20" cy="20" r="3" fill="#C8956C"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <h1 class="text-3xl font-extrabold tracking-tight text-gold text-glow">
                        Beutify
                    </h1>
                    <p class="text-xs uppercase tracking-[0.3em] text-warm-muted font-bold">
                        Beauty Store
                    </p>
                </div>
            </a>

            {{-- Search --}}
            <div class="hidden lg:flex flex-1 max-w-xl mx-6">
                <form action="{{ route('catalog.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari skincare, makeup, serum..."
                            class="w-full h-12 pl-5 pr-14 rounded-full bg-dark-tertiary border border-border-subtle text-sm text-warm-white placeholder:text-warm-muted focus:border-gold focus:ring-4 focus:ring-gold/20 transition"
                        >
                        <button
                            type="submit"
                            class="absolute right-1.5 top-1.5 w-9 h-9 rounded-full bg-gold text-dark-primary flex items-center justify-center hover:bg-gold-light transition"
                        >
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-4.35-4.35m1.6-5.15a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Menu (Wishlist, Cart, Profile) --}}
            <div class="hidden sm:flex items-center gap-2">

                @auth
                    <a href="{{ route('wishlist.index') }}"
                        class="relative w-11 h-11 rounded-full bg-dark-tertiary border border-border-subtle text-gold flex items-center justify-center hover:bg-dark-elevated hover:border-gold/40 hover:-translate-y-0.5 transition"
                        title="Wishlist">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </a>

                    <a href="{{ route('cart.index') }}"
                        class="relative w-11 h-11 rounded-full bg-dark-tertiary border border-border-subtle text-gold-light flex items-center justify-center hover:bg-dark-elevated hover:border-gold/40 hover:-translate-y-0.5 transition"
                        title="Keranjang">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </a>

                    {{-- Profile Dropdown --}}
                    <x-dropdown align="right" width="56">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center gap-3 pl-2 pr-4 py-2 rounded-full border border-border-subtle bg-dark-secondary hover:bg-dark-tertiary transition">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-dark-primary font-extrabold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div class="hidden lg:block text-left leading-tight">
                                    <p class="text-sm font-bold text-warm-white max-w-[110px] truncate">
                                        {{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[11px] text-warm-muted">Customer</p>
                                </div>
                                <svg class="w-4 h-4 text-warm-muted" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-border-subtle">
                                <p class="text-sm font-bold text-warm-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-warm-muted truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('dashboard')">Dashboard</x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                            <x-dropdown-link :href="route('orders.index')">Pesanan Saya</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-full text-sm font-extrabold text-warm-gray hover:text-gold transition">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-full btn-gold text-sm font-extrabold rounded-full">Register</a>
                @endauth
            </div>

            {{-- Mobile Hamburger --}}
            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center w-11 h-11 rounded-full bg-dark-tertiary border border-border-subtle text-gold hover:bg-dark-elevated focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Desktop Bottom Menu --}}
        <div class="hidden lg:flex items-center justify-center gap-8 h-12 border-t border-border-subtle text-sm font-bold text-warm-gray">
            <a href="{{ route('dashboard') }}" class="transition {{ request()->routeIs('dashboard') ? 'text-gold' : 'hover:text-gold' }}">
                Home
            </a>
            <a href="{{ route('catalog.index') }}" class="transition {{ request()->routeIs('catalog.*') ? 'text-gold' : 'hover:text-gold' }}">
                Katalog
            </a>
            @auth
                <a href="{{ route('orders.index') }}" class="transition {{ request()->routeIs('orders.*') ? 'text-gold' : 'hover:text-gold' }}">
                    Order
                </a>
            @endauth
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-dark-secondary border-t border-border-subtle">
        <div class="px-4 pt-4 pb-3">
            <form action="{{ route('catalog.index') }}" method="GET">
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari produk..." class="w-full h-11 pl-4 pr-12 rounded-full bg-dark-tertiary border border-border-subtle text-sm text-warm-white placeholder:text-warm-muted focus:border-gold focus:ring-4 focus:ring-gold/20 transition">
                    <button type="submit" class="absolute right-1.5 top-1.5 w-8 h-8 rounded-full bg-gold text-dark-primary flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.6-5.15a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">Home</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('catalog.index')" :active="request()->routeIs('catalog.*')">Katalog</x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">Order</x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('login')">Login</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">Register</x-responsive-nav-link>
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-4 border-t border-border-subtle bg-dark-tertiary/50">
                <div class="px-4 flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-dark-primary font-extrabold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-base text-warm-white">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-warm-muted">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @endauth
    </div>
</nav>