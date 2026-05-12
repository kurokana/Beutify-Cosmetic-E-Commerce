<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FFF9FB]">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-[#FFD1DC]/40 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-black tracking-tight">
                    <span class="text-[#89CFF0]">Konfirmasi</span>
                    <span class="text-[#E86FA3]">Kata Sandi</span>
                </h2>
                <p class="text-slate-400 text-sm mt-1">Harap konfirmasi kata sandi Anda sebelum melanjutkan</p>
            </div>

            <div class="mb-4 text-sm text-slate-500">
                {{ __('Ini adalah area aman aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.') }}
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <!-- Password -->
                <div>
                    <x-input-label for="password" :value="__('Kata Sandi')" class="text-gray-700 font-semibold text-sm" />
                    <x-text-input id="password" class="block mt-1 w-full rounded-xl border-[#FFD1DC]/70 focus:border-[#E86FA3] focus:ring-[#E86FA3]"
                        type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-xs" />
                </div>

                <div class="flex items-center justify-end mt-6">
                    <x-primary-button class="bg-[#E86FA3] hover:bg-[#d45a92] rounded-full px-6 py-2 font-semibold">
                        {{ __('Konfirmasi') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>