<x-app-layout>
    <div class="relative py-8 bg-[#FFF5F8] min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- HEADER SEARCH --}}
            <div class="mb-10 text-center max-w-3xl mx-auto">
                <h1 class="text-2xl font-black mb-6">
                    <span class="text-slate-400 uppercase text-xs tracking-[0.3em] block mb-2">Hasil Pencarian Untuk</span>
                    <span class="text-[#E86FA3]">"{{ $keyword }}"</span>
                </h1>

                <form method="GET" action="{{ route('search') }}" class="flex flex-col md:flex-row gap-3">
                    {{-- Search Bar Nav Style --}}
                    <div class="relative flex-1">
                        <input
                            type="text"
                            name="q"
                            value="{{ $keyword }}"
                            placeholder="Cari skincare, makeup, serum..."
                            class="w-full h-14 pl-6 pr-14 rounded-full bg-white border border-[#FFD1DC]/80 text-sm text-slate-600 focus:border-[#89CFF0] focus:ring-4 focus:ring-[#BDEBFF]/40 shadow-sm transition"
                        >
                        <button type="submit" class="absolute right-2 top-2 w-10 h-10 rounded-full bg-[#E86FA3] text-white flex items-center justify-center hover:bg-[#D9578F] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.6-5.15a6.75 6.75 0 11-13.5 0 6.75 6.75 0 0113.5 0z" /></svg>
                        </button>
                    </div>

                    {{-- Sort Dropdown --}}
                    <select name="sort" onchange="this.form.submit()"
                        class="h-14 px-6 rounded-full bg-white border border-[#FFD1DC]/80 text-xs font-bold text-[#E86FA3] focus:border-[#89CFF0] focus:ring-4 focus:ring-[#BDEBFF]/40 shadow-sm transition min-w-[160px]">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                        <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                    </select>
                </form>
            </div>

            {{-- RESULTS GRID --}}
            @if ($products->isEmpty())
                <div class="bg-white rounded-[2rem] border border-[#FFD1DC]/60 shadow-[0_14px_35px_rgba(248,187,208,0.12)] p-16 text-center">
                    <p class="text-slate-400 font-bold">Maaf, kami tidak dapat menemukan "{{ $keyword }}"</p>
                    <a href="{{ route('catalog.index') }}" class="inline-block mt-6 text-[#E86FA3] font-black text-xs uppercase tracking-widest border-b-2 border-[#FFD1DC] hover:border-[#E86FA3] transition">Kembali ke Katalog</a>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach ($products as $product)
                        @php $primaryImage = $product->images->firstWhere('is_primary', true) ?? $product->images->first(); @endphp
                        <div class="group bg-white rounded-[1.5rem] border border-[#FFD1DC]/60 shadow-[0_8px_20px_rgba(248,187,208,0.06)] overflow-hidden hover:shadow-[0_15px_35px_rgba(137,207,240,0.15)] hover:-translate-y-1 transition-all duration-300">
                            <a href="{{ route('catalog.show', $product->slug) }}">
                                <div class="relative m-2 aspect-square rounded-xl bg-white p-2">
                                    <img src="{{ Storage::url($primaryImage->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-500">
                                </div>
                                <div class="p-4 pt-0">
                                    <h3 class="text-sm font-bold text-slate-800 line-clamp-2 h-10">{{ $product->name }}</h3>
                                    <p class="mt-2 text-sm font-black text-[#E86FA3]">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->appends(['q' => $keyword, 'sort' => request('sort')])->links() }}
                </div>
            @endif

        </div>
    </div>
</x-app-layout>