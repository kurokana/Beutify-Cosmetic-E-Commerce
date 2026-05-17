<x-admin-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    <div class="space-y-6">
        {{-- Hero Summary --}}
        <div class="bg-gradient-to-r from-[#FDF2F8] via-white to-[#EFF6FF] border border-[#FCE7F3] rounded-3xl p-8 shadow-sm">
            <div class="flex flex-col xl:flex-row gap-6 items-start xl:items-center justify-between">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#BE185D] mb-3">Dashboard Admin</p>
                    <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">Selamat Datang, {{ Auth::user()->name }}!</h1>
                    <p class="mt-4 text-slate-600 leading-7">Pantau performa toko dan kelola produk, pesanan, pengguna, serta laporan dengan cepat dari satu tampilan yang bersih dan intuitif.</p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 xl:grid-cols-[1.4fr_0.85fr]">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                {{-- Total Produk --}}
                <div class="bg-white rounded-3xl border border-[#FFD1DC]/60 p-6 shadow-sm">
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
                <div class="bg-white rounded-3xl border border-[#BDEBFF]/60 p-6 shadow-sm">
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
                <div class="bg-white rounded-3xl border border-[#E9E7FF]/60 p-6 shadow-sm">
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
                <div class="bg-white rounded-3xl border border-[#FFD1DC]/60 p-6 shadow-sm">
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

            <div class="space-y-6">
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-slate-900">Ringkasan Toko</h2>
                    <p class="mt-3 text-sm text-slate-500">Semua kebutuhan manajemen toko ada di sini. Gunakan akses cepat untuk langsung menuju fitur penting.</p>

                    <div class="mt-6 grid gap-4">
                        <div class="flex items-center gap-4 rounded-3xl bg-[#FEF3C7] p-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#FDE68A]/70 text-[#92400E]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18M3 14h18M3 18h18"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Status operasional</p>
                                <p class="text-sm text-slate-500">Semua sistem toko berjalan normal saat ini.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 rounded-3xl bg-[#ECFDF5] p-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#A7F3D0]/70 text-[#047857]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Promosi dan voucher</p>
                                <p class="text-sm text-slate-500">Cek voucher terbaru untuk dorong penjualan.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 rounded-3xl bg-[#EFF6FF] p-4">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-[#BFDBFE]/70 text-[#1D4ED8]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Kinerja toko</p>
                                <p class="text-sm text-slate-500">Lihat ringkasan dan pastikan semua indikator berjalan baik.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-admin-layout>
