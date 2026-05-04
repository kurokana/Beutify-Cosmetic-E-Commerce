<x-app-layout>
    {{-- ═══════════════════════════════════════════════════════════════════════
         HERO / BANNER SECTION
    ════════════════════════════════════════════════════════════════════════ --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-pink-500 via-fuchsia-500 to-purple-600">
        {{-- Decorative blobs --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-16 -left-16 w-72 h-72 bg-pink-300/20 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/3 w-48 h-48 bg-fuchsia-300/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
            <div class="flex flex-col lg:flex-row items-center gap-10">
                {{-- Text content --}}
                <div class="flex-1 text-center lg:text-left">
                    <span class="inline-block bg-white/20 text-white text-xs font-semibold uppercase tracking-widest px-3 py-1 rounded-full mb-4">
                        ✨ Koleksi Terbaru 2025
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight mb-4">
                        Tampil Cantik<br>
                        <span class="text-yellow-300">Setiap Hari</span>
                    </h1>
                    <p class="text-pink-100 text-lg max-w-md mx-auto lg:mx-0 mb-8 leading-relaxed">
                        Temukan ribuan produk kosmetik premium dari merek-merek ternama dunia. Kualitas terjamin, harga terbaik.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        <a href="{{ route('catalog.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white text-pink-600 font-semibold rounded-full shadow-lg hover:bg-pink-50 transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            Belanja Sekarang
                        </a>
                        <a href="{{ route('catalog.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-transparent text-white font-semibold rounded-full border-2 border-white/50 hover:border-white hover:bg-white/10 transition-all duration-200">
                            Lihat Katalog
                        </a>
                    </div>
                </div>

                {{-- Decorative cosmetics illustration --}}
                <div class="flex-shrink-0 hidden lg:flex items-center justify-center">
                    <div class="relative w-72 h-72">
                        {{-- Outer ring --}}
                        <div class="absolute inset-0 rounded-full border-4 border-white/20 animate-pulse"></div>
                        {{-- Inner circle --}}
                        <div class="absolute inset-8 rounded-full bg-white/10 backdrop-blur-sm flex items-center justify-center">
                            <div class="text-center">
                                <div class="text-6xl mb-2">💄</div>
                                <p class="text-white font-semibold text-sm">Premium Beauty</p>
                                <p class="text-pink-200 text-xs">100% Original</p>
                            </div>
                        </div>
                        {{-- Floating badges --}}
                        <div class="absolute -top-2 -right-2 bg-yellow-400 text-yellow-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                            🌟 Terpercaya
                        </div>
                        <div class="absolute -bottom-2 -left-2 bg-green-400 text-green-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                            ✅ Original
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats bar --}}
            <div class="mt-12 grid grid-cols-3 gap-4 max-w-lg mx-auto lg:mx-0">
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">500+</p>
                    <p class="text-pink-200 text-xs">Produk</p>
                </div>
                <div class="text-center border-x border-white/20">
                    <p class="text-2xl font-bold text-white">50+</p>
                    <p class="text-pink-200 text-xs">Merek</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-white">10K+</p>
                    <p class="text-pink-200 text-xs">Pelanggan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════════
         FEATURED BRANDS SECTION
    ════════════════════════════════════════════════════════════════════════ --}}
    @if ($featuredBrands->isNotEmpty())
    <section class="py-12 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Merek Unggulan</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Produk original dari merek-merek terpercaya</p>
                </div>
                <a href="{{ route('catalog.index') }}"
                    class="text-sm text-pink-600 font-medium hover:text-pink-700 flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            {{-- Scrollable brand row --}}
            <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide snap-x snap-mandatory">
                @foreach ($featuredBrands as $brand)
                    <a href="{{ route('catalog.index', ['brand_id' => $brand->id]) }}"
                        class="snap-start flex-shrink-0 flex flex-col items-center gap-2 group">
                        <div class="w-20 h-20 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center overflow-hidden
                                    group-hover:border-pink-300 group-hover:shadow-md transition-all duration-200">
                            @if ($brand->logo_path)
                                <img
                                    src="{{ Storage::url($brand->logo_path) }}"
                                    alt="{{ $brand->name }}"
                                    class="w-14 h-14 object-contain"
                                    loading="lazy"
                                >
                            @else
                                <span class="text-2xl font-bold text-pink-400">
                                    {{ strtoupper(substr($brand->name, 0, 2)) }}
                                </span>
                            @endif
                        </div>
                        <span class="text-xs text-gray-600 font-medium text-center max-w-[80px] truncate group-hover:text-pink-600 transition-colors">
                            {{ $brand->name }}
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════
         LATEST PRODUCTS SECTION
    ════════════════════════════════════════════════════════════════════════ --}}
    <section class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Produk Terbaru</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Koleksi terkini yang baru hadir</p>
                </div>
                <a href="{{ route('catalog.index', ['sort' => 'latest']) }}"
                    class="text-sm text-pink-600 font-medium hover:text-pink-700 flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if ($latestProducts->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <svg class="mx-auto w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4" />
                    </svg>
                    <p>Belum ada produk tersedia.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($latestProducts as $product)
                        @php $badge = null; @endphp
                        @include('partials.product-card')
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════════
         BEST SELLERS SECTION
    ════════════════════════════════════════════════════════════════════════ --}}
    <section class="py-12 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Produk Terlaris</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Pilihan favorit para pelanggan kami</p>
                </div>
                <a href="{{ route('catalog.index') }}"
                    class="text-sm text-pink-600 font-medium hover:text-pink-700 flex items-center gap-1">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            @if ($bestSellers->isEmpty())
                <div class="text-center py-12 text-gray-400">
                    <svg class="mx-auto w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <p>Belum ada data penjualan.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($bestSellers as $index => $product)
                        @php $badge = $index < 3 ? '#' . ($index + 1) . ' Terlaris' : null; @endphp
                        @include('partials.product-card')
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ═══════════════════════════════════════════════════════════════════════
         PROMO BANNER STRIP
    ════════════════════════════════════════════════════════════════════════ --}}
    <section class="py-10 bg-gradient-to-r from-fuchsia-600 to-pink-500">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div>
                    <h3 class="text-xl font-bold text-white">Daftar Sekarang & Dapatkan Promo Eksklusif!</h3>
                    <p class="text-pink-100 text-sm mt-1">Nikmati penawaran spesial untuk member baru. Gratis ongkir untuk pembelian pertama.</p>
                </div>
                @guest
                    <a href="{{ route('register') }}"
                        class="flex-shrink-0 inline-flex items-center gap-2 px-7 py-3 bg-white text-pink-600 font-semibold rounded-full shadow hover:bg-pink-50 transition-all duration-200 hover:-translate-y-0.5">
                        Daftar Gratis
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @else
                    <a href="{{ route('catalog.index') }}"
                        class="flex-shrink-0 inline-flex items-center gap-2 px-7 py-3 bg-white text-pink-600 font-semibold rounded-full shadow hover:bg-pink-50 transition-all duration-200 hover:-translate-y-0.5">
                        Belanja Sekarang
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                @endguest
            </div>
        </div>
    </section>
</x-app-layout>
