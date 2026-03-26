<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden"> {{-- PERBAIKAN: Tambahkan overflow-hidden --}}
    
    {{-- Background Glow Effect --}}
    <div class="absolute top-20 right-0 -translate-x-1/2 w-[400px] h-[400px] bg-primary/20 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>
    {{-- Elemen di bawah ini yang menyebabkan space kosong jika tidak di-hidden --}}
    <div class="absolute bottom-0 left-0 translate-y-1/2 w-[300px] h-[300px] bg-secondary/20 blur-[100px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-5xl px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="mx-auto max-w-3xl text-center mb-16">
            <h2 class="text-base font-semibold leading-7 text-primary font-heading tracking-wide uppercase">Legal & Rules</h2>
            <h1 class="mt-2 text-4xl font-heading font-bold tracking-tight text-white sm:text-6xl text-glow">
                SYARAT & <span class="text-transparent bg-clip-text bg-linear-to-r from-primary to-secondary">KETENTUAN</span>
            </h1>
            <p class="mt-6 text-lg leading-8 text-gray-400">
                Aturan penggunaan dan hak serta kewajiban pemain terhadap peringkat (Rank) di Minecraft Server <b>{{ $settings?->site_name ?? 'Minecraft Server' }}</b>.
            </p>
        </div>

        {{-- Content Card --}}
        <div class="rounded-3xl bg-dark-800/60 ring-1 ring-white/10 backdrop-blur-md shadow-2xl relative overflow-hidden">
            
            {{-- Decorative Line --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-primary to-secondary"></div>

            <div class="p-8 sm:p-12">
                
                {{-- 01. DEFINISI --}}
                <div class="mb-12 border-b border-white/5 pb-10">
                    <h3 class="text-2xl font-heading text-white mb-4 flex items-center gap-3">
                        <span class="text-primary">#</span> Apa itu TOS / S&K?
                    </h3>
                    <p class="text-gray-300 leading-relaxed">
                        TOS (Terms of Service) atau S&K (Syarat & Ketentuan) Rank adalah aturan mutlak yang mengatur penggunaan, hak, serta kewajiban pemain terhadap rank di dalam game Minecraft Server <strong>{{ $settings->site_name }}</strong>.
                    </p>
                </div>

                {{-- 02. ATURAN UTAMA (Grid Layout) --}}
                <div class="mb-12">
                    <h3 class="text-2xl font-heading text-white mb-6">Aturan & Kewajiban Utama</h3>
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <li class="bg-dark-900/50 p-4 rounded-xl border border-white/5">
                            <strong class="text-white block mb-1">🚫 No Refund Policy</strong>
                            <span class="text-gray-400 text-sm">Anda tidak diperbolehkan meminta refund rank kembali dalam bentuk uang. Rank yang sudah dibeli tidak dapat dikembalikan dengan alasan apapun.</span>
                        </li>

                        <li class="bg-dark-900/50 p-4 rounded-xl border border-white/5">
                            <strong class="text-white block mb-1">🚫 Dilarang Jual Beli (RTM)</strong>
                            <span class="text-gray-400 text-sm">Dilarang menjual rank ke player lain yang merugikan server (RTM). Jangan membeli jika uang pas-pasan lalu menjual akun kembali.</span>
                        </li>

                        <li class="bg-dark-900/50 p-4 rounded-xl border border-white/5">
                            <strong class="text-white block mb-1">⚠️ Status Akun</strong>
                            <span class="text-gray-400 text-sm">Pastikan akun tidak sedang di-banned saat pembelian/migrasi. Jika terkena banned setelah beli, rank tidak dapat dikembalikan/dipindah.</span>
                        </li>

                        <li class="bg-dark-900/50 p-4 rounded-xl border border-white/5">
                            <strong class="text-white block mb-1">✅ Tanggung Jawab Data</strong>
                            <span class="text-gray-400 text-sm">Kami tidak bertanggung jawab jika Anda salah menginput Gamertag/username. Teliti sebelum checkout.</span>
                        </li>

                        <li class="bg-dark-900/50 p-4 rounded-xl border border-white/5">
                            <strong class="text-white block mb-1">⏳ Proses Rank</strong>
                            <span class="text-gray-400 text-sm">Rank diproses paling lambat 1x24 jam setelah bukti pembayaran valid diterima, sesuai antrian.</span>
                        </li>

                        <li class="bg-dark-900/50 p-4 rounded-xl border border-white/5">
                            <strong class="text-white block mb-1">🔒 Lifetime</strong>
                            <span class="text-gray-400 text-sm">Rank bersifat permanen selama server {{ $settings->site_name }} beroperasi dan tidak ada perubahan S&K mayor.</span>
                        </li>
                    </ul>
                </div>

                {{-- 03. MIGRASI & PERPINDAHAN --}}
                <div class="mb-12">
                    <h3 class="text-2xl font-heading text-white mb-6">Ketentuan Migrasi & Pindah Akun</h3>
                    <div class="space-y-4 text-gray-300">
                        <div class="flex gap-4">
                            <div class="shrink-0 w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                            <p>Pindah rank ke gamertag/akun baru akan dikenakan <strong>tax (biaya) 20%</strong> dari harga rank yang dibeli.</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="shrink-0 w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                            <p>Migrasi rank <strong>tanpa bukti kepemilikan</strong> (Ticket ID/Bukti Beli) dianggap tidak valid dan dibatalkan.</p>
                        </div>
                        <div class="flex gap-4">
                            <div class="shrink-0 w-1.5 h-1.5 rounded-full bg-primary mt-2"></div>
                            <p>Rank <strong>tidak bisa dipindah antar mode server</strong> (Contoh: Dari Economy dipindah ke Skyblock tidak bisa).</p>
                        </div>
                    </div>
                </div>

                {{-- 04. INFORMASI PENTING (Alert Box) --}}
                <div class="bg-linear-to-r from-red-900/20 to-dark-900 border-l-4 border-red-500 p-6 rounded-r-xl mb-10">
                    <h4 class="text-red-400 font-heading text-lg mb-2 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        PENTING: Migrasi & Keamanan
                    </h4>
                    <div class="space-y-3 text-sm text-gray-300">
                        <p>
                            Kalian boleh memindahkan/menjual rank ke akun orang lain <strong>HANYA JIKA</strong> dilakukan lewat proses migrasi rank resmi (membayar fee 20%). Hal ini tidak dihitung RMT karena berkontribusi ke server.
                        </p>
                        <p>
                            Namun, jika terjadi <strong>Hackback</strong>, akun dibobol, atau sengketa kepemilikan di kemudian hari, kami <strong>TIDAK BERTANGGUNG JAWAB</strong>.
                        </p>
                        <p class="font-bold text-white">
                            Rank yang sudah dipindah menjadi hak penuh pemilik akun baru. Pemilik lama tidak bisa meminta rank kembali dengan alasan apapun (misal: rank sudah diupgrade oleh pemilik baru).
                        </p>
                    </div>
                </div>

                {{-- 05. SANKSI --}}
                <div class="bg-dark-900/50 p-6 rounded-xl text-center border border-white/5">
                    <p class="text-gray-400 italic">
                        "Apabila ketahuan melanggar poin-poin di atas, akan diberi sanksi tegas atau hukuman sesuai peraturan {{ $settings->site_name }}."
                    </p>
                    <p class="text-sm text-gray-500 mt-2">
                        Peraturan, Harga, dan Fitur dapat berubah sewaktu-waktu tanpa pemberitahuan. <br>
                        <strong>No Komplain. Membeli berarti setuju.</strong>
                    </p>
                </div>

            </div>

            {{-- Footer Action --}}
            <div class="bg-dark-900/80 p-6 border-t border-white/10 flex flex-col sm:flex-row justify-between items-center gap-4">
                <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-400 hover:text-white transition">
                    &larr; Kembali ke Home
                </a>
                <a href="{{ route('shop') }}" class="rounded-lg bg-linear-to-r from-primary to-secondary px-6 py-2 text-sm font-heading font-bold text-white shadow-lg shadow-primary/20 hover:shadow-primary/40 transition-all hover:scale-105">
                    Saya Setuju & Lanjut Belanja
                </a>
            </div>

        </div>
    </div>
</div>