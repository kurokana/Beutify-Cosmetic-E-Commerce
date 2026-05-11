<x-guest-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-[#FFF9FB]">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-[#FFD1DC]/40 p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-black tracking-tight">
                    <span class="text-[#89CFF0]">Verifikasi</span>
                    <span class="text-[#E86FA3]">Email</span>
                </h2>
                <p class="text-slate-400 text-sm mt-1">Konfirmasi alamat email Anda</p>
            </div>

            <div class="mb-4 text-sm text-slate-500">
                {{ __('Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengklik tautan yang baru saja kami kirimkan melalui email? Jika Anda tidak menerima email, kami akan dengan senang hati mengirimkan yang lain.') }}
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 border border-green-200 rounded-lg p-3">
                    {{ __('Tautan verifikasi baru telah dikirim ke alamat email yang Anda berikan saat pendaftaran.') }}
                </div>
            @endif

            <div class="mt-4 flex flex-col gap-3">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <x-primary-button class="w-full bg-[#E86FA3] hover:bg-[#d45a92] rounded-full py-2 font-semibold justify-center">
                        {{ __('Kirim Ulang Email Verifikasi') }}
                    </x-primary-button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-center text-sm text-[#89CFF0] hover:text-[#E86FA3] transition-colors">
                        {{ __('Keluar') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>