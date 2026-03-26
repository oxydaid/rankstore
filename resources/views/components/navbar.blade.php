@props(['logo', 'siteName'])

{{-- Header z-40 (Lebih rendah dari menu mobile nanti) --}}
<header x-data="{ mobileMenuOpen: false }" class="sticky top-0 z-40 w-full border-b border-white/5 bg-dark-900/80 backdrop-blur-md">
    <nav class="mx-auto flex max-w-7xl items-center justify-between p-4 lg:px-8" aria-label="Global">
        
        {{-- Logo --}}
        <div class="flex lg:flex-1">
            <a href="{{ route('home') }}" class="-m-1.5 p-1.5 flex items-center gap-3 group">
                @if($logo)
                    <img class="h-7 md:h-8 w-auto transition-transform group-hover:scale-110 duration-300" src="{{ $logo }}" alt="{{ $siteName }}">
                @else
                    <span class="text-2xl font-heading font-bold text-white tracking-wider uppercase">
                        {{ $siteName }}
                    </span>
                @endif
            </a>
        </div>

        {{-- Desktop Menu --}}
        <div class="hidden lg:flex lg:gap-x-8">
            <a href="{{ route('home') }}" 
               class="text-sm font-semibold leading-6 py-1 {{ request()->routeIs('home') ? 'text-primary font-bold border-b-2 border-primary' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-white/20 transition-all duration-300' }}">
                Home
            </a>

            <a href="{{ route('shop') }}" 
               class="text-sm font-semibold leading-6 py-1 {{ request()->routeIs('shop*') ? 'text-primary font-bold border-b-2 border-primary' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-white/20 transition-all duration-300' }}">
                Shop
            </a>
            
            <a href="{{ route('migration') }}" 
               class="text-sm font-semibold leading-6 py-1 {{ request()->routeIs('migration*') ? 'text-primary font-bold border-b-2 border-primary' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-white/20 transition-all duration-300' }}">
                Migrasi
            </a>

            <a href="{{ route('terms') }}" 
               class="text-sm font-semibold leading-6 py-1 {{ request()->routeIs('terms*') ? 'text-primary font-bold border-b-2 border-primary' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-white/20 transition-all duration-300' }}">
                TOS
            </a>
            
            <a href="{{ route('how-to-buy') }}" 
               class="text-sm font-semibold leading-6 py-1 {{ request()->routeIs('how-to-buy*') ? 'text-primary font-bold border-b-2 border-primary' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-white/20 transition-all duration-300' }}">
                Cara Pembelian
            </a>

            <a href="{{ route('tracking') }}" 
               class="text-sm font-semibold leading-6 py-1 {{ request()->routeIs('tracking*') ? 'text-primary font-bold border-b-2 border-primary' : 'text-gray-300 hover:text-white hover:border-b-2 hover:border-white/20 transition-all duration-300' }}">
                Cek Pesanan
            </a>
        </div>

        {{-- Right Side --}}
        <div class="flex flex-1 justify-end items-center gap-4">
            <a href="{{ route('shop') }}" class="hidden lg:block rounded-full bg-linear-to-r from-primary to-secondary px-5 py-2 text-sm font-heading text-white shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-105 transition-all duration-300">
                Beli Rank
            </a>
            
            <div class="flex lg:hidden">
                <button type="button" @click="mobileMenuOpen = true" class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-400 hover:text-white">
                    <span class="sr-only">Open main menu</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </nav>

    {{-- Mobile Menu (DIPINDAHKAN KE BODY AGAR TIDAK TERPOTONG HEADER) --}}
    <template x-teleport="body">
        <div x-show="mobileMenuOpen" x-cloak class="lg:hidden relative z-99" role="dialog" aria-modal="true">
            
            {{-- Backdrop Gelap --}}
              <div x-show="mobileMenuOpen" x-cloak
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/80 backdrop-blur-sm" 
                 @click="mobileMenuOpen = false">
            </div>
            
            {{-- Panel Menu --}}
              <div class="fixed inset-y-0 right-0 z-100 w-full overflow-y-auto bg-dark-900 px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-white/10 shadow-2xl"
                 x-show="mobileMenuOpen"
                  x-cloak
                 x-transition:enter="transform transition ease-in-out duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transform transition ease-in-out duration-300"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full">
                
                <div class="flex items-center justify-between">
                    <span class="text-xl font-heading font-bold text-white uppercase tracking-wider">{{ $siteName }}</span>
                    
                    <button type="button" @click="mobileMenuOpen = false" class="-m-2.5 rounded-md p-2.5 text-gray-400 hover:text-white">
                        <span class="sr-only">Close menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <a href="{{ route('home') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-white/5">Home</a>
                            <a href="{{ route('shop') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-white/5">Shop</a>
                            <a href="{{ route('migration') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-white/5">Migrasi</a>
                            <a href="{{ route('terms') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-white/5">TOS</a>
                            <a href="{{ route('how-to-buy') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-white/5">Cara Pembelian</a>
                            <a href="{{ route('tracking') }}" class="-mx-3 block rounded-lg px-3 py-2 text-base font-semibold leading-7 text-white hover:bg-white/5">Cek Pesanan</a>
                        </div>
                        <div class="py-6">
                            <a href="{{ route('shop') }}" class="-mx-3 block rounded-lg px-3 py-2.5 text-base font-heading font-bold leading-7 text-primary hover:bg-white/5">
                                BELI RANK &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</header>