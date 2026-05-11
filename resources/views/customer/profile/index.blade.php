<x-app-layout>
    <style>
        /* Memaksa warna agar sesuai dengan tema Beauty */
        .text-beauty-pink { color: #E86FA3 !important; }
        .bg-beauty-pink { background-color: #E86FA3 !important; }
        .text-beauty-blue { color: #89CFF0 !important; }
        .bg-beauty-blue { background-color: #89CFF0 !important; }
        .beauty-border { border-color: #FFD1DC !important; }
        .beauty-shadow { box-shadow: 0 18px 45px rgba(244, 194, 194, 0.22) !important; }
    </style>

    <x-slot name="header">
        <nav class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest">
            <span class="text-slate-400">Akun Saya</span>
            <span class="text-[#FFD1DC]">/</span>
            <span class="text-slate-800">Profil</span>
        </nav>
    </x-slot>

    <div class="py-12 bg-[#FFF9FC] min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Profile Card --}}
            <div class="bg-white rounded-[2.5rem] border beauty-border beauty-shadow overflow-hidden">
                <div class="flex items-center justify-between px-8 py-6 border-b beauty-border bg-gradient-to-r from-[#FFF1F6] to-white">
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-beauty-pink">Informasi Akun</p>
                        <h3 class="text-xl font-bold text-slate-800">Profil Pengguna</h3>
                    </div>
                    <a href="{{ route('customer.profile.edit') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border beauty-border rounded-full text-xs font-black uppercase tracking-widest text-beauty-pink hover:bg-[#FFF1F6] transition shadow-sm">
                        Edit Profil
                    </a>
                </div>
                
                <div class="px-8 py-8 grid grid-cols-1 sm:grid-cols-2 gap-8">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-beauty-pink uppercase tracking-widest">Nama Lengkap</p>
                        <p class="text-base font-bold text-slate-800">{{ $user->name }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-beauty-pink uppercase tracking-widest">Email</p>
                        <p class="text-base font-bold text-slate-800">{{ $user->email }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-beauty-blue uppercase tracking-widest">No. Telepon</p>
                        <p class="text-base font-bold text-slate-800">{{ $user->phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Alamat Card --}}
            <div class="bg-white rounded-[2.5rem] border beauty-border beauty-shadow overflow-hidden">
                <div class="px-8 py-6 border-b beauty-border">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Alamat</h3>
                </div>
                <div class="p-8">
                    @forelse ($user->addresses as $address)
                        <div class="p-4 mb-4 rounded-2xl border beauty-border bg-[#FFF9FB]/50">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-black uppercase text-slate-800">{{ $address->recipient_name }}</span>
                                @if($address->is_default)
                                    <span class="bg-beauty-pink text-white text-[9px] px-2 py-0.5 rounded-full uppercase font-black">Utama</span>
                                @endif
                            </div>
                            <p class="text-sm text-slate-600">{{ $address->full_address }}</p>
                        </div>
                    @empty
                        <p class="text-center text-slate-400 text-sm italic">Belum ada alamat.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>