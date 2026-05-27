<x-app-layout>
    <div class="py-10 sm:py-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Hero / Header Welcome --}}
            <div class="relative overflow-hidden rounded-[28px] border border-[#fbcfe8]/70 bg-[linear-gradient(135deg,#fff1f7_0%,#ffffff_56%,#ecfeff_100%)] p-6 sm:p-8 shadow-[0_24px_60px_-24px_rgba(244,114,182,0.35)] mb-8">
                <div class="absolute -top-10 -left-10 h-28 w-28 rounded-full bg-[#f9a8d4]/30 blur-2xl"></div>
                <div class="absolute bottom-0 right-0 h-24 w-24 rounded-full bg-[#99f6e4]/30 blur-2xl"></div>
                <div class="relative flex flex-col lg:flex-row lg:items-end justify-between gap-4">
                    <div class="max-w-2xl">
                        <p class="inline-flex items-center rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.3em] text-[#db2777] border border-[#fbcfe8]/70">Beauty Journey</p>
                        <h1 class="mt-4 text-3xl sm:text-4xl font-black tracking-tight text-slate-900">
                            <span class="text-[#14b8a6]">Selamat Datang</span>
                            <span class="text-[#db2777]">di Beutify</span>
                        </h1>
                        <p class="mt-3 text-sm sm:text-base text-slate-600 leading-7">Temukan ritual skincare, serum, dan produk favoritmu dengan tampilan yang lebih segar dan modern.</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('catalog.index') }}" class="inline-flex items-center justify-center rounded-full bg-[linear-gradient(135deg,#ec4899_0%,#14b8a6_100%)] px-5 py-2.5 text-sm font-extrabold text-white shadow-[0_18px_30px_-18px_rgba(20,184,166,0.55)]">
                            Jelajahi Katalog
                        </a>
                        <a href="{{ route('wishlist.index') }}" class="inline-flex items-center justify-center rounded-full border border-[#fbcfe8] bg-white px-5 py-2.5 text-sm font-bold text-[#db2777]">
                            Wishlist
                        </a>
                    </div>
                </div>
            </div>

            {{-- POSTER / BANNER UTAMA --}}
            <div class="mb-12 rounded-[24px] overflow-hidden shadow-[0_22px_55px_-24px_rgba(244,114,182,0.28)] border border-[#fbcfe8]/70 bg-white">
                <img
                    src="{{ asset('images/posterbeutify.jpg') }}"
                    alt="Welcome to Beutify"
                    class="w-full h-auto object-cover"
                >
            </div>

            {{-- PRODUK TERBARU --}}
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6 gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            <span class="text-[#14b8a6]">Produk</span>
                            <span class="text-[#db2777]">Terbaru</span>
                        </h2>
                        <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">Koleksi terbaru yang siap memanjakan kulitmu</p>
                    </div>
                    <a href="{{ route('catalog.index', ['sort' => 'latest']) }}"
                       class="text-sm font-bold text-[#db2777] hover:text-[#be185d] transition flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if(isset($latestProducts) && $latestProducts->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @foreach($latestProducts as $product)
                            @include('partials.product-card', ['product' => $product, 'badge' => null])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-[linear-gradient(180deg,#ffffff_0%,#fff7fb_100%)] rounded-[24px] border border-[#fbcfe8]/70 shadow-[0_18px_40px_-24px_rgba(244,114,182,0.26)]">
                        <p class="text-slate-500 font-medium">Belum ada produk terbaru.</p>
                    </div>
                @endif
            </div>

            {{-- PRODUK TERLARIS (BEST SELLER) --}}
            <div>
                <div class="flex items-center justify-between mb-6 gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-900">
                            <span class="text-[#14b8a6]">Produk</span>
                            <span class="text-[#db2777]">Terlaris</span>
                        </h2>
                        <p class="text-slate-500 text-xs sm:text-sm font-medium mt-1">Paling diminati oleh para Beautifier</p>
                    </div>
                    <a href="{{ route('catalog.index', ['sort' => 'best_seller']) }}"
                       class="text-sm font-bold text-[#db2777] hover:text-[#be185d] transition flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if(isset($bestSellers) && $bestSellers->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @foreach($bestSellers as $product)
                            @php
                                $badge = $loop->first ? '#1 Terlaris' : null;
                            @endphp
                            @include('partials.product-card', ['product' => $product, 'badge' => $badge])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-[linear-gradient(180deg,#ffffff_0%,#ecfeff_100%)] rounded-[24px] border border-[#99f6e4]/70 shadow-[0_18px_40px_-24px_rgba(20,184,166,0.26)]">
                        <p class="text-slate-500 font-medium">Belum ada data produk terlaris.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>