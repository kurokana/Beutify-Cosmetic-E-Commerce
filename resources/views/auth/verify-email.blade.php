<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beutify') }} - Verifikasi Email</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#FFF9FB] min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-[#FFD1DC]/40 p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black tracking-tight">
                <span class="text-[#89CFF0]">Verifikasi</span>
                <span class="text-[#E86FA3]">Email</span>
            </h2>
            <p class="text-slate-400 text-sm mt-1">
                Konfirmasi alamat email Anda
            </p>
        </div>

        <div class="mb-4 text-sm text-slate-500 leading-relaxed">
            Terima kasih telah mendaftar. Sebelum mulai menggunakan akun, silakan verifikasi alamat email Anda melalui tautan yang sudah kami kirimkan.
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                Tautan verifikasi baru telah dikirim ke alamat email Anda.
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <button type="submit" class="w-full bg-[#E86FA3] hover:bg-[#d45a92] rounded-full px-6 py-2 font-semibold text-white transition">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="w-full text-center text-sm text-[#89CFF0] hover:text-[#E86FA3] transition-colors">
                    Keluar
                </button>
            </form>
        </div>
    </div>

</body>
</html>