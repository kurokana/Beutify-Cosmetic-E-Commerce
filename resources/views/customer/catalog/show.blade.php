<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <a href="{{ route('catalog.index', ['category_id' => $product->category_id]) }}"
                class="hover:text-pink-600">{{ $product->category?->name }}</a>
            <span>/</span>
            <span class="text-gray-800 font-medium truncate max-w-xs">{{ $product->name }}</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ── Product Detail Section ──────────────────────────────────── --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="flex flex-col lg:flex-row gap-0">

                    {{-- ── Image Gallery ──────────────────────────────────── --}}
                    <div class="lg:w-1/2 p-6" x-data="imageGallery()">
                        {{-- Main Image --}}
                        <div class="aspect-square rounded-xl overflow-hidden bg-gray-50 mb-3">
                            <img
                                :src="activeImage"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover"
                            >
                        </div>

                        {{-- Thumbnails --}}
                        @if ($product->images->count() > 1)
                            <div class="flex gap-2 overflow-x-auto pb-1">
                                @foreach ($product->images as $image)
                                    <button
                                        @click="setImage('{{ Storage::url($image->image_path) }}')"
                                        :class="activeImage === '{{ Storage::url($image->image_path) }}'
                                            ? 'ring-2 ring-pink-500'
                                            : 'ring-1 ring-gray-200 hover:ring-pink-300'"
                                        class="shrink-0 w-16 h-16 rounded-lg overflow-hidden transition">
                                        <img
                                            src="{{ Storage::url($image->image_path) }}"
                                            alt="{{ $product->name }}"
                                            class="w-full h-full object-cover"
                                            loading="lazy"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- ── Product Info ────────────────────────────────────── --}}
                    <div class="lg:w-1/2 p-6 lg:border-l border-gray-100">

                        {{-- Brand & Category --}}
                        <div class="flex items-center gap-2 mb-2">
                            <a href="{{ route('catalog.index', ['brand_id' => $product->brand_id]) }}"
                                class="text-sm font-semibold text-pink-600 hover:underline">
                                {{ $product->brand?->name }}
                            </a>
                            <span class="text-gray-300">·</span>
                            <a href="{{ route('catalog.index', ['category_id' => $product->category_id]) }}"
                                class="text-sm text-gray-500 hover:text-pink-600">
                                {{ $product->category?->name }}
                            </a>
                        </div>

                        {{-- Name --}}
                        <h1 class="text-2xl font-bold text-gray-900 leading-tight mb-3">{{ $product->name }}</h1>

                        {{-- Rating --}}
                        @php
                            $rating = (float) $product->average_rating;
                            $reviewCount = $product->reviews->count();
                        @endphp
                        <div class="flex items-center gap-2 mb-4">
                            <div class="flex text-yellow-400 text-lg">
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
                            <span class="text-sm font-medium text-gray-700">{{ number_format($rating, 1) }}</span>
                            <span class="text-sm text-gray-400">({{ $reviewCount }} ulasan)</span>
                        </div>

                        {{-- Price --}}
                        <div class="mb-4">
                            <span class="text-3xl font-bold text-gray-900">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Stock Status --}}
                        <div class="mb-5">
                            @if ($product->stock <= 0)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    Stok Habis
                                </span>
                            @elseif ($product->stock <= 5)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-700">
                                    <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                    Stok Terbatas ({{ $product->stock }} tersisa)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-700">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    Tersedia ({{ $product->stock }} stok)
                                </span>
                            @endif
                        </div>

                        {{-- Variants --}}
                        @if ($product->variants->isNotEmpty())
                            @php
                                $variantGroups = $product->variants->groupBy('name');
                            @endphp
                            <div class="mb-5 space-y-3">
                                @foreach ($variantGroups as $variantName => $variantOptions)
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 mb-2">{{ $variantName }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($variantOptions as $variant)
                                                <button
                                                    class="px-3 py-1.5 rounded-lg border text-sm font-medium transition
                                                        {{ $variant->stock > 0
                                                            ? 'border-gray-300 text-gray-700 hover:border-pink-500 hover:text-pink-600 cursor-pointer'
                                                            : 'border-gray-200 text-gray-300 cursor-not-allowed line-through' }}"
                                                    {{ $variant->stock <= 0 ? 'disabled' : '' }}
                                                    title="{{ $variant->stock <= 0 ? 'Stok habis' : '' }}"
                                                >
                                                    {{ $variant->value }}
                                                    @if ($variant->additional_price > 0)
                                                        <span class="text-xs text-gray-400">
                                                            (+Rp {{ number_format($variant->additional_price, 0, ',', '.') }})
                                                        </span>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Add to Cart / Wishlist --}}
                        <div class="flex gap-3 mb-6">
                            @auth
                                <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                        class="w-full py-3 bg-pink-600 text-white rounded-xl font-semibold hover:bg-pink-700 transition
                                            {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ $product->stock <= 0 ? 'disabled' : '' }}
                                    >
                                        {{ $product->stock <= 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                                    </button>
                                </form>

                                {{-- Wishlist Toggle Button — Requirements 8.1, 8.2 --}}
                                @php
                                    $inWishlist = auth()->user()?->wishlists()
                                        ->where('product_id', $product->id)
                                        ->exists();
                                @endphp
                                <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="p-3 rounded-xl border transition
                                            {{ $inWishlist
                                                ? 'border-pink-500 text-pink-600 bg-pink-50 hover:bg-pink-100'
                                                : 'border-gray-300 text-gray-500 hover:border-pink-500 hover:text-pink-600' }}"
                                        title="{{ $inWishlist ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}">
                                        <svg class="w-5 h-5" fill="{{ $inWishlist ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}"
                                    class="flex-1 py-3 bg-pink-600 text-white rounded-xl font-semibold text-center hover:bg-pink-700 transition">
                                    Login untuk Membeli
                                </a>
                                {{-- Requirement 8.5: redirect guest to login --}}
                                <a href="{{ route('login') }}"
                                    class="p-3 rounded-xl border border-gray-300 text-gray-500 hover:border-pink-500 hover:text-pink-600 transition"
                                    title="Login untuk menambah ke Wishlist">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </a>
                            @endauth
                        </div>

                        {{-- SKU & Weight --}}
                        <div class="text-xs text-gray-400 space-y-1 border-t pt-4">
                            <p>SKU: <span class="text-gray-600">{{ $product->sku }}</span></p>
                            <p>Berat: <span class="text-gray-600">{{ $product->weight }} gram</span></p>
                        </div>
                    </div>
                </div>

                {{-- ── Description ─────────────────────────────────────────── --}}
                <div class="border-t border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3">Deskripsi Produk</h2>
                    <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            </div>

            {{-- ── Reviews Section ─────────────────────────────────────────── --}}
            <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-5">
                    Ulasan Pelanggan
                    <span class="text-sm font-normal text-gray-400 ml-2">({{ $reviewCount }} ulasan)</span>
                </h2>

                {{-- ── Review Form ─────────────────────────────────────────── --}}
                @auth
                    @if ($canReview && ! $hasReviewed)
                        {{-- Requirement 7.1: show form only for delivered-order buyers --}}
                        <div class="mb-6 p-5 bg-pink-50 border border-pink-100 rounded-xl">
                            <h3 class="text-sm font-semibold text-gray-800 mb-4">Tulis Ulasan Anda</h3>

                            @if (session('error'))
                                <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                                    {{ session('error') }}
                                </div>
                            @endif

                            <form action="{{ route('reviews.store', $product) }}" method="POST" x-data="{ rating: 0, hovered: 0 }">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $eligibleOrder->id }}">

                                {{-- Star Rating --}}
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating <span class="text-red-500">*</span></label>
                                    <div class="flex gap-1">
                                        @for ($star = 1; $star <= 5; $star++)
                                            <button
                                                type="button"
                                                @click="rating = {{ $star }}"
                                                @mouseenter="hovered = {{ $star }}"
                                                @mouseleave="hovered = 0"
                                                class="text-3xl transition-colors focus:outline-none"
                                                :class="(hovered || rating) >= {{ $star }} ? 'text-yellow-400' : 'text-gray-300'"
                                                aria-label="{{ $star }} bintang"
                                            >★</button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" :value="rating">
                                    @error('rating')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Comment --}}
                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                                    <textarea
                                        id="comment"
                                        name="comment"
                                        rows="3"
                                        maxlength="1000"
                                        placeholder="Bagikan pengalaman Anda menggunakan produk ini..."
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent resize-none"
                                    >{{ old('comment') }}</textarea>
                                    @error('comment')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button
                                    type="submit"
                                    class="px-5 py-2 bg-pink-600 text-white text-sm font-semibold rounded-lg hover:bg-pink-700 transition"
                                    x-bind:disabled="rating === 0"
                                    :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                                >
                                    Kirim Ulasan
                                </button>
                            </form>
                        </div>
                    @elseif ($canReview && $hasReviewed)
                        {{-- Requirement 7.4: already reviewed --}}
                        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Anda sudah memberikan ulasan untuk produk ini.
                        </div>
                    @else
                        {{-- Requirement 7.3: not purchased --}}
                        <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-xl text-sm text-gray-500 flex items-center gap-2">
                            <svg class="w-5 h-5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Anda hanya dapat mengulas produk yang sudah dibeli dan pesanannya berstatus Selesai.
                        </div>
                    @endif
                @endauth

                {{-- Success toast --}}
                @if (session('success'))
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if ($product->reviews->isEmpty())
                    <div class="text-center py-8 text-gray-400">
                        <svg class="mx-auto w-12 h-12 mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <p class="text-sm">Belum ada ulasan untuk produk ini.</p>
                        <p class="text-xs mt-1">Jadilah yang pertama memberikan ulasan!</p>
                    </div>
                @else
                    {{-- Rating Summary --}}
                    <div class="flex items-center gap-6 mb-6 p-4 bg-gray-50 rounded-xl">
                        <div class="text-center">
                            <p class="text-4xl font-bold text-gray-900">{{ number_format($rating, 1) }}</p>
                            <div class="flex justify-center text-yellow-400 text-lg mt-1">
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
                            <p class="text-xs text-gray-400 mt-1">{{ $reviewCount }} ulasan</p>
                        </div>
                        <div class="flex-1 space-y-1">
                            @for ($star = 5; $star >= 1; $star--)
                                @php
                                    $count = $product->reviews->where('rating', $star)->count();
                                    $pct = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                                @endphp
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="w-4 text-right">{{ $star }}</span>
                                    <span class="text-yellow-400">★</span>
                                    <div class="flex-1 bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-yellow-400 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="w-6 text-right">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    {{-- Review List --}}
                    <div class="space-y-5">
                        @foreach ($product->reviews as $review)
                            <div class="border-b border-gray-100 pb-5 last:border-0 last:pb-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-pink-100 flex items-center justify-center text-pink-600 font-semibold text-sm shrink-0">
                                            {{ strtoupper(substr($review->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $review->user?->name ?? 'Pengguna' }}</p>
                                            <div class="flex text-yellow-400 text-xs">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <span>{{ $i <= $review->rating ? '★' : '☆' }}</span>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400 shrink-0">
                                        {{ $review->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                @if ($review->comment)
                                    <p class="mt-2 text-sm text-gray-600 leading-relaxed pl-12">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- ── Related Products ─────────────────────────────────────────── --}}
            @if ($relatedProducts->isNotEmpty())
                <div class="mt-8">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Produk Terkait</h2>
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                        @foreach ($relatedProducts as $related)
                            @php
                                $relatedPrimaryImage = $related->images->firstWhere('is_primary', true)
                                    ?? $related->images->first();
                            @endphp
                            <a href="{{ route('catalog.show', $related->slug) }}"
                                class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                                <div class="aspect-square overflow-hidden bg-gray-50">
                                    @if ($relatedPrimaryImage)
                                        <img
                                            src="{{ Storage::url($relatedPrimaryImage->image_path) }}"
                                            alt="{{ $related->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <p class="text-xs text-pink-600 font-medium mb-0.5">{{ $related->brand?->name }}</p>
                                    <h3 class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug">{{ $related->name }}</h3>
                                    <div class="flex items-center gap-1 mt-1">
                                        @php $relRating = (float) $related->average_rating; @endphp
                                        <div class="flex text-yellow-400 text-xs">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span>{{ $i <= floor($relRating) ? '★' : '☆' }}</span>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-gray-400">{{ number_format($relRating, 1) }}</span>
                                    </div>
                                    <p class="mt-1.5 text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($related->price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </a>
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
                setImage(url) {
                    this.activeImage = url;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
