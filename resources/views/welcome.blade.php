<x-app-layout>
    <div class="py-12 bg-transparent min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header Welcome --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-[#FFD1DC]/40 pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-[#89CFF0]">Selamat Datang</span>
                        <span class="text-[#E86FA3]">di Beutify</span>
                    </h1>
                    <p class="text-slate-400 text-sm mt-1 font-medium">Temukan produk kecantikan favoritmu</p>
                </div>
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-white border border-[#FFD1DC] text-[#E86FA3] text-[11px] font-black uppercase tracking-widest rounded-xl shadow-sm">
                        #BeautyJourney
                    </span>
                </div>
            </div>

            {{-- POSTER / BANNER UTAMA --}}
            <div class="mb-12 rounded-2xl overflow-hidden shadow-xl border border-[#FFD1DC]/40">
                <img 
                    src="{{ asset('images/dashboard-poster.jpg') }}" 
                    alt="Welcome to Beutify" 
                    class="w-full h-auto object-cover"
                >
                {{-- Jika ingin dinamis, bisa gunakan variabel $posterUrl dari controller --}}
            </div>

            {{-- PRODUK TERBARU --}}
            <div class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-gray-800">
                            <span class="text-[#89CFF0]">Produk</span> 
                            <span class="text-[#E86FA3]">Terbaru</span>
                        </h2>
                        <p class="text-slate-400 text-xs font-medium mt-1">Koleksi terbaru yang siap memanjakan kulitmu</p>
                    </div>
                    <a href="{{ route('catalog.index', ['sort' => 'latest']) }}" 
                       class="text-sm font-semibold text-[#E86FA3] hover:text-[#d45a92] transition flex items-center gap-1">
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
                    <div class="text-center py-12 bg-white rounded-3xl border border-[#FFD1DC]/50 shadow-sm">
                        <p class="text-slate-400 font-medium">Belum ada produk terbaru.</p>
                    </div>
                @endif
            </div>

            {{-- PRODUK TERLARIS --}}
            <div>
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-black text-gray-800">
                            <span class="text-[#89CFF0]">Produk</span> 
                            <span class="text-[#E86FA3]">Terlaris</span>
                        </h2>
                        <p class="text-slate-400 text-xs font-medium mt-1">Paling diminati oleh para Beautifier</p>
                    </div>
                    <a href="{{ route('catalog.index', ['sort' => 'best_seller']) }}" 
                       class="text-sm font-semibold text-[#E86FA3] hover:text-[#d45a92] transition flex items-center gap-1">
                        Lihat Semua
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>

                @if(isset($bestSellerProducts) && $bestSellerProducts->isNotEmpty())
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                        @foreach($bestSellerProducts as $product)
                            @php
                                $badge = $loop->first ? '#1 Terlaris' : null;
                            @endphp
                            @include('partials.product-card', ['product' => $product, 'badge' => $badge])
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-3xl border border-[#FFD1DC]/50 shadow-sm">
                        <p class="text-slate-400 font-medium">Belum ada data produk terlaris.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
