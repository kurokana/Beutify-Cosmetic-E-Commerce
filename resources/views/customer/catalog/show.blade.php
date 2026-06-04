<x-app-layout>
    <style>
        .font-display { font-family: 'Playfair Display', Georgia, serif; }
        /* Adjusted for dark luxe theme */
        .beauty-soft-shadow { box-shadow: 0 14px 35px rgba(0, 0, 0, 0.3); }
        .beauty-gold-shadow { box-shadow: 0 12px 30px rgba(200, 149, 108, 0.1); }
    </style>

    <x-slot name="header">
        <nav class="flex flex-wrap items-center gap-2 text-sm text-warm-gray">
            <a href="{{ route('catalog.index') }}" class="font-bold hover:text-gold transition">Katalog</a>
            <span>/</span>
            <a href="{{ route('catalog.index', ['category_id' => $product->category_id]) }}" class="font-bold hover:text-gold-light transition">
                {{ $product->category?->name }}
            </a>
            <span>/</span>
            <span class="text-gold font-bold truncate max-w-xs">{{ $product->name }}</span>
        </nav>
    </x-slot>

    <div class="min-h-screen bg-dark-primary py-8">
        
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- PRODUCT DETAIL CARD --}}
            <div class="overflow-hidden rounded-[2rem] bg-dark-secondary border border-border-subtle beauty-soft-shadow">
                <div class="grid lg:grid-cols-2 gap-0">

                    {{-- IMAGE GALLERY --}}
                    <div class="p-6 sm:p-10 bg-dark-secondary" x-data="imageGallery()">
                        <div class="relative aspect-square overflow-hidden rounded-[1.5rem] bg-dark-tertiary border border-border-subtle shadow-sm p-2">
                            <img :src="activeImage" alt="{{ $product->name }}" class="w-full h-full object-contain rounded-xl">

                            <div class="absolute left-5 top-5 rounded-full bg-dark-secondary/90 border border-gold/30 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-gold shadow-sm">
                                {{ $product->category?->name ?? 'Beauty' }}
                            </div>

                            @if ($product->stock <= 0)
                                <div class="absolute right-5 top-5 rounded-full bg-red-500/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-red-400 border border-red-500/20 shadow-sm">Habis</div>
                            @elseif ($product->stock <= 5)
                                <div class="absolute right-5 top-5 rounded-full bg-orange-500/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-widest text-orange-400 border border-orange-500/20 shadow-sm">Terbatas</div>
                            @endif
                        </div>

                        @if ($product->images->count() > 1)
                            <div class="mt-4 flex gap-3 overflow-x-auto pb-2 custom-scrollbar">
                                @foreach ($product->images as $image)
                                    <button type="button" @click="setImage('{{ Storage::url($image->image_path) }}')"
                                        :class="activeImage === '{{ Storage::url($image->image_path) }}' ? 'ring-2 ring-gold' : 'ring-1 ring-border-subtle hover:ring-gold/50'"
                                        class="shrink-0 h-20 w-20 p-1 overflow-hidden rounded-xl bg-dark-tertiary transition">
                                        <img src="{{ Storage::url($image->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg" loading="lazy">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- PRODUCT INFO --}}
                    <div class="p-6 sm:p-10 lg:border-l border-border-subtle bg-dark-elevated/30">
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <a href="{{ route('catalog.index', ['brand_id' => $product->brand_id]) }}" class="text-[10px] font-black uppercase tracking-widest text-gold hover:text-gold-light transition">
                                {{ $product->brand?->name ?? 'Beauty Brand' }}
                            </a>
                        </div>

                        <h1 class="text-3xl sm:text-4xl font-black text-warm-white tracking-tight leading-snug">
                            {{ $product->name }}
                        </h1>

                        @php
                            $rating = (float) $product->average_rating;
                            $reviewCount = $product->reviews->count();
                        @endphp

                        <div class="mt-4 flex items-center gap-2">
                            <div class="flex text-yellow-400 text-sm">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($rating)) <span>★</span>
                                    @elseif ($i - 0.5 <= $rating) <span class="opacity-60">★</span>
                                    @else <span class="text-warm-muted">★</span> @endif
                                @endfor
                            </div>
                            <span class="text-sm font-bold text-warm-white">{{ number_format($rating, 1) }}</span>
                            <span class="text-xs font-semibold text-warm-muted">({{ $reviewCount }} ulasan)</span>
                        </div>

                        <div class="mt-8 border-y border-border-subtle py-6">
                            <p class="text-3xl sm:text-4xl font-black text-gold text-glow">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- VARIANTS --}}
                        @if ($product->variants->isNotEmpty())
                            @php $variantGroups = $product->variants->groupBy('name'); @endphp
                            <div class="mt-6 space-y-5">
                                @foreach ($variantGroups as $variantName => $variantOptions)
                                    <div>
                                        <p class="mb-3 text-xs font-black uppercase tracking-widest text-warm-gray">{{ $variantName }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($variantOptions as $variant)
                                                <button type="button" class="rounded-full border px-5 py-2 text-sm font-bold transition {{ $variant->stock > 0 ? 'border-border-subtle bg-dark-tertiary text-warm-white hover:border-gold hover:text-gold' : 'border-border-subtle bg-dark-tertiary/50 text-warm-muted cursor-not-allowed line-through' }}" {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                                                    {{ $variant->value }}
                                                    @if ($variant->additional_price > 0)
                                                        <span class="text-[10px] text-warm-muted ml-1">+Rp {{ number_format($variant->additional_price, 0, ',', '.') }}</span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- ACTION BUTTON --}}
                        <div class="mt-8 flex gap-3">
                            @auth
                                <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full rounded-full btn-gold px-6 py-4 text-sm font-black uppercase tracking-widest transition hover:-translate-y-1 disabled:cursor-not-allowed disabled:opacity-50" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                        {{ $product->stock <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                                    </button>
                                </form>

                                @php $inWishlist = auth()->user()?->wishlists()->where('product_id', $product->id)->exists(); @endphp
                                <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex h-full min-h-[52px] w-[60px] items-center justify-center rounded-full border transition-all {{ $inWishlist ? 'border-gold bg-gold text-dark-primary shadow-gold-sm' : 'border-border-subtle bg-dark-tertiary text-gold hover:bg-dark-elevated hover:border-gold/50' }}" title="{{ $inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
                                        <svg class="w-5 h-5" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="flex-1 rounded-full btn-gold px-6 py-4 text-center text-sm font-black uppercase tracking-widest transition hover:-translate-y-1">Login untuk Membeli</a>
                            @endauth
                        </div>

                        {{-- PRODUCT META --}}
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="rounded-xl bg-dark-tertiary border border-border-subtle p-4 shadow-sm text-center">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gold-light">SKU</p>
                                <p class="mt-1 text-sm font-bold text-warm-white">{{ $product->sku }}</p>
                            </div>
                            <div class="rounded-xl bg-dark-tertiary border border-border-subtle p-4 shadow-sm text-center">
                                <p class="text-[10px] font-black uppercase tracking-widest text-gold">Berat</p>
                                <p class="mt-1 text-sm font-bold text-warm-white">{{ $product->weight }} gram</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="border-t border-border-subtle p-6 sm:p-10 bg-dark-secondary">
                    <h2 class="text-lg font-black text-warm-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gold" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                        Deskripsi Produk
                    </h2>
                    <div class="prose prose-sm prose-invert max-w-none text-warm-gray leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            {{-- RELATED PRODUCTS --}}
            @if ($relatedProducts->isNotEmpty())
                <div class="mt-12 mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-black text-warm-white">
                            <span class="text-gold">Produk</span> 
                            <span class="text-warm-white">Terkait</span>
                        </h2>
                    </div>

                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($relatedProducts as $relProduct)
                            @php
                                $relImage = $relProduct->images->firstWhere('is_primary', true) ?? $relProduct->images->first();
                            @endphp
                            <div class="group relative bg-dark-secondary rounded-[1.5rem] border border-border-subtle shadow-dark-card overflow-hidden hover:shadow-[0_15px_35px_rgba(200,149,108,0.08)] hover:border-gold/25 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                                <a href="{{ route('catalog.show', $relProduct->slug) }}" class="flex-1 flex flex-col">
                                    <div class="relative m-2 aspect-square overflow-hidden rounded-xl bg-dark-tertiary border border-border-subtle p-2">
                                        @if ($relImage)
                                            <img src="{{ Storage::url($relImage->image_path) }}" alt="{{ $relProduct->name }}" class="w-full h-full object-contain rounded-lg group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-warm-muted">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4 pt-2 flex-1 flex flex-col">
                                        <p class="text-[9px] text-gold font-black uppercase tracking-widest mb-1">{{ $relProduct->brand?->name ?? 'Brand' }}</p>
                                        <h3 class="text-sm font-bold text-warm-white line-clamp-2 leading-snug group-hover:text-gold transition-colors">{{ $relProduct->name }}</h3>
                                        <div class="mt-auto pt-3">
                                            <p class="text-sm font-black text-warm-white">Rp {{ number_format($relProduct->price, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            function imageGallery() {
                @php
                    $firstImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first();
                    $firstImageUrl = $firstImage ? Storage::url($firstImage->image_path) : asset('images/placeholder.png');
                @endphp
                return {
                    activeImage: '{{ $firstImageUrl }}',
                    setImage(url) { this.activeImage = url; }
                }
            }
        </script>
    @endpush
</x-app-layout>