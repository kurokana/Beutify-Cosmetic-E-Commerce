<x-app-layout>
    <div class="py-12 bg-dark-primary min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Header: Disamakan persis dengan Shopping Cart --}}
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 border-b border-border-subtle pb-6 gap-4">
                <div>
                    <h1 class="text-3xl md:text-2xl font-black tracking-tight leading-tight">
                        <span class="text-gold text-glow">Katalog</span> 
                        <span class="text-warm-white">Beutify</span>
                    </h1>
                    <p class="text-warm-gray text-sm mt-1 font-medium">Temukan produk kecantikan terbaik untuk kulitmu.</p>
                </div>
                
                {{-- Menampilkan jumlah total produk sebagai pemanis (opsional) --}}
                <div class="flex items-center">
                    <span class="px-4 py-1.5 bg-dark-secondary border border-gold/30 text-gold text-[11px] font-black uppercase tracking-widest rounded-xl shadow-gold-sm">
                        {{ $products->total() }} Produk Tersedia
                    </span>
                </div>
            </div>

            {{-- FILTER BAR AREA --}}
            <div class="mb-10">
                <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                    <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                        {{-- Kategori --}}
                        <select name="category_id" onchange="this.form.submit()" 
                            class="h-12 px-4 rounded-full bg-dark-secondary border border-border-subtle text-xs font-bold text-warm-gray focus:ring-gold/30 focus:border-gold transition cursor-pointer min-w-[130px]">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Merk --}}
                        <select name="brand_id" onchange="this.form.submit()" 
                            class="h-12 px-4 rounded-full bg-dark-secondary border border-border-subtle text-xs font-bold text-warm-gray focus:ring-gold/30 focus:border-gold transition cursor-pointer min-w-[130px]">
                            <option value="">Semua Merk</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Urutkan --}}
                        <select name="sort" onchange="this.form.submit()" 
                            class="h-12 px-4 rounded-full bg-dark-secondary border border-border-subtle text-xs font-bold text-gold focus:ring-gold/30 focus:border-gold transition cursor-pointer">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga Terendah</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        </select>

                        {{-- Sortir Harga --}}
                        <div class="flex items-center gap-1 bg-dark-secondary border border-border-subtle rounded-full px-2 h-12">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-16 border-none bg-transparent text-[11px] text-warm-white focus:ring-0 placeholder:text-warm-muted font-bold">
                            <span class="text-warm-muted">-</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-16 border-none bg-transparent text-[11px] text-warm-white focus:ring-0 placeholder:text-warm-muted font-bold">
                            <button type="submit" class="p-1.5 text-gold hover:text-gold-light">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                            </button>
                        </div>
                    </div>

                    @if(request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif
                </form>
            </div>

            {{-- Grid Produk --}}
            @if($products->isEmpty())
                <div class="text-center py-20 bg-dark-secondary rounded-3xl border border-border-subtle shadow-sm">
                    <p class="text-warm-muted font-medium">Tidak ada produk yang ditemukan.</p>
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>