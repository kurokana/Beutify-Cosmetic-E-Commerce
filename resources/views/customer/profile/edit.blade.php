<x-app-layout>
    <style>
        .beauty-shadow { box-shadow: 0 18px 45px rgba(244, 194, 194, 0.22) !important; }
        .form-beauty-focus:focus { 
            border-color: #89CFF0 !important; 
            ring-color: #89CFF0 !important;
            box-shadow: 0 0 0 4px rgba(137, 207, 240, 0.2) !important;
        }
    </style>

    <div class="py-12 bg-transparent min-h-screen">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-[2.5rem] border border-[#FFD1DC] beauty-shadow overflow-hidden">
                <div class="px-8 py-6 border-b border-[#FFD1DC] bg-gradient-to-r from-[#EAF8FF] to-white">
                    <h3 class="text-lg font-bold text-slate-800">Edit Profil</h3>
                </div>

                <form method="POST" action="{{ route('customer.profile.update') }}" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-black text-[#E86FA3] uppercase tracking-widest mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                               class="w-full rounded-2xl border-[#FFD1DC] bg-[#FFF9FB]/50 font-bold text-slate-700 form-beauty-focus">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-[#E86FA3] uppercase tracking-widest mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="w-full rounded-2xl border-[#FFD1DC] bg-[#FFF9FB]/50 font-bold text-slate-700 form-beauty-focus">
                    </div>

                    <div class="flex items-center justify-between pt-6">
                        <a href="{{ route('customer.profile.index') }}" class="text-[10px] font-black uppercase text-slate-400">Batal</a>
                        <button type="submit" 
                                class="px-8 py-4 bg-gradient-to-r from-[#E86FA3] to-[#89CFF0] text-white text-[11px] font-black uppercase tracking-widest rounded-full shadow-lg hover:-translate-y-0.5 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
