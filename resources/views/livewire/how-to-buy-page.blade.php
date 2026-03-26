<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden">

    {{-- Background Glow Effect --}}
    <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[600px] bg-primary/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mx-auto max-w-2xl text-center mb-20">
            <h2 class="text-base font-semibold leading-7 text-primary font-heading tracking-wide uppercase">Panduan Transaksi</h2>
            <h1 class="mt-2 text-4xl font-heading font-bold tracking-tight text-white sm:text-6xl text-glow">
                CARA <span class="text-transparent bg-clip-text bg-linear-to-r from-primary to-secondary">PEMBELIAN</span>
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-400">
                Ikuti 4 langkah mudah berikut untuk mendapatkan Rank impianmu di {{ $settings?->site_name ?? 'Minecraft Server' }}.
            </p>
        </div>

        {{-- TIMELINE STEPS --}}
        <div class="mx-auto max-w-3xl">
            <div class="relative">
                
                {{-- Garis Tengah Vertikal (Mobile: Kiri, Desktop: Tengah) --}}
                <div class="absolute left-8 top-0 bottom-0 w-1 bg-dark-700 md:left-1/2 md:-ml-0.5"></div>

                <div class="space-y-12 relative">
                    
                    {{-- STEP 1 --}}
                    <div class="relative flex flex-row md:items-center gap-6 md:gap-0 group">
                        
                        {{-- Icon/Number --}}
                        <div class="relative shrink-0 md:absolute md:left-1/2 md:-translate-x-1/2 flex h-16 w-16 items-center justify-center rounded-full border-4 border-primary bg-dark-800 shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="font-heading text-xl font-bold text-white">01</span>
                        </div>
                        
                       {{-- Content --}}
                        <div class="flex-1 md:w-1/2 md:pr-12 md:text-right md:flex-none">
                            <div class="p-6 rounded-2xl bg-dark-800/50 ring-1 ring-white/10 hover:ring-primary/50 transition-all backdrop-blur-md">
                                <h3 class="text-xl font-heading font-bold text-white mb-2">Pilih Rank & Mode</h3>
                                <p class="text-gray-400 text-sm">
                                    Buka halaman <a href="" class="text-primary hover:underline">Shop</a>, pilih kategori mode (Skyblock/Economy), lalu pilih Rank yang Anda inginkan.
                                </p>
                            </div>
                        </div>
                        <div class="hidden md:block md:w-1/2"></div>
                    </div>

                    {{-- STEP 2 --}}
                    <div class="relative flex flex-row md:items-center gap-6 md:gap-0 group">
                        <div class="relative shrink-0 md:absolute md:left-1/2 md:-translate-x-1/2 flex h-16 w-16 items-center justify-center rounded-full border-4 border-primary bg-dark-800 shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="font-heading text-xl font-bold text-white">02</span>
                        </div>
                        
                        <div class="hidden md:block md:w-1/2"></div>
                        
                        <div class="flex-1 md:w-1/2 md:pl-12 md:flex-none">
                            <div class="p-6 rounded-2xl bg-dark-800/50 ring-1 ring-white/10 hover:ring-primary/50 transition-all backdrop-blur-md">
                                <h3 class="text-xl font-heading font-bold text-white mb-2">Isi Formulir Order</h3>
                                <p class="text-gray-400 text-sm">
                                    Masukkan <strong>Gamertag</strong> (Nama akun Minecraft) dengan benar. Isi juga nomor WhatsApp untuk notifikasi otomatis.
                                </p>
                                <div class="mt-3 text-xs bg-red-500/10 text-red-400 px-3 py-1 rounded inline-block border border-red-500/20">
                                    ⚠️ Awas Typo! Gamertag bersifat Case Sensitive.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3 --}}
                    <div class="relative flex flex-row md:items-center gap-6 md:gap-0 group">
                        <div class="relative shrink-0 md:absolute md:left-1/2 md:-translate-x-1/2 flex h-16 w-16 items-center justify-center rounded-full border-4 border-primary bg-dark-800 shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                            <span class="font-heading text-xl font-bold text-white">03</span>
                        </div>
                        
                        <div class="flex-1 md:w-1/2 md:pr-12 md:text-right md:flex-none">
                            <div class="p-6 rounded-2xl bg-dark-800/50 ring-1 ring-white/10 hover:ring-secondary/50 transition-all backdrop-blur-md">
                                <h3 class="text-xl font-heading font-bold text-white mb-2">Pembayaran</h3>
                                <p class="text-gray-400 text-sm">
                                    Lakukan transfer sesuai nominal ke metode pembayaran yang tersedia (QRIS/Bank). 
                                    <strong class="text-white">Simpan bukti transfer (Screenshot/Struk)</strong> karena wajib diupload.
                                </p>
                            </div>
                        </div>
                        <div class="hidden md:block md:w-1/2"></div>
                    </div>

                    {{-- STEP 4 --}}
                    <div class="relative flex flex-row md:items-center gap-6 md:gap-0 group">
                        <div class="relative shrink-0 md:absolute md:left-1/2 md:-translate-x-1/2 flex h-16 w-16 items-center justify-center rounded-full border-4 border-primary bg-linear-to-br from-primary to-secondary shadow-xl z-10 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        
                        <div class="hidden md:block md:w-1/2"></div>
                        
                        <div class="flex-1 md:w-1/2 md:pl-12 md:flex-none">
                            <div class="p-6 rounded-2xl bg-dark-800/50 ring-1 ring-white/10 hover:ring-green-500/50 transition-all backdrop-blur-md">
                                <h3 class="text-xl font-heading font-bold text-white mb-2">Selesai!</h3>
                                <p class="text-gray-400 text-sm">
                                    Sistem akan otomatis mengarahkan Anda ke halaman tracking. Upload bukti transfer di sana. 
                                    Rank akan aktif dalam <strong>5-15 menit</strong> setelah admin memverifikasi.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- FAQ SECTION --}}
        <div class="mx-auto max-w-3xl mt-24">
            <h2 class="text-2xl font-heading text-white text-center mb-8">Pertanyaan Umum (FAQ)</h2>
            
            <div class="grid grid-cols-1 gap-4" x-data="{ active: null }">
                {{-- FAQ Item 1 --}}
                <div class="bg-dark-800/40 rounded-xl overflow-hidden border border-white/5">
                    <button @click="active === 1 ? active = null : active = 1" class="flex items-center justify-between w-full p-4 text-left">
                        <span class="font-semibold text-gray-200">Berapa lama proses masuknya rank?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="active === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 1" x-collapse class="p-4 pt-0 text-gray-400 text-sm">
                        Estimasi normal adalah 5 - 15 menit jika Admin sedang online. Paling lambat 1x24 jam. Anda akan mendapat notifikasi WhatsApp saat rank aktif.
                    </div>
                </div>

                {{-- FAQ Item 2 --}}
                <div class="bg-dark-800/40 rounded-xl overflow-hidden border border-white/5">
                    <button @click="active === 2 ? active = null : active = 2" class="flex items-center justify-between w-full p-4 text-left">
                        <span class="font-semibold text-gray-200">Apakah rank bersifat permanen?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="active === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 2" x-collapse class="p-4 pt-0 text-gray-400 text-sm">
                        Ya, Rank bersifat <strong>Lifetime</strong> (Seumur hidup) selama server {{ $settings?->site_name }} beroperasi.
                    </div>
                </div>

                {{-- FAQ Item 3 --}}
                <div class="bg-dark-800/40 rounded-xl overflow-hidden border border-white/5">
                    <button @click="active === 3 ? active = null : active = 3" class="flex items-center justify-between w-full p-4 text-left">
                        <span class="font-semibold text-gray-200">Saya salah input Gamertag, bagaimana?</span>
                        <svg class="w-5 h-5 text-gray-400 transform transition-transform" :class="active === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="active === 3" x-collapse class="p-4 pt-0 text-gray-400 text-sm">
                        Jika status pesanan masih <strong>Pending</strong>, segera hubungi Admin melalui tombol WhatsApp di halaman tracking. Jika sudah diproses, mohon maaf tidak bisa diubah (Sesuai TOS).
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA Bottom --}}
        <div class="text-center mt-16">
            <a href="{{ route('shop') }}" class="inline-flex items-center justify-center rounded-full bg-linear-to-r from-primary to-secondary px-8 py-3 text-base font-heading font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/50 transition-all hover:-translate-y-1">
                Mengerti, Beli Sekarang
            </a>
        </div>

    </div>
</div>