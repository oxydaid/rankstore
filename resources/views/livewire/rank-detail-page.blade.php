<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden">
    
    {{-- Background Glow --}}
    <div class="absolute top-0 left-0 -translate-y-1/2 w-[600px] h-[600px] bg-primary/20 blur-[150px] rounded-full pointer-events-none z-[-1]"></div>
    <div class="absolute bottom-0 right-0 translate-y-1/2 w-[500px] h-[500px] bg-secondary/10 blur-[150px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        
        {{-- Breadcrumb --}}
        <nav class="flex mb-8 text-sm font-medium text-gray-400">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-primary transition">Home</a></li>
                <li><span class="text-gray-600">/</span></li>
                <li><a href="{{ route('shop') }}" class="hover:text-primary transition">Shop</a></li>
                <li><span class="text-gray-600">/</span></li>
                <li class="text-white">{{ $rank->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">
            
            {{-- KOLOM KIRI: GAMBAR PRODUK --}}
            <div class="relative group">
                {{-- Decorative Border --}}
                <div class="absolute -inset-1 bg-linear-to-r from-primary to-secondary rounded-3xl blur opacity-30 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                
                <div class="relative rounded-3xl bg-dark-800 ring-1 ring-white/10 overflow-hidden shadow-2xl">
                    @if($rank->image)
                        <img src="{{ asset('img/'.$rank->image) }}" alt="{{ $rank->name }}" class="w-full h-auto object-cover transform transition duration-500 group-hover:scale-105">
                    @else
                        {{-- Fallback Image jika tidak ada upload --}}
                        <div class="w-full aspect-square flex items-center justify-center bg-dark-900">
                            <svg class="w-32 h-32 text-gray-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Badge Kategori --}}
                    <div class="absolute top-4 left-4 bg-black/60 backdrop-blur-md px-4 py-2 rounded-full border border-white/10">
                        <span class="text-sm font-bold text-white tracking-wide uppercase">{{ $rank->category->name }}</span>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: DETAIL INFO --}}
            <div>
                <h1 class="text-4xl lg:text-5xl font-heading font-bold text-white mb-2 uppercase">{{ $rank->name }}</h1>
                
                {{-- Harga --}}
                <div class="flex flex-wrap items-end gap-4 mb-8">
                    
                    {{-- Harga Utama Besar --}}
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-primary">Rp</span>
                        <span class="text-5xl font-heading font-bold text-white leading-none tracking-tight">
                            {{ number_format($rank->price, 0, ',', '.') }}
                        </span>
                    </div>

                    {{-- Harga Coret & Badge Hemat --}}
                    @if($rank->slice_price)
                        <div class="flex flex-col justify-end pb-1">
                            <span class="text-lg font-medium text-gray-500 line-through decoration-red-500 decoration-2">
                                Rp {{ number_format($rank->slice_price, 0, ',', '.') }}
                            </span>
                            <span class="bg-red-500/10 text-red-400 text-xs font-bold px-2 py-1 rounded border border-red-500/20 mt-1 w-fit">
                                HEMAT {{ round((($rank->slice_price - $rank->price) / $rank->slice_price) * 100) }}%
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Deskripsi Pendek --}}
                <div class="prose prose-invert text-gray-400 mb-8">
                    <p>
                        Rank eksklusif untuk server mode <strong>{{ $rank->category->name }}</strong>. 
                        Dapatkan akses ke fitur premium dan kit spesial untuk mempercepat progress permainanmu.
                    </p>
                </div>

                {{-- Fitur & Benefit --}}
                <div class="mb-8">
                    <h3 class="text-lg font-heading text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Hak Akses & Fitur
                    </h3>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @if($rank->description)
                            @foreach($rank->description as $item)
                                <li class="flex items-start gap-3 bg-dark-800/50 p-3 rounded-lg border border-white/5">
                                    <svg class="w-5 h-5 text-green-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span class="text-sm text-gray-300">{{ $item['feature'] }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="text-gray-500 italic">Tidak ada deskripsi fitur.</li>
                        @endif
                    </ul>
                </div>

                {{-- Bonus Kits (INI BAGIAN PENTINGNYA) --}}
                @if($rank->kits)
                    <div class="mb-10">
                        <h3 class="text-lg font-heading text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Bonus Item (Kits)
                        </h3>
                        {{-- 
                           PERUBAHAN LOGIKA LAYOUT:
                           Mobile: grid grid-cols-2 (2 Kolom Fix)
                           Desktop: sm:flex sm:flex-wrap (Flexible width)
                        --}}
                        <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-3">
                            @foreach($rank->kits as $kit)
                                {{-- 
                                   Card Item: 
                                   Mobile: Flex Col (Icon atas, Teks bawah, Center)
                                   Desktop: Flex Row (Icon kiri, Teks kanan, Left)
                                --}}
                                <div class="group relative flex flex-col sm:flex-row items-center sm:items-center gap-2 sm:gap-3 bg-linear-to-br from-dark-800 to-dark-900 p-3 sm:pr-5 rounded-xl border border-white/10 hover:border-secondary/50 transition-all hover:-translate-y-1 shadow-lg text-center sm:text-left h-full sm:h-auto">
                                    <div class="w-8 h-8 rounded-lg bg-secondary/20 flex items-center justify-center text-secondary shrink-0">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                                    </div>
                                    <span class="text-sm font-bold text-white group-hover:text-secondary transition-colors leading-tight">{{ $kit['name'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Action Button --}}
                <div class="border-t border-white/10 pt-8">
                    <a href="{{ route('checkout', $rank->id) }}" class="w-full sm:w-auto inline-flex justify-center items-center gap-3 rounded-xl bg-linear-to-r from-primary to-secondary px-8 py-4 text-base font-heading font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/50 hover:scale-[1.02] transition-all duration-300">
                        <span>LANJUT KE PEMBAYARAN</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                    <p class="mt-4 text-xs text-gray-500 text-center sm:text-left">
                        *Dengan membeli, Anda menyetujui <a href="{{ route('terms') }}" class="underline hover:text-white">Syarat & Ketentuan</a> kami.
                    </p>
                </div>

            </div>
        </div>
    </div>
</div>