<x-admin-layout>
    <x-slot name="pageTitle">Manajemen Voucher</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Voucher</h2>
                <p class="mt-1 text-sm text-warm-gray">Kelola semua voucher diskon di toko Anda</p>
            </div>
            <a
                href="{{ route('admin.vouchers.create') }}"
                class="inline-flex items-center justify-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
            >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Voucher
            </a>
        </div>

        {{-- Vouchers Table --}}
        <div class="bg-dark-secondary rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-dark-tertiary">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Kode
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Tipe & Nilai
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Penggunaan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-dark-secondary divide-y divide-gray-200">
                        @forelse ($vouchers as $voucher)
                            <tr class="hover:bg-dark-tertiary transition-colors">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-bold text-warm-white font-mono">
                                            {{ $voucher->code }}
                                        </div>
                                        <div class="text-xs text-warm-gray mt-1">
                                            Min. Pembelian: Rp {{ number_format($voucher->minimum_purchase, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-warm-white">
                                        @if ($voucher->type === 'percentage')
                                            <span class="font-semibold text-pink-600">{{ $voucher->value }}%</span>
                                            <span class="text-warm-gray">Diskon</span>
                                        @else
                                            <span class="font-semibold text-pink-600">Rp {{ number_format($voucher->value, 0, ',', '.') }}</span>
                                            <span class="text-warm-gray">Potongan</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-warm-white">
                                        {{ $voucher->used_count }}
                                        @if ($voucher->max_usage)
                                            / {{ $voucher->max_usage }}
                                        @else
                                            / ∞
                                        @endif
                                    </div>
                                    @if ($voucher->max_usage && $voucher->used_count >= $voucher->max_usage)
                                        <div class="text-xs text-red-600 mt-1">
                                            Batas tercapai
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="space-y-1">
                                        @if ($voucher->is_active && !$voucher->expires_at->isPast() && (!$voucher->max_usage || $voucher->used_count < $voucher->max_usage))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @elseif ($voucher->expires_at->isPast())
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Kedaluwarsa
                                            </span>
                                        @elseif ($voucher->max_usage && $voucher->used_count >= $voucher->max_usage)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Habis
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-dark-tertiary text-warm-white">
                                                Nonaktif
                                            </span>
                                        @endif
                                        <div class="text-xs text-warm-gray">
                                            Exp: {{ $voucher->expires_at->format('d M Y') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Toggle Active --}}
                                        <form
                                            action="{{ route('admin.vouchers.toggle-active', $voucher) }}"
                                            method="POST"
                                            class="inline"
                                        >
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="text-blue-600 hover:text-blue-900 transition-colors"
                                                title="{{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                            >
                                                @if ($voucher->is_active)
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('admin.vouchers.edit', $voucher) }}"
                                            class="text-pink-600 hover:text-pink-900 transition-colors"
                                            title="Edit"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('admin.vouchers.destroy', $voucher) }}"
                                            method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus voucher ini?')"
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-warm-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                                        </svg>
                                        <p class="text-warm-gray text-sm">Belum ada voucher</p>
                                        <a
                                            href="{{ route('admin.vouchers.create') }}"
                                            class="mt-4 text-pink-600 hover:text-pink-700 text-sm font-medium"
                                        >
                                            Tambah voucher pertama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($vouchers->hasPages())
                <div class="px-6 py-4 border-t border-border-subtle">
                    {{ $vouchers->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
