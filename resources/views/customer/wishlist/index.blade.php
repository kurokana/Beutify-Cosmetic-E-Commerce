<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
            <a href="{{ route('catalog.index') }}" class="text-warm-muted hover:text-gold transition">Katalog</a>
            <span class="text-warm-muted">/</span>
            <span class="text-warm-white">Wishlist Saya</span>
        </nav>
    </x-slot>

    <div class="py-12 bg-dark-primary min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-6 p-4 bg-dark-secondary border-l-4 border-[#89CFF0] rounded-r-xl shadow-sm text-warm-gray text-[11px] font-bold uppercase tracking-wide">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Header Section --}}
            <div class="flex items-end justify-between mb-8 border-b border-border-subtle pb-4">
                <h1 class="text-2xl font-bold tracking-tight">
                    <span class="text-gold-light">Wishlist</span> 
                    <span class="text-gold">Favorit</span>
                </h1>
                
                <span class="px-3 py-1 bg-dark-secondary border border-gold/30 text-gold text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                    {{ $wishlists->count() }} Produk Tersimpan
                </span>
            </div>

            @if ($wishlists->isEmpty())
                {{-- Empty State --}}
                <div class="bg-dark-secondary rounded-[2.5rem] border border-border-subtle shadow-sm p-16 text-center">
                    <div class="w-20 h-20 bg-dark-primary rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-warm-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-warm-white mb-2">Wishlist Anda masih kosong</h2>
                    <a href="{{ route('catalog.index') }}"
                        class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gold to-gold-light text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg">
                        Cari Produk
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach ($wishlists as $wishlist)
                        @php
                            $product = $wishlist->product;
                            $primaryImage = $product?->images?->firstWhere('is_primary', true) ?? $product?->images?->first();
                            $rating = (float) ($product?->average_rating ?? 0);
                        @endphp

                        @if ($product)
                            {{-- Card Start --}}
                            <div class="group relative flex flex-col overflow-hidden rounded-[1.75rem] bg-dark-secondary border border-border-subtle shadow-[0_14px_35px_rgba(248,187,208,0.18)] hover:shadow-[0_20px_45px_rgba(137,207,240,0.22)] hover:-translate-y-1.5 transition-all duration-300">
                                
                                {{-- Decorative soft gradient (Persis Catalog) --}}
                                <div class="absolute inset-x-0 top-0 h-28 bg-gradient-to-br from-[#FFF1F6] via-white to-[#EAF8FF] pointer-events-none"></div>

                                {{-- Wishlist toggle (Tombol Hapus - Warna Pink Pekat) --}}
                                <form action="{{ route('wishlist.toggle', $product) }}" method="POST" class="absolute top-3 right-3 z-20">
                                    @csrf
                                    <button type="submit"
                                        class="w-9 h-9 rounded-full flex items-center justify-center border transition-all duration-200 shadow-sm bg-gold text-white border-gold hover:bg-gold-dark"
                                        title="Hapus dari Wishlist">
                                        <svg class="w-4.5 h-4.5" fill="currentColor" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </form>

                                {{-- Image Section --}}
                                <a href="{{ route('catalog.show', $product->slug) }}" class="relative z-10 block">
                                    <div class="relative mx-3 mt-3 aspect-square overflow-hidden rounded-[1.5rem] bg-gradient-to-br from-[#FFF8FB] to-[#EAF8FF] border border-white">
                                        <img src="{{ $primaryImage ? Storage::url($primaryImage->image_path) : asset('images/placeholder.png') }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    </div>
                                </a>

                                {{-- Info Section --}}
                                <div class="p-4 flex-1 flex flex-col relative z-10">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <p class="text-[11px] text-gold font-extrabold uppercase tracking-wide truncate">
                                            {{ $product->brand?->name ?? 'Beauty Brand' }}
                                        </p>
                                        @if ($product->stock > 0)
                                            <span class="shrink-0 w-2 h-2 rounded-full bg-[#89CFF0]"></span>
                                        @endif
                                    </div>

                                    <h3 class="text-sm font-bold text-warm-white line-clamp-2 leading-snug min-h-[2.5rem] group-hover:text-gold transition-colors">
                                        {{ $product->name }}
                                    </h3>

                                    {{-- Star rating (Kuning) --}}
                                    <div class="flex items-center gap-1.5 mt-3">
                                        <div class="flex text-yellow-400 text-xs">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span>{{ $i <= floor($rating) ? '★' : '☆' }}</span>
                                            @endfor
                                        </div>
                                        <span class="text-[11px] text-warm-muted font-semibold">{{ number_format($rating, 1) }}</span>
                                    </div>

                                    {{-- Price & Stock --}}
                                    <div class="mt-3">
                                        <p class="text-base font-extrabold text-warm-white">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        <p class="mt-1 text-[11px] {{ $product->stock <= 0 ? 'text-red-500 font-bold' : 'text-warm-muted font-semibold' }}">
                                            {{ $product->stock <= 0 ? 'Stok Habis' : 'Stok tersedia' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Add to cart button (Gradasi Pink ke Biru) --}}
                                <div class="relative z-10 px-4 pb-4 mt-auto">
                                    @if ($product->stock > 0)
                                        <form action="{{ route('wishlist.moveToCart', $product) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full py-3 bg-gradient-to-r from-gold to-gold-light text-white text-xs font-extrabold rounded-full shadow-lg shadow-gold/25 hover:shadow-gold/25 hover:-translate-y-0.5 transition-all duration-200 flex items-center justify-center gap-2">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Pindah ke Keranjang
                                            </button>
                                        </form>
                                    @else
                                        <button disabled class="w-full py-3 bg-dark-tertiary text-warm-muted text-xs font-extrabold rounded-full cursor-not-allowed uppercase tracking-widest">
                                            Stok Habis
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>