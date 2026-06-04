<x-admin-layout>
    <x-slot name="pageTitle">Laporan Penjualan</x-slot>

    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-warm-white">Laporan Penjualan</h2>
        <p class="text-warm-gray mt-1">Ringkasan penjualan harian serta rasio uang dan produk masuk/keluar.</p>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6 mb-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label for="start_date" class="block text-sm font-medium text-warm-white mb-2">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}"
                       class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label for="end_date" class="block text-sm font-medium text-warm-white mb-2">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}"
                       class="w-full px-4 py-2 border border-border-subtle rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            </div>
            <div>
                <button type="submit"
                        class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors font-medium">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Summary Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
        {{-- Uang Masuk --}}
        <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-pink-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-warm-gray font-medium">Uang Masuk</p>
                    <p class="text-2xl font-bold text-warm-white">Rp {{ number_format($summary['money_in'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Uang Keluar --}}
        <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-warm-gray font-medium">Uang Keluar</p>
                    <p class="text-2xl font-bold text-warm-white">Rp {{ number_format($summary['money_out'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Produk Keluar --}}
        <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-warm-gray font-medium">Produk Keluar</p>
                    <p class="text-2xl font-bold text-warm-white">{{ number_format($summary['products_out']) }}</p>
                </div>
            </div>
        </div>

        {{-- Produk Masuk --}}
        <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 7l8-4 8 4m-16 0l8 4m-8-4v10l8 4m0-10l8-4m-8 4v10"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-warm-gray font-medium">Produk Masuk</p>
                    <p class="text-2xl font-bold text-warm-white">{{ number_format($summary['products_in']) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6">
            <h3 class="text-lg font-semibold text-warm-white mb-4">Rasio Uang Masuk/Keluar</h3>
            <div class="h-64">
                <canvas id="moneyFlowChart"></canvas>
            </div>
        </div>

        <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6">
            <h3 class="text-lg font-semibold text-warm-white mb-4">Rasio Produk Keluar/Masuk</h3>
            <div class="h-64">
                <canvas id="productFlowChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Best Selling Products Table --}}
    <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6 mb-8">
        <h3 class="text-lg font-semibold text-warm-white mb-4">Produk Terlaris</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border-subtle">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-warm-white">Peringkat</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-warm-white">Nama Produk</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-warm-white">Jumlah Terjual</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-warm-white">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bestSellingProducts as $index => $product)
                        <tr class="border-b border-border-subtle hover:bg-dark-tertiary">
                            <td class="py-3 px-4 text-sm text-warm-gray">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 text-sm font-medium text-warm-white">{{ $product['product_name'] }}</td>
                            <td class="py-3 px-4 text-sm text-warm-gray text-right">{{ number_format($product['total_quantity']) }}</td>
                            <td class="py-3 px-4 text-sm text-warm-gray text-right">Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-warm-gray">
                                Tidak ada data produk terlaris untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Daily Sales Table --}}
    <div class="bg-dark-secondary rounded-xl shadow-sm border border-border-subtle p-6 mb-8">
        <h3 class="text-lg font-semibold text-warm-white mb-4">Detail Penjualan Harian</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border-subtle">
                        <th class="text-left py-3 px-4 text-sm font-semibold text-warm-white">Tanggal</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-warm-white">Jumlah Pesanan</th>
                        <th class="text-right py-3 px-4 text-sm font-semibold text-warm-white">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dailySales['data'] as $day)
                        <tr class="border-b border-border-subtle hover:bg-dark-tertiary">
                            <td class="py-3 px-4 text-sm text-warm-white">{{ date('d M Y', strtotime($day['date'])) }}</td>
                            <td class="py-3 px-4 text-sm text-warm-gray text-right">{{ number_format($day['total_orders']) }}</td>
                            <td class="py-3 px-4 text-sm text-warm-gray text-right">Rp {{ number_format($day['total_revenue'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-8 text-center text-warm-gray">
                                Tidak ada data penjualan untuk periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart.js Script --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Chart configuration
        const chartColors = {
            primary: 'rgb(219, 39, 119)', // pink-600
            secondary: 'rgb(147, 51, 234)', // purple-600
            success: 'rgb(34, 197, 94)', // green-600
            info: 'rgb(59, 130, 246)', // blue-600
        };

        // Money Flow Chart
        const moneyFlowCtx = document.getElementById('moneyFlowChart').getContext('2d');
        new Chart(moneyFlowCtx, {
            type: 'doughnut',
            data: {
                labels: ['Uang Masuk', 'Uang Keluar'],
                datasets: [
                    {
                        data: [@json($moneyFlow['in']), @json($moneyFlow['out'])],
                        backgroundColor: [
                            'rgba(219, 39, 119, 0.9)',
                            'rgba(239, 68, 68, 0.9)',
                        ],
                        borderColor: ['#ffffff', '#ffffff'],
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed || 0;
                                return context.label + ': Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

        // Product Flow Chart
        const productFlowCtx = document.getElementById('productFlowChart').getContext('2d');
        new Chart(productFlowCtx, {
            type: 'doughnut',
            data: {
                labels: ['Produk Keluar', 'Produk Masuk'],
                datasets: [
                    {
                        data: [@json($productFlow['out']), @json($productFlow['in'])],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.9)',
                            'rgba(147, 51, 234, 0.9)',
                        ],
                        borderColor: ['#ffffff', '#ffffff'],
                        borderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed || 0;
                                return context.label + ': ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    </script>

</x-admin-layout>
