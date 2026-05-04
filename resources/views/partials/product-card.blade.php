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

<div class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 relative flex flex-col">

    {{-- Badge (e.g. "#1 Terlaris") --}}
    @if (!empty($badge))
        <div class="absolute top-2 left-2 z-10 bg-pink-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow">
            {{ $badge }}
        </div>
    @endif

    {{-- Wishlist toggle --}}
    @auth
        <form action="{{ route('wishlist.toggle', $product) }}" method="POST"
            class="absolute top-2 right-2 z-10">
            @csrf
            <button type="submit"
                class="w-8 h-8 rounded-full flex items-center justify-center shadow transition
                    {{ $inWishlist
                        ? 'bg-pink-600 text-white hover:bg-pink-700'
                        : 'bg-white text-gray-400 hover:text-pink-600' }}"
                title="{{ $inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
                <svg class="w-4 h-4" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </button>
        </form>
    @else
        <a href="{{ route('login') }}"
            class="absolute top-2 right-2 z-10 w-8 h-8 rounded-full bg-white flex items-center justify-center shadow text-gray-400 hover:text-pink-600 transition"
            title="Login untuk menambah ke Wishlist">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
            </svg>
        </a>
    @endauth

    {{-- Product image + info link --}}
    <a href="{{ route('catalog.show', $product->slug) }}" class="block flex-1 flex flex-col">
        {{-- Image --}}
        <div class="aspect-square overflow-hidden bg-gray-50">
            @if ($primaryImage)
                <img
                    src="{{ Storage::url($primaryImage->image_path) }}"
                    alt="{{ $product->name }}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    loading="lazy"
                >
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-300">
                    <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="p-3 flex-1 flex flex-col">
            <p class="text-xs text-pink-600 font-medium mb-0.5">{{ $product->brand?->name }}</p>
            <h3 class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug flex-1">{{ $product->name }}</h3>

            {{-- Star rating --}}
            <div class="flex items-center gap-1 mt-1.5">
                <div class="flex text-yellow-400 text-xs">
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($rating))
                            <span>★</span>
                        @elseif ($i - 0.5 <= $rating)
                            <span class="opacity-60">★</span>
                        @else
                            <span class="text-gray-300">★</span>
                        @endif
                    @endfor
                </div>
                <span class="text-xs text-gray-400">{{ number_format($rating, 1) }}</span>
            </div>

            {{-- Price --}}
            <p class="mt-2 text-sm font-semibold text-gray-900">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </p>

            {{-- Stock badge --}}
            @if ($product->stock <= 0)
                <span class="mt-1 inline-block text-xs text-red-500 font-medium">Stok Habis</span>
            @elseif ($product->stock <= 5)
                <span class="mt-1 inline-block text-xs text-orange-500 font-medium">Stok Terbatas</span>
            @endif
        </div>
    </a>

    {{-- Add to cart button --}}
    <div class="px-3 pb-3">
        @if ($product->stock > 0)
            @auth
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit"
                        class="w-full py-2 bg-pink-600 text-white text-xs font-semibold rounded-lg hover:bg-pink-700 transition-colors duration-200 flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Tambah ke Keranjang
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="block w-full py-2 bg-pink-600 text-white text-xs font-semibold rounded-lg hover:bg-pink-700 transition-colors duration-200 text-center">
                    Tambah ke Keranjang
                </a>
            @endauth
        @else
            <button disabled
                class="w-full py-2 bg-gray-200 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed">
                Stok Habis
            </button>
        @endif
    </div>
</div>
