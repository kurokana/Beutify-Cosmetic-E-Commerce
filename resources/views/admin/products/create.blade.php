<x-admin-layout>
    <x-slot name="pageTitle">Tambah Produk</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('admin.products.index') }}"
                class="text-warm-gray hover:text-warm-white transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Tambah Produk Baru</h2>
                <p class="mt-1 text-sm text-warm-gray">Isi formulir di bawah untuk menambahkan produk baru</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-dark-secondary rounded-lg shadow">
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf

                {{-- Product Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-warm-white mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Contoh: L'Oreal Serum"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-warm-white mb-2">
                        Deskripsi <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        required
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="Deskripsi lengkap tentang produk..."
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Brand & Category --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-warm-white mb-2">
                            Merek <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="brand_id"
                            name="brand_id"
                            required
                            class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('brand_id') border-red-500 @enderror"
                        >
                            <option value="">Pilih Merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-warm-white mb-2">
                            Kategori <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="category_id"
                            name="category_id"
                            required
                            class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('category_id') border-red-500 @enderror"
                        >
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <label for="price" class="block text-sm font-medium text-warm-white mb-2">
                            Harga (Rp) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="price"
                            id="price"
                            step="0.01"
                            min="0"
                            value="{{ old('price') }}"
                            required
                            class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('price') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="stock" class="block text-sm font-medium text-warm-white mb-2">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="stock"
                            id="stock"
                            min="0"
                            value="{{ old('stock', 0) }}"
                            required
                            class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('stock') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="weight" class="block text-sm font-medium text-warm-white mb-2">
                            Berat (gram) <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            name="weight"
                            id="weight"
                            min="1"
                            value="{{ old('weight') }}"
                            required
                            class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('weight') border-red-500 @enderror"
                            placeholder="0"
                        >
                        @error('weight')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-warm-white mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="sku"
                        id="sku"
                        value="{{ old('sku') }}"
                        required
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('sku') border-red-500 @enderror"
                        placeholder="Contoh: LOREAL-SERUM-001"
                    >
                    <p class="mt-1 text-xs text-warm-gray">Kode unik untuk identifikasi produk</p>
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Images --}}
                <div>
                    <label for="images" class="block text-sm font-medium text-warm-white mb-2">
                        Gambar Produk <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex items-center gap-4">
                        <div class="flex-1">
                            <input
                                type="file"
                                name="images[]"
                                id="images"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                required
                                class="block w-full text-sm text-warm-gray file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 @error('images') border-red-500 @enderror"
                            >
                            <p class="mt-1 text-xs text-warm-gray">
                                Format: JPG, PNG, WebP. Maksimal 2MB per file. Gambar pertama akan menjadi gambar utama.
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
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="h-4 w-4 text-pink-600 focus:ring-pink-500 border-border-subtle rounded"
                    >
                    <label for="is_active" class="ml-2 block text-sm text-warm-white">
                        Aktifkan produk (tampilkan di katalog)
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                    <a
                        href="{{ route('admin.products.index') }}"
                        class="px-4 py-2 text-sm font-medium text-warm-white bg-dark-secondary border border-border-subtle rounded-lg hover:bg-dark-tertiary transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Simpan Produk
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
