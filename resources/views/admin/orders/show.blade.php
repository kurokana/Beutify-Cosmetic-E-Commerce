<x-admin-layout>
    <x-slot name="pageTitle">Detail Pesanan #{{ $order->order_number }}</x-slot>

    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-2"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Pesanan
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Pesanan #{{ $order->order_number }}</h2>
                <p class="mt-1 text-sm text-gray-600">
                    Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Order Items --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Produk Pesanan</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach ($order->items as $item)
                            <div class="px-6 py-4 flex items-center gap-4">
                                @if ($item->product && $item->product->images->first())
                                    <img
                                        src="{{ Storage::url($item->product->images->first()->image_path) }}"
                                        alt="{{ $item->product_name }}"
                                        class="w-16 h-16 rounded-lg object-cover"
                                    >
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-gray-200 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900">{{ $item->product_name }}</h4>
                                    @if ($item->variant_name)
                                        <p class="text-sm text-gray-500">{{ $item->variant_name }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500">Jumlah: {{ $item->quantity }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500">
                                        Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-900">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Customer Information --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Pelanggan</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Nama</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->user->email }}</p>
                        </div>
                        @if ($order->user->phone)
                            <div>
                                <p class="text-sm text-gray-500">Telepon</p>
                                <p class="text-sm font-medium text-gray-900">{{ $order->user->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Shipping Address --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Alamat Pengiriman</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Penerima</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->address->recipient_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telepon</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->address->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Alamat Lengkap</p>
                            <p class="text-sm font-medium text-gray-900">{{ $order->address->full_address }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $order->address->district }}, {{ $order->address->city }}, {{ $order->address->province }} {{ $order->address->postal_code }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Order Summary --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Ringkasan Pesanan</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @if ($order->discount_amount > 0)
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Diskon</span>
                                <span class="font-medium text-green-600">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Ongkos Kirim</span>
                            <span class="font-medium text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-200 flex justify-between">
                            <span class="text-base font-semibold text-gray-900">Total</span>
                            <span class="text-base font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Information --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Pembayaran</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Metode Pembayaran</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ $order->payment->payment_method ?? 'Belum dipilih' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Status Pembayaran</p>
                            @php
                                $paymentStatusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'success' => 'bg-green-100 text-green-800',
                                    'failed' => 'bg-red-100 text-red-800',
                                    'expired' => 'bg-gray-100 text-gray-800',
                                    'cancelled' => 'bg-gray-100 text-gray-800',
                                ];
                                $paymentStatusLabels = [
                                    'pending' => 'Pending',
                                    'success' => 'Berhasil',
                                    'failed' => 'Gagal',
                                    'expired' => 'Kedaluwarsa',
                                    'cancelled' => 'Dibatalkan',
                                ];
                                $paymentStatus = $order->payment->status ?? 'pending';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full {{ $paymentStatusColors[$paymentStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $paymentStatusLabels[$paymentStatus] ?? $paymentStatus }}
                            </span>
                        </div>
                        @if ($order->payment->paid_at)
                            <div>
                                <p class="text-sm text-gray-500">Tanggal Pembayaran</p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $order->payment->paid_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Shipping Information --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi Pengiriman</h3>
                    </div>
                    <div class="px-6 py-4 space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Kurir</p>
                            <p class="text-sm font-medium text-gray-900">
                                {{ strtoupper($order->courier_name) }} - {{ $order->courier_service }}
                            </p>
                        </div>
                        @if ($order->shipping_tracking_number)
                            <div>
                                <p class="text-sm text-gray-500">Nomor Resi</p>
                                <p class="text-sm font-medium text-gray-900">{{ $order->shipping_tracking_number }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Update Status --}}
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Update Status</h3>
                    </div>
                    <div class="px-6 py-4 space-y-4">
                        {{-- Current Status --}}
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Status Saat Ini</p>
                            @php
                                $orderStatusColors = [
                                    'pending_payment' => 'bg-yellow-100 text-yellow-800',
                                    'payment_confirmed' => 'bg-blue-100 text-blue-800',
                                    'processing' => 'bg-purple-100 text-purple-800',
                                    'shipped' => 'bg-indigo-100 text-indigo-800',
                                    'delivered' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                ];
                                $orderStatusLabels = [
                                    'pending_payment' => 'Menunggu Pembayaran',
                                    'payment_confirmed' => 'Pembayaran Dikonfirmasi',
                                    'processing' => 'Diproses',
                                    'shipped' => 'Sedang Dikirim',
                                    'delivered' => 'Selesai',
                                    'cancelled' => 'Dibatalkan',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-full {{ $orderStatusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $orderStatusLabels[$order->status] ?? $order->status }}
                            </span>
                        </div>

                        {{-- Update Status Form --}}
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-3">
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Ubah Status
                                    </label>
                                    <select
                                        id="status"
                                        name="status"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                    >
                                        @foreach ($orderStatusLabels as $value => $label)
                                            <option value="{{ $value }}" {{ $order->status === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button
                                    type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-lg hover:bg-pink-700 transition-colors focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2"
                                >
                                    Update Status
                                </button>
                            </div>
                        </form>

                        {{-- Tracking Number Form --}}
                        @if (in_array($order->status, ['payment_confirmed', 'processing']))
                            <div class="pt-4 border-t border-gray-200">
                                <form action="{{ route('admin.orders.update-tracking', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="space-y-3">
                                        <div>
                                            <label for="shipping_tracking_number" class="block text-sm font-medium text-gray-700 mb-1">
                                                Nomor Resi
                                            </label>
                                            <input
                                                type="text"
                                                id="shipping_tracking_number"
                                                name="shipping_tracking_number"
                                                value="{{ $order->shipping_tracking_number }}"
                                                placeholder="Masukkan nomor resi"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-pink-500 focus:ring-pink-500"
                                                required
                                            >
                                        </div>
                                        <button
                                            type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                        >
                                            Simpan Resi & Kirim
                                        </button>
                                        <p class="text-xs text-gray-500">
                                            Status akan otomatis berubah menjadi "Sedang Dikirim"
                                        </p>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Order Notes --}}
                @if ($order->notes)
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Catatan Pesanan</h3>
                        </div>
                        <div class="px-6 py-4">
                            <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
