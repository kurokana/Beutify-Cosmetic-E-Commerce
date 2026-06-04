<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beutify') }} - Lupa Kata Sandi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-dark-primary min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    <div class="max-w-md w-full bg-dark-secondary rounded-2xl shadow-xl border border-border-subtle p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black tracking-tight">
                <span class="text-gold-light">Lupa</span>
                <span class="text-gold">Kata Sandi</span>
            </h2>
            <p class="text-warm-muted text-sm mt-1">
                Masukkan email Anda untuk menerima tautan reset
            </p>
        </div>

        <div class="mb-4 text-sm text-warm-gray leading-relaxed">
            Lupa kata sandi? Tenang, masukkan alamat email Anda dan kami akan mengirimkan tautan untuk membuat kata sandi baru.
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-warm-white font-semibold text-sm mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-xl bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/40 py-2.5 px-4 placeholder:text-warm-muted">

                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between mt-6">
                <a href="{{ route('login') }}" class="text-sm text-gold-light hover:text-gold transition-colors">
                    Kembali ke login
                </a>

                <button type="submit" class="bg-gold hover:bg-[#d45a92] rounded-full px-6 py-2 font-semibold text-white transition">
                    Kirim Link
                </button>
            </div>
        </form>
    </div>

</body>
</html>