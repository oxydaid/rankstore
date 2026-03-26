<div class="overflow-hidden">

    <div class="relative isolate px-6 pt-14 lg:px-8">

        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-[300px] h-[300px] bg-primary/30 blur-[100px] rounded-full pointer-events-none z-[-1]">
        </div>

        <div class="mx-auto max-w-3xl py-20 sm:py-32 text-center">

            <!-- Badge Partner -->
            <div class="mb-8 flex justify-center animate-fade-in-up">
                <div
                    class="relative rounded-full px-4 py-1.5 text-sm leading-6 text-gray-400 ring-1 ring-white/10 hover:ring-white/20 bg-white/5 backdrop-blur-sm transition-all hover:scale-105">
                    Official Store of <span class="font-bold text-primary">{{ $settings?->site_name ?? 'Minecraft Store' }}</span>
                </div>
            </div>

            <!-- Title Dinamis -->
            <h1
                class="text-5xl md:text-7xl font-heading font-bold tracking-tight text-white mb-4 text-glow animate-float">
                LEVEL UP YOUR <br>
                <span class="text-transparent bg-clip-text bg-linear-to-r from-primary to-secondary">MINECRAFT</span>
                JOURNEY
            </h1>

            @if($settings?->server_ip)
                <div x-data="{ copied: false }" class="mb-8 flex justify-center">
                    <button
                        @click="navigator.clipboard.writeText('{{ $settings->server_ip }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="group relative inline-flex items-center gap-3 rounded-xl bg-white/5 px-5 py-3 text-sm font-mono text-gray-300 ring-1 ring-white/10 transition-all hover:bg-white/10 hover:ring-primary/50 hover:scale-105">
                        <span class="flex h-3 w-3 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>

                        <span class="font-bold tracking-wide">{{ $settings->server_ip }}</span>
                        <span class="text-gray-600">|</span>
                        <span class="text-primary">{{ $settings->server_port }}</span>

                        <div class="ml-1 relative w-5 h-5">
                            <svg x-show="!copied"
                                class="absolute inset-0 w-5 h-5 text-gray-400 group-hover:text-white transition-colors"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <svg x-show="copied" x-cloak class="absolute inset-0 w-5 h-5 text-green-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <div x-show="copied" x-transition
                            class="absolute -top-10 left-1/2 -translate-x-1/2 rounded bg-primary px-2 py-1 text-xs font-bold text-white shadow-lg">
                            IP Copied!
                        </div>
                    </button>
                </div>
            @endif

            <p class="mt-6 text-lg leading-8 text-gray-300 max-w-2xl mx-auto">
                {{ $settings?->site_description ?? 'Dapatkan akses eksklusif, kit spesial, dan fitur premium lainnya dengan proses otomatis dan aman.' }}
            </p>

            <!-- CTA Buttons -->
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a href="#katalog"
                    class="rounded-lg bg-linear-to-r from-primary to-secondary px-8 py-3.5 text-base font-heading font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/50 transition-all duration-300 hover:-translate-y-1">
                    BELANJA SEKARANG
                </a>
                <a href="{{ route('tracking') }}"
                    class="text-sm font-semibold leading-6 text-white flex items-center gap-2 hover:text-primary transition group">
                    Cek Pesanan <span aria-hidden="true" class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            <!-- STATS GRID (REALTIME) -->

            <div class="mt-16 grid grid-cols-1 gap-4 sm:grid-cols-3 lg:gap-8" wire:poll.30s="fetchServerStatus">

                <div
                    class="rounded-2xl bg-dark-800/50 p-6 ring-1 ring-white/10 backdrop-blur-lg hover:bg-dark-800/80 transition group">
                    <dt class="text-sm font-semibold leading-6 text-gray-400">Status Server</dt>
                    <dd
                        class="order-first text-3xl font-heading font-semibold tracking-tight drop-shadow-md {{ $isOnline ? 'text-green-400' : 'text-red-400' }}">
                        @if($isOnline)
                            <span class="flex items-center justify-center gap-2">
                                <span class="relative flex h-3 w-3">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                </span>
                                ONLINE
                            </span>
                        @else
                            OFFLINE
                        @endif
                    </dd>
                </div>

                <div
                    class="rounded-2xl bg-dark-800/50 p-6 ring-1 ring-white/10 backdrop-blur-lg hover:bg-dark-800/80 transition">
                    <dt class="text-sm font-semibold leading-6 text-gray-400">Player Online</dt>
                    <dd class="order-first text-3xl font-heading font-semibold tracking-tight text-white">
                        {{ $isOnline ? number_format($playerCount) : '-' }}
                        <span class="text-base text-gray-500 font-sans font-normal">/
                            {{ number_format($maxPlayers) }}</span>
                    </dd>
                </div>

                <div
                    class="rounded-2xl bg-dark-800/50 p-6 ring-1 ring-white/10 backdrop-blur-lg hover:bg-dark-800/80 transition">
                    <dt class="text-sm font-semibold leading-6 text-gray-400">Versi Server</dt>
                    <dd class="order-first text-3xl font-heading font-semibold tracking-tight text-primary truncate">
                        {{ $isOnline ? $serverVersion : 'Maintenance' }}
                    </dd>
                </div>
            </div>
        </div>
    </div>

    @if(isset($promoCodes) && $promoCodes->count() > 0)
        <div class="py-12 relative">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">

                <div class="text-center mb-8 animate-fade-in-up">
                    <h2 class="text-2xl font-heading font-bold text-white tracking-wide uppercase flex items-center justify-center gap-2">
                        <svg class="w-6 h-6 text-yellow-400 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                        <span class="text-transparent bg-clip-text bg-linear-to-r from-yellow-400 to-orange-500">VOUCHER SPESIAL</span>
                    </h2>
                </div>

                <div class="flex flex-wrap justify-center gap-6">
                    @foreach($promoCodes as $promo)
                        <div x-data="{ copied: false }" class="relative group w-full max-w-sm cursor-pointer" @click="navigator.clipboard.writeText('{{ $promo->code }}'); copied = true; setTimeout(() => copied = false, 2000)">

                            <div class="absolute -inset-0.5 bg-linear-to-r from-yellow-400 to-orange-600 rounded-2xl blur opacity-20 group-hover:opacity-60 transition duration-500"></div>

                            <div class="relative flex items-center bg-dark-800 rounded-2xl p-4 ring-1 ring-white/10 group-hover:ring-yellow-500/50 transition-all">

                                <div class="shrink-0 bg-dark-900/50 p-3 rounded-xl border border-white/5 text-center min-w-20 flex flex-col justify-center items-center">
                                    <span class="block text-2xl font-heading font-bold text-yellow-400 leading-none">
                                        @if($promo->type === 'percent')
                                            {{ $promo->amount }}%
                                        @else
                                            <span class="text-sm">Rp </span>{{ number_format($promo->amount / 1000, 0) }}k
                                        @endif
                                    </span>
                                    <span class="text-[10px] text-gray-400 uppercase tracking-wider font-bold mt-1">DISKON</span>
                                </div>

                                <div class="w-1px h-12 border-l-2 border-dashed border-gray-600 mx-4 relative">
                                    <div class="absolute -top-6 -left-1.5 w-3 h-3 bg-dark-900 rounded-full"></div>
                                    <div class="absolute -bottom-6 -left-1.5 w-3 h-3 bg-dark-900 rounded-full"></div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-400 mb-1">Ketuk untuk salin:</p>
                                    <div class="flex items-center justify-between gap-2 bg-black/30 rounded-lg px-3 py-1.5 border border-white/5 group-hover:border-yellow-500/30 transition-colors">
                                        <code class="text-lg font-mono font-bold text-white tracking-widest truncate">{{ $promo->code }}</code>

                                        <div class="text-gray-400 group-hover:text-white transition">
                                            <svg x-show="!copied" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <svg x-show="copied" x-cloak class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div id="katalog" class="py-24 sm:py-32 relative scroll-mt-20">

        <div
            class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-secondary/10 blur-[120px] rounded-full pointer-events-none z-[-1]">
        </div>

        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-heading font-bold tracking-tight text-white sm:text-4xl">PILIH PAKET ANDA</h2>
                <p class="mt-2 text-lg leading-8 text-gray-400">Pilih kategori mode permainan favoritmu.</p>
            </div>

            <div class="mt-8 flex flex-wrap justify-center gap-4">

                @foreach($categories as $cat)
                    <button 
                        wire:click="setCategory('{{ $cat->slug }}')"
                        class="px-6 py-2 rounded-full font-heading text-sm transition-all duration-300 border border-transparent
                        {{ $activeCategory === $cat->slug 
                            ? 'bg-primary text-white shadow-lg shadow-primary/25 scale-105' 
                            : 'bg-dark-800 text-gray-400 border-white/5 hover:border-white/20 hover:text-white' 
                        }}">
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>

            <div class="mx-auto mt-16 flex flex-wrap justify-center gap-8">
                @forelse($ranks as $rank)
                    <div
                        class="flex flex-col justify-between rounded-3xl bg-dark-800/60 p-8 ring-1 ring-white/10 xl:p-10 backdrop-blur-md hover:ring-primary/50 transition-all duration-300 hover:scale-[1.02] group relative overflow-hidden w-full sm:w-[350px] lg:w-[380px] shrink-0">

                        @if($rank->slice_price)

                            <div
                                class="absolute top-0 left-1/2 -translate-x-1/2 rounded-b-xl bg-linear-to-r from-red-600 to-red-500 px-4 py-1.5 text-xs font-heading font-bold text-white shadow-lg z-20">
                                PROMO HOT
                            </div>
                        @endif

                        <div
                            class="absolute inset-0 bg-linear-to-br from-primary/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none">
                        </div>

                        <div>
                            <div class="flex items-center justify-between gap-x-4">
                                <h3 class="text-xl font-heading font-bold text-white tracking-wide uppercase">
                                    {{ $rank->name }}
                                </h3>

                                <div
                                    class="rounded-full bg-white/5 ring-1 ring-white/10 px-2.5 py-1 text-xs font-semibold leading-5 text-gray-300">
                                    {{ $rank->category->name }}
                                </div>
                            </div>

                            <div class="mt-6">

                                <div class="flex items-baseline gap-1">
                                    <span class="text-sm font-bold text-primary">Rp</span>
                                    <span class="text-3xl font-heading font-bold text-white leading-none tracking-tight">
                                        {{ number_format($rank->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if($rank->slice_price)
                                    <div
                                        class="mt-1 text-xs font-semibold text-gray-500 line-through decoration-red-500 decoration-2">
                                        Rp {{ number_format($rank->slice_price, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

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
                                    <li class="text-gray-500 italic">Belum ada deskripsi fitur.</li>
                                @endif
                            </ul>
                        </div>

                        <a href="{{ route('rank.detail', $rank->id) }}"
                            class="mt-8 block rounded-lg bg-dark-700 px-3 py-3 text-center text-sm font-heading font-semibold leading-6 text-white shadow-sm hover:bg-linear-to-r hover:from-primary hover:to-secondary transition-all duration-300 z-10 group-hover:shadow-lg group-hover:shadow-primary/20">
                            BELI SEKARANG
                        </a>
                    </div>
                @empty
                    <div class="w-full text-center py-12">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-dark-800 mb-4">
                            <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-heading text-white">Belum ada rank</h3>
                        <p class="text-gray-500">Kategori ini belum memiliki produk aktif.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="py-16 border-t border-white/5 bg-dark-900/50 relative">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <h2 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-8">Metode Pembayaran Tersedia</h2>

            <div class="flex flex-wrap justify-center items-center gap-8 opacity-70 hover:opacity-100 transition-opacity duration-500">
                @foreach($paymentMethods as $method)
                    @if($method->logo)
                        <div class="bg-white/5 p-3 rounded-xl border border-white/5 hover:border-white/20 transition-all hover:scale-105">
                            <img src="{{ asset('img/'.$method->logo) }}" alt="{{ $method->name }}" class="h-8 w-auto object-contain grayscale hover:grayscale-0 transition duration-300">
                        </div>
                    @else
                        <span class="text-gray-400 font-bold">{{ $method->name }}</span>
                    @endif
                @endforeach

                @if($paymentMethodTotalCount > 3)
                    <div class="text-xs text-gray-500 font-mono bg-white/5 px-3 py-2 rounded-lg">
                        +{{ $paymentMethodTotalCount - 3 }} Lainnya
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>