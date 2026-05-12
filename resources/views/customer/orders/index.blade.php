<x-app-layout>
    <div class="py-12 bg-[#FFF9FB] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header seperti halaman Katalog --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-[#FFD1DC]/40 pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-[#89CFF0]">Riwayat</span>
                        <span class="text-[#E86FA3]">Pesanan</span>
                    </h1>
                    <p class="text-slate-400 text-sm mt-1 font-medium">Lihat semua pesanan Anda</p>
                </div>
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-white border border-[#FFD1DC] text-[#E86FA3] text-[11px] font-black uppercase tracking-widest rounded-xl shadow-sm">
                        Total {{ $orders->total() }} Pesanan
                    </span>
                </div>
            </div>

            @if ($orders->isEmpty())
                <div class="bg-white rounded-3xl border border-[#FFD1DC]/50 shadow-sm p-12 text-center">
                    <svg class="mx-auto w-20 h-20 text-[#FFD1DC] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Belum ada pesanan</h2>
                    <p class="text-slate-400 text-sm mb-6">Anda belum pernah melakukan pembelian. Mulai belanja sekarang!</p>
                    <a href="{{ route('catalog.index') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#E86FA3] text-white rounded-xl font-semibold hover:bg-[#d45a92] transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="space-y-5">
                    @foreach ($orders as $order)
                        <div class="bg-white rounded-2xl border border-[#FFD1DC]/40 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden">
                            {{-- Header Order --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-b border-[#FFD1DC]/30 bg-gradient-to-r from-white to-[#FFF9FB]">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="font-bold text-gray-800 text-sm">{{ $order->order_number }}</span>
                                    @php
                                        $statusConfig = [
                                            'pending_payment'   => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-amber-100 text-amber-800 border-amber-200'],
                                            'payment_confirmed' => ['label' => 'Pembayaran Dikonfirmasi', 'class' => 'bg-blue-100 text-blue-800 border-blue-200'],
                                            'processing'        => ['label' => 'Diproses', 'class' => 'bg-purple-100 text-purple-800 border-purple-200'],
                                            'shipped'           => ['label' => 'Dikirim', 'class' => 'bg-indigo-100 text-indigo-800 border-indigo-200'],
                                            'delivered'         => ['label' => 'Selesai', 'class' => 'bg-green-100 text-green-800 border-green-200'],
                                            'cancelled'         => ['label' => 'Dibatalkan', 'class' => 'bg-red-100 text-red-800 border-red-200'],
                                        ];
                                        $status = $statusConfig[$order->status] ?? ['label' => $order->status, 'class' => 'bg-gray-100 text-gray-800 border-gray-200'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $status['class'] }}">
                                        {{ $status['label'] }}
                                    </span>
                                </div>
                                <span class="text-xs text-slate-400 font-medium">
                                    {{ $order->created_at->translatedFormat('d F Y, H:i') }}
                                </span>
                            </div>

                            {{-- Body Order --}}
                            <div class="px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <p class="text-xs text-slate-400 mb-1">
                                        {{ $order->items->count() }} produk
                                        &bull; {{ strtoupper($order->courier_name) }} {{ $order->courier_service }}
                                    </p>
                                    <p class="text-xl font-extrabold text-gray-800">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 flex-wrap">
                                    @if ($order->status === 'pending_payment')
                                        <a href="{{ route('orders.show', $order->id) }}"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#E86FA3] text-white rounded-lg text-sm font-bold hover:bg-[#d45a92] transition shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Bayar Sekarang
                                        </a>
                                    @endif
                                    <a href="{{ route('orders.show', $order->id) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 border border-[#FFD1DC] text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:border-[#E86FA3] transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if ($orders->hasPages())
                    <div class="mt-8">
                        {{ $orders->links() }}
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>