<x-admin-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="space-y-6">
        {{-- Hero Summary --}}
        <div class="relative overflow-hidden rounded-[28px] border border-pink-300/40 bg-[radial-gradient(circle_at_top_left,_rgba(236,72,153,0.16),_transparent_35%),linear-gradient(135deg,#f9c2e8_0%,#dbeafe_40%,#bef264_100%)] p-8 shadow-[0_25px_60px_-24px_rgba(236,72,153,0.38)]">
            <div class="absolute -top-10 -left-10 h-32 w-32 rounded-full bg-[#fb7185]/35 blur-2xl"></div>
            <div class="absolute bottom-0 right-0 h-28 w-28 rounded-full bg-[#34d399]/30 blur-2xl"></div>
            <div class="relative flex flex-col xl:flex-row gap-6 items-start xl:items-center justify-between">
                <div class="max-w-2xl">
                    <p class="inline-flex items-center rounded-full bg-[#fce7f3] px-3 py-1 text-[11px] font-bold uppercase tracking-[0.3em] text-[#be185d]">Dashboard Admin</p>
                    <h1 class="mt-4 text-3xl sm:text-4xl font-black text-slate-900">Selamat Datang, {{ Auth::user()->name }}!</h1>
                    <p class="mt-4 text-slate-600 leading-7">Pantau performa toko, kelola produk, pesanan, pengguna, serta laporan dengan tampilan yang lebih segar, cerah, dan sesuai karakter skincare.</p>
                </div>
                <div class="grid grid-cols-2 gap-3 min-w-[280px]">
                    <div class="rounded-2xl bg-gradient-to-br from-white via-pink-100 to-fuchsia-100/90 px-4 py-3 border border-pink-200/60 shadow-[0_10px_30px_-20px_rgba(236,72,153,0.45)]">
                        <p class="text-[11px] uppercase tracking-[0.24em] text-[#9d174d]">Produk</p>
                        <p class="mt-2 text-2xl font-black text-[#be185d]">{{ number_format($stats['total_products']) }}</p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-white via-cyan-100 to-sky-100/90 px-4 py-3 border border-cyan-200/60 shadow-[0_10px_30px_-20px_rgba(14,165,233,0.45)]">
                        <p class="text-[11px] uppercase tracking-[0.24em] text-[#0f766e]">Pesanan</p>
                        <p class="mt-2 text-2xl font-black text-[#0f766e]">{{ number_format($stats['total_orders']) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[2fr_1fr]">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Total Produk --}}
                <div class="group rounded-[24px] border border-pink-300/70 bg-[linear-gradient(180deg,#fee2e2_0%,#fbcfe8_100%)] p-6 shadow-[0_20px_50px_-24px_rgba(236,72,153,0.38)] transition-transform duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-[#be185d] font-semibold">Total Produk</p>
                            <p class="mt-3 text-3xl font-black text-[#9d174d]">{{ number_format($stats['total_products']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[linear-gradient(135deg,#f9a8d4_0%,#fb7185_100%)] flex items-center justify-center shadow-inner">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-[#7f1d1d]">Periksa dan kelola stok produk agar katalog tetap segar dan terorganisir.</p>
                </div>

                {{-- Total Pesanan --}}
                <div class="group rounded-[24px] border border-cyan-300/70 bg-[linear-gradient(180deg,#dbeafe_0%,#bae6fd_100%)] p-6 shadow-[0_20px_50px_-24px_rgba(14,165,233,0.34)] transition-transform duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-[#075985] font-semibold">Total Pesanan</p>
                            <p class="mt-3 text-3xl font-black text-[#075985]">{{ number_format($stats['total_orders']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[linear-gradient(135deg,#7dd3fc_0%,#38bdf8_100%)] flex items-center justify-center shadow-inner">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-[#0f5464]">Konfirmasi pesanan dan cek detail pengiriman dengan cepat.</p>
                </div>

                {{-- Total Pengguna --}}
                <div class="group rounded-[24px] border border-purple-300/70 bg-[linear-gradient(180deg,#e9d5ff_0%,#c4b5fd_100%)] p-6 shadow-[0_20px_50px_-24px_rgba(124,58,237,0.32)] transition-transform duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-[#6d28d9] font-semibold">Total Pengguna</p>
                            <p class="mt-3 text-3xl font-black text-[#4c1d95]">{{ number_format($stats['total_users']) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[linear-gradient(135deg,#c4b5fd_0%,#8b5cf6_100%)] flex items-center justify-center shadow-inner">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-[#5b21b6]">Jumlah pengguna aktif yang sudah terdaftar sebagai pelanggan.</p>
                </div>

                {{-- Total Pendapatan --}}
                <div class="group rounded-[24px] border border-yellow-300/70 bg-[linear-gradient(180deg,#fef3c7_0%,#fcd34d_100%)] p-6 shadow-[0_20px_50px_-24px_rgba(234,179,8,0.35)] transition-transform duration-200 hover:-translate-y-1">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-sm text-[#b45309] font-semibold">Total Pendapatan</p>
                            <p class="mt-3 text-3xl font-black text-[#92400e]">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-[linear-gradient(135deg,#fef9c3_0%,#fbbf24_100%)] flex items-center justify-center shadow-inner">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="mt-4 text-sm text-[#92400e]">Pendapatan total dari semua pesanan aktif yang telah terkonfirmasi.</p>
                </div>
            </div>

            <div class="rounded-[24px] border border-fuchsia-300/60 bg-[linear-gradient(180deg,#fdf2f8_0%,#f5f3ff_100%)] p-6 shadow-[0_20px_50px_-24px_rgba(190,24,93,0.28)] h-full">
                <div class="mb-6">
                    <h2 class="text-lg font-black text-[#9d174d]">Notifikasi Pesanan Masuk</h2>
                    <p class="mt-1 text-sm text-[#6d28d9]">Pesanan baru dari pelanggan yang masuk ke admin panel.</p>
                </div>

                <div class="space-y-4 overflow-y-auto max-h-[340px] pr-2 scrollbar-thin scrollbar-thumb-pink-300 scrollbar-track-violet-100">
                    @forelse ($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}" class="block rounded-[22px] bg-[linear-gradient(135deg,#fff1f7_0%,#e9d5ff_70%)] p-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-[0_16px_30px_-22px_rgba(139,92,246,0.35)] focus:outline-none focus:ring-2 focus:ring-[#a855f7]">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-[#5b21b6]">Pesanan #{{ $order->order_number ?? $order->id }}</p>
                                    <p class="mt-1 text-sm text-[#4c1d95]">Dipesan oleh {{ $order->user->name ?? 'Pengguna' }} • Total Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <span class="text-xs font-semibold text-[#c026d3]">{{ $order->created_at->diffForHumans() }}</span>
                            </div>
                        </a>
                    @empty
                        <p class="text-sm text-[#6d28d9]">Belum ada notifikasi pesanan masuk.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-[24px] border border-fuchsia-200/80 bg-[linear-gradient(180deg,#fdf2f8_0%,#f4f1ff_100%)] p-6 shadow-[0_20px_50px_-24px_rgba(124,58,237,0.18)]">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-lg font-black text-[#7e22ce]">Pesanan Terbaru</h2>
                    <p class="mt-1 text-sm text-[#6d28d9]">Lima pesanan terakhir pada toko kamu.</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-bold text-[#be185d] hover:text-[#db2777]">Lihat semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="bg-[linear-gradient(135deg,#fde8ff_0%,#e9d5ff_100%)] text-[#6d028d] uppercase text-[11px] tracking-[0.28em]">
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
                            <tr class="border-b border-slate-100 hover:bg-[#fff8fb]">
                                <td class="px-4 py-4 font-bold text-slate-900">{{ $order->order_number ?? $order->id }}</td>
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
