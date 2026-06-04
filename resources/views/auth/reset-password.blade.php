<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Beutify') }} - Reset Kata Sandi</title>

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
                <span class="text-gold-light">Reset</span>
                <span class="text-gold">Kata Sandi</span>
            </h2>
            <p class="text-warm-muted text-sm mt-1">
                Buat kata sandi baru untuk akun Anda
            </p>
        </div>

        <form method="POST" action="{{ route('password.store') }}">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-warm-white font-semibold text-sm mb-1">Alamat Email</label>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                    class="w-full rounded-xl bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/40 py-2.5 px-4 placeholder:text-warm-muted">

                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="block text-warm-white font-semibold text-sm mb-1">Kata Sandi Baru</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full rounded-xl bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/40 py-2.5 px-4 placeholder:text-warm-muted">

                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <label for="password_confirmation" class="block text-warm-white font-semibold text-sm mb-1">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full rounded-xl bg-dark-tertiary border-border-subtle text-warm-white focus:border-gold focus:ring-gold/40 py-2.5 px-4 placeholder:text-warm-muted">

                @error('password_confirmation')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end mt-6">
                <button type="submit" class="bg-gold hover:bg-[#d45a92] rounded-full px-6 py-2 font-semibold text-white transition">
                    Reset Kata Sandi
                </button>
            </div>
        </form>
    </div>

</body>
</html>