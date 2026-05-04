<x-app-layout>
    <x-slot name="header">
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('catalog.index') }}" class="hover:text-pink-600">Katalog</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">Riwayat Pesanan</span>
        </nav>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <h1 class="text-2xl font-bold text-gray-900 mb-6">Riwayat Pesanan</h1>

            @if ($orders->isEmpty())
                {{-- Empty state --}}
                <div class="bg-white rounded-xl shadow-sm p-12 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h2 class="text-lg font-semibold text-gray-700 mb-2">Belum ada pesanan</h2>
                    <p class="text-sm text-gray-500 mb-6">Anda belum pernah melakukan pembelian. Mulai belanja sekarang!</p>
                    <a href="{{ route('catalog.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-pink-600 text-white rounded-xl font-semibold hover:bg-pink-700 transition text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                            {{-- Order Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="font-semibold text-gray-900 text-sm">{{ $order->order_number }}</span>
                                    @php
                                        $statusConfig = [
                                            'pending_payment'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-yellow-100 text-yellow-800'],
                                            'payment_confirmed' => ['label' => 'Pembayaran Dikonfirmasi', 'class' => 'bg-blue-100 text-blue-800'],
                                            'processing'        => ['label' => 'Diproses', 'class' => 'bg-purple-100 text-purple-800'],
                                            'shipped'           => ['label' => 'Dikirim', 'class' => 'bg-indigo-100 text-indigo-800'],
                                            'delivered'         => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800'],
                                            'cancelled'         => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800'],
                                        ];
                                        $status = $statusConfig[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400">
                                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                                </span>
                            </div>

                            {{-- Order Body --}}
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">
                                        {{ $order->items_count ?? $order->items->count() }} produk
                                        &bull; {{ strtoupper($order->courier_name) }} {{ $order->courier_service }}
                                    </p>
                                    <p class="text-base font-bold text-gray-900">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    @if ($order->status === 'pending_payment')
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-pink-600 text-white rounded-lg text-sm font-medium hover:bg-pink-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
