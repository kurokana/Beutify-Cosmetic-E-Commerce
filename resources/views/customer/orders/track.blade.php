<x-app-layout>
    <div class="py-12 bg-transparent min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Header seperti halaman Katalog --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-[#FFD1DC]/40 pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-[#89CFF0]">Lacak</span>
                        <span class="text-[#E86FA3]">Pesanan</span>
                    </h1>
                    <p class="text-slate-400 text-sm mt-1 font-medium">{{ $order->order_number }}</p>
                </div>
            </div>

            @if ($trackingError)
                <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                    </svg>
                    <div>
                        <p class="font-bold text-amber-800 text-sm">Pelacakan Tidak Tersedia</p>
                        <p class="text-amber-700 text-sm mt-0.5">{{ $trackingError }}</p>
                    </div>
                </div>
            @elseif ($tracking && ! $tracking['found'])
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6 flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold text-red-800 text-sm">Resi Tidak Ditemukan</p>
                        <p class="text-red-700 text-sm mt-0.5">{{ $tracking['message'] ?? 'Informasi pelacakan belum tersedia, silakan coba beberapa saat lagi' }}</p>
                    </div>
                </div>
            @elseif ($tracking && $tracking['found'])
                {{-- Informasi Paket --}}
                <div class="bg-white rounded-2xl border border-[#FFD1DC]/40 shadow-sm p-6 mb-5">
                    <h2 class="text-base font-black text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#E86FA3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        Informasi Paket
                    </h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                        <div>
                            <dt class="text-slate-400 font-medium">Nomor Resi</dt>
                            <dd class="font-bold font-mono text-gray-800 mt-0.5">{{ $tracking['waybill'] ?: $order->shipping_tracking_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-400 font-medium">Kurir</dt>
                            <dd class="font-bold text-gray-800 mt-0.5">{{ strtoupper($tracking['courier'] ?: $order->courier_name) }} @if($tracking['service'])<span class="font-normal text-slate-500"> — {{ $tracking['service'] }}</span>@endif</dd>
                        </div>
                        @if ($tracking['receiver_name'])
                            <div>
                                <dt class="text-slate-400 font-medium">Penerima</dt>
                                <dd class="font-bold text-gray-800 mt-0.5">{{ $tracking['receiver_name'] }}</dd>
                            </div>
                        @endif
                        @if ($tracking['status'])
                            <div>
                                <dt class="text-slate-400 font-medium">Status Terkini</dt>
                                <dd class="mt-0.5"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800">{{ $tracking['status'] }}</span></dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Riwayat Pengiriman --}}
                <div class="bg-white rounded-2xl border border-[#FFD1DC]/40 shadow-sm p-6">
                    <h2 class="text-base font-black text-gray-800 mb-5 flex items-center gap-2">
                        <svg class="w-5 h-5 text-[#E86FA3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Riwayat Pengiriman
                    </h2>
                    @if (!empty($tracking['manifest']))
                        <ol class="relative border-l border-[#FFD1DC] ml-3 space-y-0">
                            @foreach ($tracking['manifest'] as $index => $event)
                                <li class="mb-6 ml-6 last:mb-0">
                                    <span class="absolute -left-3 flex items-center justify-center w-6 h-6 rounded-full {{ $index === 0 ? 'bg-indigo-600' : 'bg-gray-200' }}">
                                        @if ($index === 0)
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                        @endif
                                    </span>
                                    <div class="{{ $index === 0 ? 'bg-indigo-50 border border-indigo-100' : 'bg-gray-50 border border-gray-100' }} rounded-lg p-3">
                                        <p class="text-sm font-bold {{ $index === 0 ? 'text-indigo-900' : 'text-gray-800' }}">
                                            {{ $event['manifest_description'] ?? $event['description'] ?? '-' }}
                                        </p>
                                        <time class="text-xs {{ $index === 0 ? 'text-indigo-600' : 'text-gray-500' }} mt-1 block">
                                            {{ $event['manifest_date'] ?? $event['date'] ?? '' }}{{ isset($event['manifest_date']) && isset($event['manifest_time']) ? ', ' : '' }}{{ $event['manifest_time'] ?? $event['time'] ?? '' }}
                                        </time>
                                        @if (!empty($event['city_name']))
                                            <p class="text-xs text-gray-500 mt-0.5">{{ $event['city_name'] }}</p>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-sm text-slate-400 text-center py-4">Belum ada riwayat pengiriman yang tersedia.</p>
                    @endif
                </div>
            @endif

            <div class="flex gap-3 mt-5">
                <a href="{{ route('orders.show', $order->id) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border border-[#FFD1DC] text-gray-700 rounded-xl font-medium hover:bg-gray-50 hover:border-[#E86FA3] transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke Detail Pesanan
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
