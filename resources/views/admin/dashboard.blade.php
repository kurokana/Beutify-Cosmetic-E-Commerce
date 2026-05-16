<x-admin-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>

    {{-- Welcome Message --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h2>
        <p class="text-gray-500 mt-1">Berikut adalah ringkasan aktivitas toko Anda hari ini.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

        {{-- Total Produk --}}
        <div class="bg-white rounded-xl shadow-sm border border-[#FFD1DC]/50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-[#FFE4ED] rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#D15788]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Produk</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_products']) }}</p>
            </div>
        </div>

        {{-- Total Pesanan --}}
        <div class="bg-white rounded-xl shadow-sm border border-[#BDEBFF]/50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-[#EAF8FF] rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#4BAED8]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Total Pesanan</p>
                <p class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_orders']) }}</p>
            </div>
        </div>

        {{-- Total Pengguna --}}
        <div class="bg-white rounded-xl shadow-sm border border-[#E9E7FF]/50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-[#F3E8FF] rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#7C3AED]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pengguna</p>
                <p class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_users']) }}</p>
            </div>
        </div>

        {{-- Total Pendapatan --}}
        <div class="bg-white rounded-xl shadow-sm border border-[#FFD1DC]/50 p-6 flex items-center gap-4">
            <div class="w-12 h-12 bg-[#FFE4ED] rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-[#D15788]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-slate-500 font-medium">Total Pendapatan</p>
                <p class="text-2xl font-bold text-slate-900">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
        </div>

    </div>

    {{-- Quick Navigation --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Akses Cepat</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-3">
            @php
                $quickLinks = [
                    ['route' => 'admin.products.index', 'label' => 'Produk', 'color' => 'blue'],
                    ['route' => 'admin.brands.index', 'label' => 'Merek', 'color' => 'indigo'],
                    ['route' => 'admin.categories.index', 'label' => 'Kategori', 'color' => 'violet'],
                    ['route' => 'admin.orders.index', 'label' => 'Pesanan', 'color' => 'green'],
                    ['route' => 'admin.users.index', 'label' => 'Pengguna', 'color' => 'purple'],
                    ['route' => 'admin.vouchers.index', 'label' => 'Voucher', 'color' => 'pink'],
                    ['route' => 'admin.reports.index', 'label' => 'Laporan', 'color' => 'orange'],
                ];
            @endphp

            @foreach ($quickLinks as $link)
                @if (\Illuminate\Support\Facades\Route::has($link['route']))
                    <a href="{{ route($link['route']) }}"
                       class="flex flex-col items-center justify-center p-4 rounded-lg bg-[#FFF8FB] hover:bg-[#FFF0F6] transition-colors text-center gap-2 border border-[#FFD1DC]/50">
                        <span class="text-sm font-medium text-[#475569]">{{ $link['label'] }}</span>
                    </a>
                @else
                    <span class="flex flex-col items-center justify-center p-4 rounded-lg bg-gray-50 text-center gap-2 cursor-not-allowed opacity-50">
                        <span class="text-sm font-medium text-gray-500">{{ $link['label'] }}</span>
                    </span>
                @endif
            @endforeach
        </div>
    </div>

</x-admin-layout>
