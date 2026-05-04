<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="{{ $description ?? 'Toko kosmetik online terpercaya dengan produk berkualitas dari berbagai merek ternama.' }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Kosmetik Store') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-gray-50 text-gray-900">

        {{-- Loading Indicator --}}
        <x-loading-indicator />

        {{-- Main Navigation --}}
        @include('layouts.navigation')

        {{-- Page Content --}}
        <main class="min-h-screen">
            {{ $slot }}
        </main>

        {{-- Footer --}}
        <footer class="bg-gray-900 text-gray-300 mt-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div>
                        <h3 class="text-white font-bold text-lg mb-4">{{ config('app.name', 'Kosmetik Store') }}</h3>
                        <p class="text-sm leading-relaxed">
                            Toko kosmetik online terpercaya dengan produk berkualitas dari berbagai merek ternama.
                        </p>
                    </div>

                    <!-- Navigasi -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Navigasi</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('catalog.index') }}" class="hover:text-white transition-colors">Katalog Produk</a></li>
                            @auth
                                <li><a href="{{ route('wishlist.index') }}" class="hover:text-white transition-colors">Wishlist</a></li>
                                <li><a href="{{ route('orders.index') }}" class="hover:text-white transition-colors">Pesanan Saya</a></li>
                            @else
                                <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Daftar</a></li>
                            @endauth
                        </ul>
                    </div>

                    <!-- Bantuan -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Bantuan</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-white transition-colors">Cara Berbelanja</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Kebijakan Pengembalian</a></li>
                            <li><a href="#" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                        </ul>
                    </div>

                    <!-- Kontak -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Kontak</h4>
                        <ul class="space-y-2 text-sm">
                            <li>Email: support@kosmetikstore.id</li>
                            <li>WhatsApp: +62 812-3456-7890</li>
                            <li>Senin–Sabtu, 09.00–18.00 WIB</li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-700 mt-10 pt-6 text-sm text-center text-gray-500">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Kosmetik Store') }}. Semua hak dilindungi.
                </div>
            </div>
        </footer>

        {{-- Toast Notifications --}}
        <x-toast />

        @stack('scripts')
    </body>
</html>
