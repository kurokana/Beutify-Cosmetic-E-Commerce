<x-admin-layout>
    <x-slot name="pageTitle">Edit Produk</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('admin.products.index') }}"
                class="text-gray-600 hover:text-gray-900 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Edit Produk</h2>
                <p class="mt-1 text-sm text-gray-600">Perbarui informasi produk {{ $product->name }}</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-lg shadow">
            <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Product Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $product->name) }}"
                        required
                        autofocus
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Contoh: L'Oreal Serum"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="Deskripsi lengkap tentang produk..."
                    >{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Brand & Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Merek <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="brand_id"
                            name="brand_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('brand_id') border-red-500 @enderror"
                        >
                            <option value="">Pilih Merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('category_id') border-red-500 @enderror"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Price, Stock, Weight --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="price"
                            id="price"
                            step="0.01"
                            min="0"
                            value="{{ old('price', $product->price) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('price') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            id="stock"
                            min="0"
                            value="{{ old('stock', $product->stock) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('stock') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">
                            Berat (gram) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="weight"
                            id="weight"
                            min="1"
                            value="{{ old('weight', $product->weight) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('weight') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="sku"
                        id="sku"
                        value="{{ old('sku', $product->sku) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('sku') border-red-500 @enderror"
                        placeholder="Contoh: LOREAL-SERUM-001"
                    >
                    <p class="mt-1 text-xs text-gray-500">Kode unik untuk identifikasi produk</p>
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Current Images --}}
                @if ($product->images->count() > 0)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gambar Saat Ini
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                            @foreach ($product->images as $image)
                                <div class="relative group rounded-lg overflow-hidden border border-gray-200">
                                    <img
                                        src="{{ Storage::url($image->image_path) }}"
                                        alt="Product image"
                                        class="w-full h-32 object-cover"
                                    >
                                    @if ($image->is_primary)
                                        <span class="absolute top-2 left-2 bg-pink-600 text-white text-xs px-2 py-1 rounded">
                                            Utama
                                        </span>
                                    @endif
                                    <button
                                        type="button"
                                        onclick="if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) document.getElementById('delete-image-{{ $image->id }}').submit()"
                                        class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-black/50 transition-opacity flex items-center justify-center cursor-pointer"
                                    >
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Add New Images --}}
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                        Tambah Gambar Baru
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                        <div class="flex-1">
                            <input
                                type="file"
                                name="images[]"
                                id="images"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 @error('images') border-red-500 @enderror"
                            >
                            <p class="mt-1 text-xs text-gray-500">
                                Format: JPG, PNG, WebP. Maksimal 2MB per file.
                            </p>
                            @error('images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Is Active --}}
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        name="is_active"
                        id="is_active"
                        value="1"
                        {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-gray-300 rounded"
                    >
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Aktifkan produk (tampilkan di katalog)
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a
                        href="{{ route('admin.products.index') }}"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Perbarui Produk
                    </button>
                </div>
            </form>
        </div>

        @foreach ($product->images as $image)
            <form
                id="delete-image-{{ $image->id }}"
                action="{{ route('admin.product-images.destroy', $image) }}"
                method="POST"
                class="hidden"
            >
                @csrf
                @method('DELETE')
            </form>
        @endforeach
    </div>
</x-admin-layout>
