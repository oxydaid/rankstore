<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden">

    {{-- Background Glow Effect --}}
    <div
        class="absolute top-0 right-0 -translate-x-1/2 w-[500px] h-[500px] bg-primary/20 blur-[120px] rounded-full pointer-events-none z-[-1]">
    </div>
    <div
        class="absolute bottom-0 left-0 translate-y-1/2 w-[400px] h-[400px] bg-secondary/10 blur-[100px] rounded-full pointer-events-none z-[-1]">
    </div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8">

        {{-- HEADER SECTION --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h1 class="text-4xl font-heading font-bold text-white sm:text-5xl text-glow mb-4">
                STORE <span
                    class="text-transparent bg-clip-text bg-linear-to-r from-primary to-secondary">CATALOG</span>
            </h1>
            <p class="text-gray-400 text-lg">
                Jelajahi berbagai pilihan Rank eksklusif untuk meningkatkan pengalaman bermainmu di <b>{{ $settings?->site_name ?? 'Minecraft Server' }}</b>.
            </p>
        </div>

        {{-- CONTROL BAR (Search & Filter) --}}
        <div class="mb-12 space-y-6">

            {{-- Search Bar --}}
            <div class="max-w-md mx-auto relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500 group-focus-within:text-primary transition-colors" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text"
                    class="block w-full pl-4 pr-10 py-3 border border-white/10 rounded-xl leading-5 bg-dark-800/80 text-gray-300 placeholder-gray-500 focus:outline-none focus:bg-dark-900 focus:border-primary focus:ring-1 focus:ring-primary sm:text-sm transition-all backdrop-blur-md shadow-lg"
                    placeholder="Cari nama rank (contoh: VIP)...">
                {{-- Loading Indicator (PERBAIKAN: wire:loading dipindah ke SVG) --}}
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg wire:loading wire:target="search" class="animate-spin h-5 w-5 text-primary"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>

            {{-- Category Tabs --}}
            <div class="flex flex-wrap justify-center gap-3">
                @foreach($categories as $cat)
                            <button wire:click="setCategory('{{ $cat->slug }}')" class="px-5 py-2 rounded-full font-heading text-xs sm:text-sm transition-all duration-300 border border-transparent
                                    {{ $category === $cat->slug
                    ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-105'
                    : 'bg-dark-800/50 text-gray-400 border-white/5 hover:border-white/20 hover:text-white hover:bg-dark-700' 
                                    }}">
                                {{ $cat->name }}
                            </button>
                @endforeach
            </div>
        </div>

        <div class="relative min-h-[400px]">

            <div wire:loading.delay.longest
                class="absolute inset-0 z-10 bg-dark-900/50 backdrop-blur-sm rounded-3xl flex items-center justify-center">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-primary mb-4" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-white font-heading">Memuat Produk...</span>
                </div>
            </div>

            {{-- Grid Container --}}
            <div class="flex flex-wrap justify-center gap-8">
                @forelse($ranks as $rank)
                    <div
                        class="flex flex-col justify-between rounded-3xl bg-dark-800/60 p-8 ring-1 ring-white/10 backdrop-blur-md hover:ring-primary/50 transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden w-full sm:w-[350px] lg:w-[380px] shrink-0">

                        @if($rank->slice_price)
                            {{-- Ubah -top-4 menjadi top-0 dan rounded-full menjadi rounded-b-xl agar terlihat menggantung --}}
                            <div
                                class="absolute top-0 left-1/2 -translate-x-1/2 rounded-b-xl bg-linear-to-r from-red-600 to-red-500 px-4 py-1.5 text-xs font-heading font-bold text-white shadow-lg z-20">
                                PROMO HOT
                            </div>
                        @endif

                        {{-- Hover Gradient --}}
                        <div
                            class="absolute inset-0 bg-linear-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        </div>

                        {{-- Card Header --}}
                        <div>
                            <div class="flex items-center justify-between gap-x-4">
                                <h3 class="text-xl font-heading font-bold text-white tracking-wide uppercase">
                                    {{ $rank->name }}</h3>
                                <div
                                    class="rounded-full bg-white/5 ring-1 ring-white/10 px-2.5 py-1 text-xs font-semibold leading-5 text-gray-300">
                                    {{ $rank->category->name }}
                                </div>
                            </div>

                            {{-- Pricing --}}
                            <div class="mt-6">
                                {{-- Harga Utama (Satu Baris Rapi) --}}
                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-bold text-primary">Rp</span>
                                    <span class="text-3xl font-heading font-bold text-white leading-none tracking-tight">
                                        {{ number_format($rank->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- Harga Coret (Di bawahnya) --}}
                                @if($rank->slice_price)
                                    <div class="mt-1 text-xs font-semibold text-gray-500 line-through decoration-red-500 decoration-2">
                                        Rp {{ number_format($rank->slice_price, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            {{-- Feature List (Limited to 4) --}}
                            <ul role="list" class="mt-8 space-y-3 text-sm leading-6 text-gray-300">
                                @if($rank->description)
                                    @foreach(array_slice($rank->description, 0, 4) as $item)
                                        <li class="flex gap-x-3 items-start">
                                            <span class="text-secondary shrink-0 mt-0.5">
                                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                            {{ $item['feature'] }}
                                        </li>
                                    @endforeach

                                    @if(count($rank->description) > 4)
                                        <li class="text-xs text-gray-500 italic pl-8">+ {{ count($rank->description) - 4 }} fitur
                                            lainnya</li>
                                    @endif
                                @else
                                    <li class="text-gray-500 italic">Deskripsi fitur belum tersedia.</li>
                                @endif
                            </ul>
                        </div>

                        {{-- Action Button --}}
                        <a href="{{ route('rank.detail', $rank->id) }}"
                            class="mt-8 block rounded-lg bg-dark-700 px-3 py-3 text-center text-sm font-heading font-semibold leading-6 text-white shadow-sm hover:bg-linear-to-r hover:from-primary hover:to-secondary transition-all duration-300 z-10 group-hover:shadow-lg group-hover:shadow-primary/20">
                            BELI SEKARANG
                        </a>
                    </div>
                @empty
                    {{-- Empty State --}}
                    <div class="col-span-full w-full py-12 text-center">
                        <div
                            class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-dark-800 ring-1 ring-white/10 mb-6 animate-pulse">
                            <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-heading text-white mb-2">Tidak Ditemukan</h3>
                        <p class="text-gray-400 max-w-sm mx-auto">
                            Maaf, kami tidak dapat menemukan rank dengan kata kunci
                            <span class="text-primary font-bold">"{{ $search }}"</span>
                            @if($category !== 'all') di kategori ini @endif.
                        </p>
                        <button wire:click="$set('search', '')"
                            class="mt-6 text-sm text-primary hover:text-white underline transition">
                            Reset Pencarian
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>