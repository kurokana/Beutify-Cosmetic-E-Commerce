<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beutify') }} - Konfirmasi Kata Sandi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-cover bg-center bg-no-repeat bg-fixed" style="background-image: url('{{ asset('images/BG.png') }}')">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-[#FFD1DC]/40 p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black tracking-tight">
                <span class="text-[#89CFF0]">Konfirmasi</span>
                <span class="text-[#E86FA3]">Kata Sandi</span>
            </h2>
            <p class="text-slate-400 text-sm mt-1">
                Masukkan ulang kata sandi Anda
            </p>
        </div>

        <div class="mb-4 text-sm text-slate-500 leading-relaxed">
            Ini adalah area aman aplikasi. Silakan konfirmasi kata sandi Anda sebelum melanjutkan.
        </div>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div>
                <label for="password" class="block text-gray-700 font-semibold text-sm mb-1">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full rounded-xl border-[#FFD1DC]/70 focus:border-[#E86FA3] focus:ring-[#E86FA3] py-2.5 px-4">

                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="bg-[#E86FA3] hover:bg-[#d45a92] rounded-full px-6 py-2 font-semibold text-white transition">
                    Konfirmasi
                </button>
            </div>
        </form>
    </div>

</body>
</html>
