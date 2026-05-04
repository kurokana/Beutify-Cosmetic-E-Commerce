@php
    use App\Models\Category;
    use App\Models\CartItem;
    use Illuminate\Support\Facades\Cache;

    $categories = Cache::remember('categories', 3600, fn () => Category::orderBy('name')->get());

    $cartCount = 0;
    if (auth()->check()) {
        $cartCount = CartItem::where('user_id', auth()->id())->sum('quantity');
    }
@endphp

<nav x-data="{ open: false, categoryOpen: false, accountOpen: false }" class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">

    {{-- Desktop & Tablet Navigation --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo / Brand --}}
            <div class="flex-shrink-0">
                <a href="{{ route('catalog.index') }}" class="flex items-center gap-2">
                    <span class="text-xl font-bold text-pink-600 tracking-tight">
                        {{ config('app.name', 'Kosmetik Store') }}
                    </span>
                </a>
            </div>

            {{-- Desktop: Category + Search --}}
            <div class="hidden lg:flex items-center gap-4 flex-1 mx-8">

                {{-- Category Dropdown --}}
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <button
                        class="flex items-center gap-1 text-sm font-medium text-gray-700 hover:text-pink-600 transition-colors py-2 px-3 rounded-md hover:bg-pink-50"
                        @click="open = !open"
                        aria-haspopup="true"
                        :aria-expanded="open"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Kategori
                        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div
                        x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-1"
                        class="absolute left-0 top-full mt-1 w-56 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50"
                        role="menu"
                    >
                        <a href="{{ route('catalog.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors" role="menuitem">
                            Semua Produk
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        @foreach ($categories as $category)
                            <a
                                href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors"
                                role="menuitem"
                            >
                                {{ $category->name }}
                            </a>
                        @endforeach
                        @if ($categories->isEmpty())
                            <span class="block px-4 py-2 text-sm text-gray-400 italic">Belum ada kategori</span>
                        @endif
                    </div>
                </div>

                {{-- Search Bar --}}
                <form action="{{ route('search') }}" method="GET" class="flex-1 max-w-lg" role="search">
                    <div class="relative">
                        <input
                            type="search"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari produk, merek, kategori..."
                            class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent bg-gray-50 transition"
                            aria-label="Cari produk"
                        >
                        <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-pink-500 transition-colors" aria-label="Cari">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Desktop: Icons (Cart, Wishlist, Account) --}}
            <div class="hidden lg:flex items-center gap-2">

                {{-- Wishlist Icon --}}
                @auth
                    <a href="{{ route('wishlist.index') }}" class="relative p-2 text-gray-600 hover:text-pink-600 transition-colors rounded-full hover:bg-pink-50" aria-label="Wishlist">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>
                @endauth

                {{-- Cart Icon with Badge --}}
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-pink-600 transition-colors rounded-full hover:bg-pink-50" aria-label="Keranjang belanja">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-pink-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 leading-none" aria-label="{{ $cartCount }} item di keranjang">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- Account Dropdown --}}
                @auth
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                        <button
                            @click="open = !open"
                            class="flex items-center gap-2 p-2 text-gray-600 hover:text-pink-600 transition-colors rounded-full hover:bg-pink-50"
                            aria-haspopup="true"
                            :aria-expanded="open"
                            aria-label="Menu akun"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </button>

                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 top-full mt-2 w-52 bg-white rounded-lg shadow-lg border border-gray-100 py-2 z-50"
                            role="menu"
                        >
                            <div class="px-4 py-2 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('customer.profile.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors" role="menuitem">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Profil Saya
                            </a>
                            <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors" role="menuitem">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Pesanan Saya
                            </a>
                            <a href="{{ route('wishlist.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 transition-colors" role="menuitem">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                                Wishlist
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
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
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-pink-600 transition-colors px-3 py-2 rounded-md hover:bg-pink-50">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="text-sm font-medium bg-pink-600 text-white px-4 py-2 rounded-full hover:bg-pink-700 transition-colors">
                        Daftar
                    </a>
                @endauth
            </div>

            {{-- Mobile: Cart + Hamburger --}}
            <div class="flex items-center gap-2 lg:hidden">
                {{-- Mobile Cart Icon --}}
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-pink-600 transition-colors" aria-label="Keranjang belanja">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    @if ($cartCount > 0)
                        <span class="absolute -top-1 -right-1 bg-pink-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 leading-none" aria-label="{{ $cartCount }} item di keranjang">
                            {{ $cartCount > 99 ? '99+' : $cartCount }}
                        </span>
                    @endif
                </a>

                {{-- Hamburger Button --}}
                <button
                    @click="open = !open"
                    class="p-2 rounded-md text-gray-600 hover:text-pink-600 hover:bg-pink-50 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-400"
                    :aria-expanded="open"
                    aria-label="Buka menu navigasi"
                >
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Tablet: Search Bar (visible on md, hidden on lg) --}}
        <div class="hidden md:block lg:hidden pb-3">
            <form action="{{ route('search') }}" method="GET" role="search">
                <div class="relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari produk, merek, kategori..."
                        class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent bg-gray-50 transition"
                        aria-label="Cari produk"
                    >
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-pink-500 transition-colors" aria-label="Cari">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-gray-100 bg-white"
        id="mobile-menu"
    >
        {{-- Mobile Search --}}
        <div class="px-4 py-3 md:hidden">
            <form action="{{ route('search') }}" method="GET" role="search">
                <div class="relative">
                    <input
                        type="search"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari produk..."
                        class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-400 bg-gray-50"
                        aria-label="Cari produk"
                    >
                    <button type="submit" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" aria-label="Cari">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        {{-- Mobile Categories --}}
        <div class="px-4 py-2">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kategori</p>
            <div class="space-y-1">
                <a href="{{ route('catalog.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-md transition-colors">
                    Semua Produk
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-md transition-colors">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-100 mx-4 my-2"></div>

        {{-- Mobile Account Links --}}
        @auth
            <div class="px-4 py-2">
                <div class="flex items-center gap-3 px-3 py-2 mb-2">
                    <div class="w-8 h-8 bg-pink-100 rounded-full flex items-center justify-center">
                        <span class="text-pink-600 font-semibold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <a href="{{ route('customer.profile.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-md transition-colors">
                        Profil Saya
                    </a>
                    <a href="{{ route('orders.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-md transition-colors">
                        Pesanan Saya
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="block px-3 py-2 text-sm text-gray-700 hover:bg-pink-50 hover:text-pink-600 rounded-md transition-colors">
                        Wishlist
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-md transition-colors">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="px-4 py-3 flex gap-3">
                <a href="{{ route('login') }}" class="flex-1 text-center py-2 text-sm font-medium text-pink-600 border border-pink-600 rounded-full hover:bg-pink-50 transition-colors">
                    Masuk
                </a>
                <a href="{{ route('register') }}" class="flex-1 text-center py-2 text-sm font-medium bg-pink-600 text-white rounded-full hover:bg-pink-700 transition-colors">
                    Daftar
                </a>
            </div>
        @endauth

        <div class="pb-3"></div>
    </div>
</nav>
