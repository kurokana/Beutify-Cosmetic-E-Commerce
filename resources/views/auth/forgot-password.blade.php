<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FFF9FB]">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-[#FFD1DC]/40 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-black tracking-tight">
                    <span class="text-[#89CFF0]">Lupa</span>
                    <span class="text-[#E86FA3]">Kata Sandi</span>
                </h2>
                <p class="text-slate-400 text-sm mt-1">Masukkan email Anda, kami akan kirimkan tautan reset</p>
            </div>

            <div class="mb-4 text-sm text-slate-500">
                {{ __('Lupa kata sandi? Tenang. Beri tahu kami alamat email Anda dan kami akan mengirimkan tautan reset kata sandi yang memungkinkan Anda memilih yang baru.') }}
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="text-gray-700 font-semibold text-sm" />
                    <x-text-input id="email" class="block mt-1 w-full rounded-xl border-[#FFD1DC]/70 focus:border-[#E86FA3] focus:ring-[#E86FA3]" 
                        type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-xs" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button class="bg-[#E86FA3] hover:bg-[#d45a92] rounded-full px-6 py-2 font-semibold">
                        {{ __('Kirim Tautan Reset') }}
                    </x-primary-button>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('login') }}" class="text-sm text-[#89CFF0] hover:text-[#E86FA3] transition-colors">
                        Kembali ke halaman login
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>