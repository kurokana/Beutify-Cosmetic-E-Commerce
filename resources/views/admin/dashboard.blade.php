<x-admin-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="space-y-6">
        {{-- Hero Summary --}}
        <div class="bg-gradient-to-r from-[#FDF2F8]/70 via-white/75 to-[#EFF6FF]/70 backdrop-blur-md border border-[#FCE7F3] rounded-3xl p-8 shadow-sm">
            <div class="flex flex-col xl:flex-row gap-6 items-start xl:items-center justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#BE185D] mb-3">Dashboard Admin</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">Selamat Datang, {{ Auth::user()->name }}!</h1>
                    <p class="mt-4 text-slate-600 leading-7">Pantau performa toko dan kelola produk, pesanan, pengguna, serta laporan dengan cepat dari satu tampilan yang bersih dan intuitif.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[2fr_1fr]">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Total Produk --}}
                <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-[#FFD1DC]/60 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Produk</p>
                            <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stats['total_products']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[#FFE4ED] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#D15788]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Periksa dan kelola stok produk untuk memastikan semua jadi rapi.</p>
                </div>

                {{-- Total Pesanan --}}
                <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-[#BDEBFF]/60 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Total Pesanan</p>
                            <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stats['total_orders']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[#EAF8FF] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#4BAED8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Konfirmasi pesanan dan cek detail pengiriman dengan cepat.</p>
                </div>

                {{-- Total Pengguna --}}
                <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-[#E9E7FF]/60 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-gray-500 font-medium">Total Pengguna</p>
                            <p class="mt-3 text-3xl font-bold text-slate-900">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[#F3E8FF] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Jumlah pengguna aktif yang sudah terdaftar sebagai pelanggan.</p>
                </div>

                {{-- Total Pendapatan --}}
                <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-[#FFD1DC]/60 p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-slate-500 font-medium">Total Pendapatan</p>
                            <p class="mt-3 text-3xl font-bold text-slate-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[#FFE4ED] flex items-center justify-center">
                            <svg class="w-6 h-6 text-[#D15788]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-slate-500">Pendapatan total dari semua pesanan aktif yang telah terkonfirmasi.</p>
                </div>
            </div>

            <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-gray-100 p-6 shadow-sm h-full">
                <div class="mb-6">
                    <h2 class="text-lg font-semibold text-slate-900">Notifikasi Pesanan Masuk</h2>
                    <p class="mt-1 text-sm text-slate-500">Pesanan baru dari pelanggan yang masuk ke admin panel.</p>
                </div>

                <div class="space-y-4 overflow-y-auto max-h-[340px] pr-2 scrollbar-thin scrollbar-thumb-slate-300 scrollbar-track-slate-100">
                    @forelse ($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="block rounded-3xl bg-[#F8FAFC] p-4 transition hover:bg-[#eef4fb] focus:outline-none focus:ring-2 focus:ring-[#D15788]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">Pesanan #{{ $order->order_number ?? $order->id }}</p>
                                    <p class="mt-1 text-sm text-slate-500">Dipesan oleh {{ $order->user->name ?? 'Pengguna' }} • Total Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <span class="text-xs text-slate-400">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">Belum ada notifikasi pesanan masuk.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="bg-white/70 backdrop-blur-md rounded-3xl border border-gray-100 p-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-slate-900">Pesanan Terbaru</h2>
                    <p class="mt-1 text-sm text-slate-500">Lima pesanan terakhir pada toko kamu.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-[#BE185D] hover:text-[#D15788]">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs tracking-[0.2em]">
                        <tr>
                            <th class="px-4 py-3">#</th>
                            <th class="px-4 py-3">Pelanggan</th>
                            <th class="px-4 py-3">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentOrders as $order)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="px-4 py-4 font-medium text-slate-900">{{ $order->order_number ?? $order->id }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $order->user->name ?? '-' }}</td>
                                <td class="px-4 py-4 text-slate-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ $order->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">Tidak ada pesanan terbaru untuk ditampilkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
