{{--
    Reusable product card partial.
    Variables:
      $product  — App\Models\Product (with brand, images loaded)
      $badge    — optional string label (e.g. "#1 Terlaris"), null to hide
--}}
@php
    $primaryImage = $product->images->firstWhere('is_primary', true)
        ?? $product->images->first();

    $inWishlist = auth()->check()
        ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
        : false;

    $rating = (float) $product->average_rating;
@endphp

<div class="group relative flex flex-col overflow-hidden rounded-[1.75rem] bg-dark-secondary border border-border-subtle shadow-dark-card hover:shadow-[0_20px_50px_rgba(200,149,108,0.1)] hover:border-gold/25 hover:-translate-y-1.5 transition-all duration-300">

    {{-- Decorative soft gradient --}}
    <div class="absolute inset-x-0 top-0 h-28 bg-gradient-to-br from-dark-tertiary via-dark-secondary to-dark-elevated pointer-events-none"></div>

    {{-- Badge --}}
    @if (!empty($badge))
        <div class="absolute top-3 left-3 z-20 px-3 py-1 rounded-full bg-gradient-to-r from-gold to-gold-light text-dark-primary text-[10px] font-extrabold shadow-gold-sm">
            {{ $badge }}
        </div>
    @endif

    {{-- Wishlist toggle --}}
    @auth
        <form action="{{ route('wishlist.toggle', $product) }}" method="POST"
            class="absolute top-3 right-3 z-20">
            @csrf
            <button type="submit"
                class="w-9 h-9 rounded-full flex items-center justify-center border transition-all duration-200 shadow-sm
                    {{ $inWishlist
                        ? 'bg-gold text-dark-primary border-gold hover:bg-gold-light'
                        : 'bg-dark-secondary/90 text-warm-muted border-border-subtle hover:text-gold hover:border-gold/50' }}"
                title="{{ $inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
                <svg class="w-4.5 h-4.5" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
            class="absolute top-3 right-3 z-20 w-9 h-9 rounded-full bg-dark-secondary/90 border border-border-subtle flex items-center justify-center shadow-sm text-warm-muted hover:text-gold hover:border-gold/50 transition-all duration-200"
            title="Login untuk menambah ke Wishlist">
            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </a>
    @endauth

    {{-- Product image + info link --}}
    <a href="{{ route('catalog.show', $product->slug) }}" class="relative z-10 block flex-1 flex flex-col">

        {{-- Image --}}
        <div class="relative mx-3 mt-3 aspect-square overflow-hidden rounded-[1.5rem] bg-gradient-to-br from-dark-tertiary to-dark-elevated border border-border-subtle">
            @if ($primaryImage)
                <img
                    src="{{ Storage::url($primaryImage->image_path) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    loading="lazy"
                >
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-warm-muted">
                    <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="text-xs font-semibold text-warm-muted">No Image</span>
                </div>
            @endif

            {{-- Hover quick actions --}}
            <div class="absolute inset-x-0 bottom-4 flex justify-center gap-2 opacity-0 translate-y-3 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300">
                <span class="w-9 h-9 rounded-full bg-dark-secondary text-gold border border-gold/30 flex items-center justify-center shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </span>

                <span class="w-9 h-9 rounded-full bg-gold text-dark-primary flex items-center justify-center shadow-gold-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </span>
            </div>
        </div>

        {{-- Info --}}
        <div class="p-4 flex-1 flex flex-col">
            <div class="flex items-center justify-between gap-2 mb-1">
                <p class="text-[11px] text-gold font-extrabold uppercase tracking-wide truncate">
                    {{ $product->brand?->name ?? 'Beauty Brand' }}
                </p>

                @if ($product->stock > 0)
                    <span class="shrink-0 w-2 h-2 rounded-full bg-emerald-400"></span>
                @endif
            </div>

            <h3 class="text-sm font-bold text-warm-white line-clamp-2 leading-snug min-h-[2.5rem] group-hover:text-gold transition-colors">
                {{ $product->name }}
            </h3>

            {{-- Star rating --}}
            <div class="flex items-center gap-1.5 mt-3">
                <div class="flex text-yellow-400 text-xs">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($rating))
                            <span>★</span>
                        @elseif ($i - 0.5 <= $rating)
                            <span class="opacity-60">★</span>
                        @else
                            <span class="text-warm-muted">★</span>
                        @endif
                    @endfor
                </div>

                <span class="text-[11px] text-warm-gray font-semibold">
                    {{ number_format($rating, 1) }}
                </span>
            </div>

            {{-- Price + stock --}}
            <div class="mt-3 flex items-end justify-between gap-2">
                <div>
                    <p class="text-base font-extrabold text-warm-white">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </p>

                    @if ($product->stock <= 0)
                        <span class="mt-1 inline-flex text-[11px] text-red-400 font-bold">
                            Stok Habis
                        </span>
                    @elseif ($product->stock <= 5)
                        <span class="mt-1 inline-flex text-[11px] text-orange-400 font-bold">
                            Stok Terbatas
                        </span>
                    @else
                        <span class="mt-1 inline-flex text-[11px] text-warm-muted font-semibold">
                            Stok tersedia
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </a>

    {{-- Add to cart button --}}
    <div class="relative z-10 px-4 pb-4 mt-auto">
        @if ($product->stock > 0)
            @auth
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">

                    <button type="submit"
                        class="w-full py-3 btn-gold text-xs font-extrabold rounded-full flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Tambah Keranjang
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="w-full py-3 btn-gold text-xs font-extrabold rounded-full flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Tambah Keranjang
                </a>
            @endauth
        @else
            <button disabled
                class="w-full py-3 bg-dark-tertiary text-warm-muted text-xs font-extrabold rounded-full cursor-not-allowed">
                Stok Habis
            </button>
        @endif
    </div>
</div>