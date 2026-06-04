<x-app-layout>
    <div class="py-12 bg-dark-primary min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Welcome (sama dengan dashboard) --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-border-subtle pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-gold text-glow">Selamat Datang</span>
                        <span class="text-warm-white">di Beutify</span>
                    </h1>
                    <p class="text-warm-gray text-sm mt-1 font-medium">Temukan produk kecantikan favoritmu</p>
                </div>
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-dark-secondary border border-gold/30 text-gold text-[11px] font-black uppercase tracking-widest rounded-xl shadow-gold-sm">
                        #BeautyJourney
                    </span>
                </div>
            </div>

            {{-- POSTER / BANNER UTAMA (ganti dengan gambar sesuai keinginan) --}}
            <div class="mb-12 rounded-2xl overflow-hidden shadow-dark-card border border-border-subtle">
                <img 
                    src="{{ asset('images/posterbeutify.jpg') }}" 
                    alt="Welcome to Beutify" 
                    class="w-full h-auto object-cover"
                >
                {{-- Jika ingin dinamis, bisa gunakan variabel $posterUrl dari controller --}}
            </div>

            {{-- PRODUK TERBARU --}}
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-warm-white">
                            <span class="text-gold">Produk</span> 
                            <span class="text-warm-white">Terbaru</span>
                        </h2>
                        <p class="text-warm-muted text-xs font-medium mt-1">Koleksi terbaru yang siap memanjakan kulitmu</p>
                    </div>
                    <a href="{{ route('catalog.index', ['sort' => 'latest']) }}" 
                       class="text-sm font-semibold text-gold hover:text-gold-light transition flex items-center gap-1">
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
                    <div class="text-center py-12 bg-dark-secondary rounded-3xl border border-border-subtle shadow-sm">
                        <p class="text-warm-muted font-medium">Belum ada produk terbaru.</p>
                    </div>
                @endif
            </div>

            {{-- PRODUK TERLARIS (BEST SELLER) --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-warm-white">
                            <span class="text-gold">Produk</span> 
                            <span class="text-warm-white">Terlaris</span>
                        </h2>
                        <p class="text-warm-muted text-xs font-medium mt-1">Paling diminati oleh para Beautifier</p>
                    </div>
                    <a href="{{ route('catalog.index', ['sort' => 'best_seller']) }}" 
                       class="text-sm font-semibold text-gold hover:text-gold-light transition flex items-center gap-1">
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
                    <div class="text-center py-12 bg-dark-secondary rounded-3xl border border-border-subtle shadow-sm">
                        <p class="text-warm-muted font-medium">Belum ada data produk terlaris.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>