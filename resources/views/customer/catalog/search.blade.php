<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Hasil Pencarian
            @if ($keyword !== '')
                <span class="text-gray-400 font-normal text-base ml-2">untuk "{{ $keyword }}"</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('search') }}" class="mb-8">
                <div class="flex gap-2 max-w-2xl">
                    <input
                        type="text"
                        name="q"
                        value="{{ $keyword }}"
                        placeholder="Cari produk, merek, atau deskripsi..."
                        autofocus
                        class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500 text-sm"
                    >
                    <button type="submit"
                        class="px-6 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700 transition">
                        Cari
                    </button>
                </div>
            </form>

            {{-- No keyword entered --}}
            @if ($keyword === '')
                <div class="bg-white rounded-xl shadow-sm p-12 text-center max-w-lg mx-auto">
                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <p class="text-gray-600 text-lg font-medium">Cari produk kosmetik favoritmu</p>
                    <p class="text-gray-400 text-sm mt-2">Masukkan nama produk, merek, atau kata kunci di kolom pencarian di atas.</p>

                    {{-- Category suggestions --}}
                    @if ($categories->isNotEmpty())
                        <div class="mt-6">
                            <p class="text-sm font-medium text-gray-600 mb-3">Telusuri berdasarkan kategori:</p>
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach ($categories->take(8) as $category)
                                    <a href="{{ route('catalog.index', ['category_id' => $category->id]) }}"
                                        class="px-3 py-1.5 bg-pink-50 text-pink-700 rounded-full text-sm hover:bg-pink-100 transition">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

            {{-- Search performed but no results --}}
            @elseif ($products->isEmpty())
                <div class="bg-white rounded-xl shadow-sm p-12 text-center max-w-lg mx-auto">
                    <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-gray-700 text-lg font-semibold">Produk tidak ditemukan</p>
                    <p class="text-gray-400 text-sm mt-2">
                        Tidak ada produk yang cocok dengan kata kunci
                        <span class="font-medium text-gray-600">"{{ $keyword }}"</span>.
                    </p>

                    {{-- Suggestions --}}
                    <div class="mt-6 text-left bg-gray-50 rounded-xl p-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Saran pencarian:</p>
                        <ul class="text-sm text-gray-500 space-y-1 list-disc list-inside">
                            <li>Periksa ejaan kata kunci yang dimasukkan</li>
                            <li>Gunakan kata kunci yang lebih umum atau singkat</li>
                            <li>Coba cari berdasarkan nama merek atau kategori</li>
                        </ul>
                    </div>

                    {{-- Category suggestions --}}
                    @if ($categories->isNotEmpty())
                        <div class="mt-5">
                            <p class="text-sm font-medium text-gray-600 mb-3">Atau telusuri berdasarkan kategori:</p>
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach ($categories->take(8) as $category)
                                    <a href="{{ route('catalog.index', ['category_id' => $category->id]) }}"
                                        class="px-3 py-1.5 bg-pink-50 text-pink-700 rounded-full text-sm hover:bg-pink-100 transition">
                                        {{ $category->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Brand suggestions --}}
                    @if ($brands->isNotEmpty())
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-600 mb-3">Atau telusuri berdasarkan merek:</p>
                            <div class="flex flex-wrap justify-center gap-2">
                                @foreach ($brands->take(6) as $brand)
                                    <a href="{{ route('catalog.index', ['brand_id' => $brand->id]) }}"
                                        class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition">
                                        {{ $brand->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <a href="{{ route('catalog.index') }}"
                        class="mt-6 inline-block px-6 py-2.5 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700 transition">
                        Lihat Semua Produk
                    </a>
                </div>

            {{-- Search results found --}}
            @else
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <p class="text-sm text-gray-500">
                        Ditemukan <span class="font-medium text-gray-700">{{ $products->total() }}</span> produk
                        untuk kata kunci <span class="font-medium text-gray-700">"{{ $keyword }}"</span>
                    </p>
                    <form method="GET" action="{{ route('search') }}" id="search-sort-form">
                        <input type="hidden" name="q" value="{{ $keyword }}">
                        <select name="sort" onchange="document.getElementById('search-sort-form').submit()"
                            class="rounded-md border-gray-300 text-sm focus:border-pink-500 focus:ring-pink-500">
                            <option value="latest"     {{ request('sort', 'latest') === 'latest'     ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc"  {{ request('sort') === 'price_asc'  ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                            <option value="rating_desc"{{ request('sort') === 'rating_desc'? 'selected' : '' }}>Rating Tertinggi</option>
                        </select>
                    </form>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach ($products as $product)
                        @php
                            $primaryImage = $product->images->firstWhere('is_primary', true)
                                ?? $product->images->first();
                        @endphp
                        <a href="{{ route('catalog.show', $product->slug) }}"
                            class="group bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
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
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <p class="text-xs text-pink-600 font-medium mb-0.5">{{ $product->brand?->name }}</p>
                                <h3 class="text-sm font-medium text-gray-800 line-clamp-2 leading-snug">{{ $product->name }}</h3>
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
                                <p class="mt-2 text-sm font-semibold text-gray-900">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                                @if ($product->stock <= 0)
                                    <span class="mt-1 inline-block text-xs text-red-500 font-medium">Stok Habis</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $products->appends(['q' => $keyword, 'sort' => request('sort')])->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
