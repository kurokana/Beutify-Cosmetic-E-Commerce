<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beutify') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-dark-primary min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">

    {{-- Card login --}}
    <div class="max-w-md w-full bg-dark-secondary rounded-2xl shadow-xl border border-border-subtle p-8">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-black tracking-tight">
                <span class="text-gold-light">Masuk</span>
                <span class="text-gold">Beutify</span>
            </h2>
            <p class="text-warm-muted text-sm mt-1">Silakan masuk ke akun Anda</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-warm-white font-semibold text-sm mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                    class="w-full rounded-xl bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/40 py-2.5 px-4 placeholder:text-warm-muted">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-warm-white font-semibold text-sm mb-1">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full rounded-xl bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/40 py-2.5 px-4 placeholder:text-warm-muted">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="block mt-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="rounded border-gold/30 text-gold shadow-sm focus:ring-gold/40">
                    <span class="ms-2 text-sm text-warm-gray">Ingat saya</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="text-sm text-gold-light hover:text-gold transition-colors" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif

                <button type="submit" class="btn-gold rounded-full px-6 py-2 font-semibold transition">
                    Masuk
                </button>
            </div>

            <div class="text-center mt-6">
                <p class="text-sm text-warm-muted">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-gold font-semibold hover:underline">Daftar sekarang</a>
                </p>
            </div>
        </form>
    </div>

</body>
</html>