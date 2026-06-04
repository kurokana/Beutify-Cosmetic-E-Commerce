<x-admin-layout>
    <x-slot name="pageTitle">Edit Kategori</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center gap-4">
            <a
                href="{{ route('admin.categories.index') }}"
                class="text-warm-gray hover:text-warm-white transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Edit Kategori</h2>
                <p class="mt-1 text-sm text-warm-gray">Perbarui informasi kategori {{ $category->name }}</p>
            </div>
        </div>

        {{-- Form --}}
        <div class="bg-dark-secondary rounded-lg shadow">
            <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-warm-white mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name', $category->name) }}"
                        required
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('name') border-red-500 @enderror"
                        placeholder="Contoh: Lipstik"
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-medium text-warm-white mb-2">
                        Deskripsi
                    </label>
                    <textarea
                        name="description"
                        id="description"
                        rows="4"
                        class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent @error('description') border-red-500 @enderror"
                        placeholder="Deskripsi singkat tentang kategori..."
                    >{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-border-subtle">
                    <a
                        href="{{ route('admin.categories.index') }}"
                        class="px-4 py-2 text-sm font-medium text-warm-white bg-dark-secondary border border-border-subtle rounded-lg hover:bg-dark-tertiary transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Batal
                    </a>
                    <button
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                    >
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
