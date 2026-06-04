<x-admin-layout>
    <x-slot name="pageTitle">Manajemen Kategori</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Kategori</h2>
                <p class="mt-1 text-sm text-warm-gray">Kelola semua kategori produk di toko Anda</p>
            </div>
            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </a>
        </div>

        {{-- Categories Table --}}
        <div class="bg-dark-secondary rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-dark-tertiary">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Kategori
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Jumlah Produk
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-dark-secondary divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-dark-tertiary transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-warm-white">
                                            {{ $category->name }}
                                        </div>
                                        @if ($category->description)
                                            <div class="text-sm text-warm-gray max-w-md">
                                                {{ $category->description }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-warm-white">
                                        {{ $category->products_count }} produk aktif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('admin.categories.edit', $category) }}"
                                            class="text-pink-600 hover:text-pink-900 transition-colors"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                        <form
                                            action="{{ route('admin.categories.destroy', $category) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Hapus"
                                            >
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-warm-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>
                                        <p class="text-warm-gray text-sm">Belum ada kategori</p>
                                        <a
                                            href="{{ route('admin.categories.create') }}"
                                            class="mt-4 text-pink-600 hover:text-pink-700 text-sm font-medium"
                                        >
                                            Tambah kategori pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-border-subtle">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
