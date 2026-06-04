<x-admin-layout>
    <x-slot name="pageTitle">Manajemen Pengguna</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-warm-white">Pengguna</h2>
                <p class="mt-1 text-sm text-warm-gray">Kelola akun pelanggan terdaftar</p>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-dark-secondary rounded-lg shadow p-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Search Filter --}}
                <div class="md:col-span-2">
                    <label for="search" class="block text-sm font-medium text-warm-white mb-1">
                        Cari Pengguna
                    </label>
                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama atau email..."
                        class="w-full rounded-lg bg-dark-tertiary border-border-subtle text-warm-white shadow-sm focus:border-gold focus:ring-gold/20 placeholder:text-warm-muted"
                    >
                </div>

                {{-- Status Filter --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-warm-white mb-1">
                        Status Akun
                    </label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border-border-subtle shadow-sm focus:border-pink-500 focus:ring-pink-500"
                    >
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            Aktif
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>
                </div>

                {{-- Filter Buttons --}}
                <div class="md:col-span-3 flex gap-2">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gold text-white text-sm font-medium rounded-lg hover:bg-gold-dark transition-colors focus:outline-none focus:ring-2 focus:ring-gold/40 focus:ring-offset-2"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari
                    </button>
                    <a
                        href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-dark-tertiary text-[#475569] text-sm font-medium rounded-lg hover:bg-dark-elevated transition-colors"
                    >
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="bg-dark-secondary rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-dark-tertiary">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Nama
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Email
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Tanggal Pendaftaran
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Jumlah Pesanan
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Status Akun
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-warm-gray uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-dark-secondary divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-dark-elevated transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gold/40 flex items-center justify-center">
                                                <span class="text-gold font-medium text-sm">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-warm-white">
                                                {{ $user->name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-warm-white">{{ $user->email }}</div>
                                    @if ($user->email_verified_at)
                                        <div class="text-xs text-green-600 flex items-center mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Terverifikasi
                                        </div>
                                    @else
                                        <div class="text-xs text-warm-gray flex items-center mt-1">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Belum Terverifikasi
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-warm-white">
                                        {{ $user->created_at->format('d M Y') }}
                                    </div>
                                    <div class="text-sm text-warm-gray">
                                        {{ $user->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-warm-white">
                                        {{ $user->orders_count }} pesanan
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                            </svg>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.toggle-active', $user) }}"
                                        class="inline"
                                        onsubmit="return confirm('Apakah Anda yakin ingin {{ $user->is_active ? 'menonaktifkan' : 'mengaktifkan' }} akun ini?')"
                                    >
                                        @csrf
                                        @method('PATCH')
                                        <button
                                            type="submit"
                                            class="text-gold hover:text-gold-dark transition-colors"
                                            title="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} Akun"
                                        >
                                            @if ($user->is_active)
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                                                </svg>
                                            @else
                                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-warm-muted mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="text-warm-gray text-sm">Belum ada pengguna terdaftar</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($users->hasPages())
                <div class="px-6 py-4 border-t border-border-subtle">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
