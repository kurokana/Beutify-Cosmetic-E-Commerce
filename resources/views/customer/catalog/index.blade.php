<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Katalog Produk
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search bar --}}
            <form method="GET" action="{{ route('search') }}" class="mb-6">
                <div class="flex gap-2">
                    <input
                        type="text"
                        name="q"
                        placeholder="Cari produk, merek, atau deskripsi..."
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                    >
                    <button type="submit"
                        class="px-5 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700 transition">
                        Cari
                    </button>
                </div>
            </form>

            <div class="flex flex-col lg:flex-row gap-6">

                {{-- ── Sidebar Filter ─────────────────────────────────────── --}}
                <aside class="w-full lg:w-64 shrink-0">
                    <form method="GET" action="{{ route('catalog.index') }}" id="filter-form">
                        <div class="bg-white rounded-xl shadow-sm p-5 space-y-6">

                            {{-- Kategori --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Kategori</h3>
                                <ul class="space-y-1">
                                    <li>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 hover:text-pink-600">
                                            <input type="radio" name="category_id" value=""
                                                {{ empty($filters['category_id']) ? 'checked' : '' }}
                                                class="text-pink-600 focus:ring-pink-500"
                                                onchange="document.getElementById('filter-form').submit()">
                                            Semua Kategori
                                        </label>
                                    </li>
                                    @foreach ($categories as $category)
                                        <li>
                                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 hover:text-pink-600">
                                                <input type="radio" name="category_id" value="{{ $category->id }}"
                                                    {{ (string)($filters['category_id'] ?? '') === (string)$category->id ? 'checked' : '' }}
                                                    class="text-pink-600 focus:ring-pink-500"
                                                    onchange="document.getElementById('filter-form').submit()">
                                                {{ $category->name }}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Merek --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Merek</h3>
                                <ul class="space-y-1 max-h-48 overflow-y-auto">
                                    <li>
                                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 hover:text-pink-600">
                                            <input type="radio" name="brand_id" value=""
                                                {{ empty($filters['brand_id']) ? 'checked' : '' }}
                                                class="text-pink-600 focus:ring-pink-500"
                                                onchange="document.getElementById('filter-form').submit()">
                                            Semua Merek
                                        </label>
                                    </li>
                                    @foreach ($brands as $brand)
                                        <li>
                                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600 hover:text-pink-600">
                                                <input type="radio" name="brand_id" value="{{ $brand->id }}"
                                                    {{ (string)($filters['brand_id'] ?? '') === (string)$brand->id ? 'checked' : '' }}
                                                    class="text-pink-600 focus:ring-pink-500"
                                                    onchange="document.getElementById('filter-form').submit()">
                                                {{ $brand->name }}
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            {{-- Rentang Harga --}}
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Rentang Harga</h3>
                                <div class="space-y-2">
                                    <div>
                                        <label class="text-xs text-gray-500">Harga Minimum</label>
                                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}"
                                            placeholder="0"
                                            class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Harga Maksimum</label>
                                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}"
                                            placeholder="Tidak terbatas"
                                            class="mt-1 w-full rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2 bg-pink-600 text-white rounded-md text-sm font-medium hover:bg-pink-700 transition">
                                        Terapkan
                                    </button>
                                </div>
                            </div>

                            {{-- Reset --}}
                            @if (array_filter($filters))
                                <a href="{{ route('catalog.index') }}"
                                    class="block text-center text-sm text-gray-500 hover:text-pink-600 underline">
                                    Reset Filter
                                </a>
                            @endif

                            {{-- Hidden sort to preserve when filter changes --}}
                            <input type="hidden" name="sort" value="{{ $sort }}">
                        </div>
                    </form>
                </aside>

                {{-- ── Main Content ────────────────────────────────────────── --}}
                <div class="flex-1 min-w-0">

                    {{-- Toolbar: count + sort --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                        <p class="text-sm text-gray-500">
                            Menampilkan <span class="font-medium text-gray-700">{{ $products->total() }}</span> produk
                        </p>
                        <form method="GET" action="{{ route('catalog.index') }}" id="sort-form">
                            {{-- Preserve active filters --}}
                            @foreach ($filters as $key => $value)
                                @if ($value !== null && $value !== '')
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <select name="sort" onchange="document.getElementById('sort-form').submit()"
                                class="rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                                <option value="latest"     {{ $sort === 'latest'     ? 'selected' : '' }}>Terbaru</option>
                                <option value="price_asc"  {{ $sort === 'price_asc'  ? 'selected' : '' }}>Harga Terendah</option>
                                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                                <option value="rating_desc"{{ $sort === 'rating_desc'? 'selected' : '' }}>Rating Tertinggi</option>
                            </select>
                        </form>
                    </div>

                    {{-- Product Grid --}}
                    @if ($products->isEmpty())
                        <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                            <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0H4" />
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">Tidak ada produk ditemukan</p>
                            <p class="text-gray-400 text-sm mt-1">Coba ubah atau hapus filter yang diterapkan.</p>
                            <a href="{{ route('catalog.index') }}"
                                class="mt-4 inline-block px-5 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700 transition">
                                Lihat Semua Produk
                            </a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4">
                            @foreach ($products as $product)
                                @php
                                    $primaryImage = $product->images->firstWhere('is_primary', true)
                                        ?? $product->images->first();
                                    $inWishlist = auth()->check()
                                        ? auth()->user()->wishlists()->where('product_id', $product->id)->exists()
                                        : false;
                                @endphp
                                <div class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 relative">
                                    {{-- Wishlist Toggle Button (top-right corner) --}}
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

                                    <a href="{{ route('catalog.show', $product->slug) }}" class="block">
                                        {{-- Product Image --}}
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

                                        {{-- Product Info --}}
                                        <div class="p-3">
                                            <p class="text-xs text-pink-600 font-medium mb-0.5">{{ $product->brand?->name }}</p>
                                            <h3 class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug">{{ $product->name }}</h3>

                                            {{-- Rating --}}
                                            <div class="flex items-center gap-1 mt-1.5">
                                                @php $rating = (float) $product->average_rating; @endphp
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
                                </div>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-8">
                            {{ $products->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
