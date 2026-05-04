<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Wishlist Saya</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="flex items-center justify-between mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Wishlist Saya</h1>
                <span class="text-sm text-gray-500">{{ $wishlists->count() }} produk tersimpan</span>
            </div>

            @if ($wishlists->isEmpty())
                {{-- Empty Wishlist State --}}
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Wishlist Anda kosong</h2>
                    <p class="text-sm text-gray-400 mb-6">Simpan produk favorit Anda untuk dibeli nanti.</p>
                    <a href="{{ route('catalog.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-pink-600 text-white rounded-xl font-semibold hover:bg-pink-700 transition">
                        Jelajahi Produk
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                    @foreach ($wishlists as $wishlist)
                        @php
                            $product = $wishlist->product;
                            $primaryImage = $product?->images?->firstWhere('is_primary', true)
                                ?? $product?->images?->first();
                        @endphp

                        @if ($product)
                            <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col">
                                {{-- Product Image --}}
                                <a href="{{ route('catalog.show', $product->slug) }}"
                                    class="block aspect-square overflow-hidden bg-gray-50 group">
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
                                </a>

                                {{-- Product Info --}}
                                <div class="p-4 flex flex-col flex-1">
                                    <p class="text-xs text-pink-600 font-medium mb-0.5">{{ $product->brand?->name }}</p>
                                    <a href="{{ route('catalog.show', $product->slug) }}"
                                        class="text-sm font-semibold text-gray-800 hover:text-pink-600 line-clamp-2 leading-snug flex-1">
                                        {{ $product->name }}
                                    </a>

                                    {{-- Price --}}
                                    <p class="mt-2 text-base font-bold text-gray-900">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>

                                    {{-- Stock Status --}}
                                    <div class="mt-1 mb-3">
                                        @if ($product->stock <= 0)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                                Stok Habis
                                            </span>
                                        @elseif ($product->stock <= 5)
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-orange-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                                                Stok Terbatas ({{ $product->stock }} tersisa)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                                Tersedia
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex gap-2 mt-auto">
                                        {{-- Move to Cart --}}
                                        @if ($product->stock > 0)
                                            <form action="{{ route('wishlist.moveToCart', $product) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit"
                                                    class="w-full py-2 bg-pink-600 text-white text-xs font-semibold rounded-lg hover:bg-pink-700 transition">
                                                    Pindah ke Keranjang
                                                </button>
                                            </form>
                                        @else
                                            <button disabled
                                                class="flex-1 py-2 bg-gray-100 text-gray-400 text-xs font-semibold rounded-lg cursor-not-allowed">
                                                Stok Habis
                                            </button>
                                        @endif

                                        {{-- Remove from Wishlist --}}
                                        <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="p-2 rounded-lg border border-gray-200 text-gray-400 hover:border-red-300 hover:text-red-500 transition"
                                                title="Hapus dari wishlist">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
