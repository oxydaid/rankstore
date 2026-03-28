<x-layouts.app :title="$__env->yieldContent('title')">
    <div class="max-w-4xl mx-auto px-4 py-16 flex flex-col items-center justify-center min-h-[70vh] relative z-10">
        
        <!-- decorative background glow behind text -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full blur-[100px] opacity-20 pointer-events-none" style="background: linear-gradient(135deg, var(--primary), var(--secondary));"></div>

        <!-- Error Code with Gradient -->
        <h1 class="text-[8rem] md:text-[12rem] font-bold leading-none tracking-tighter relative" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            @yield('code')
        </h1>
        
        <!-- Divider -->
        <div class="w-24 h-1.5 rounded-full mb-8 shadow-[0_0_15px_rgba(0,0,0,0.3)]" style="background: linear-gradient(90deg, var(--primary), var(--secondary));"></div>
        
        <!-- Message -->
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 text-center tracking-tight">
            @yield('message')
        </h2>
        
        <!-- Sub-message based on code (Optional but nice) -->
        <p class="text-gray-400 text-center max-w-lg mb-10 text-lg md:text-xl leading-relaxed">
            @if(trim($__env->yieldContent('code')) == '404')
                Halaman yang Anda cari mungkin telah dihapus, diubah namanya, atau untuk sementara tidak tersedia.
            @elseif(trim($__env->yieldContent('code')) == '403')
                Anda tidak memiliki izin untuk mengakses halaman ini. Silakan periksa akses Anda atau hubungi dukungan jika ini adalah sebuah kesalahan.
            @elseif(trim($__env->yieldContent('code')) == '500')
                Server kami saat ini sedang mengalami masalah. Tim kami telah diberitahu dan sedang berusaha untuk menyelesaikan masalah ini.
            @elseif(trim($__env->yieldContent('code')) == '503')
                Kami saat ini sedang menjalani pemeliharaan terjadwal untuk meningkatkan layanan kami. Silakan periksa kembali beberapa saat lagi.
            @else
                Ada yang tidak beres. Terjadi kesalahan yang tidak terduga dan kami tidak dapat menyelesaikan permintaan Anda.
            @endif
        </p>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-center w-full sm:w-auto">
            <a href="{{ url('/') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold tracking-wide text-white transition-all transform hover:-translate-y-1 hover:shadow-lg hover:shadow-primary/30" style="background: linear-gradient(135deg, var(--primary), var(--secondary)); border: 1px solid rgba(255,255,255,0.1);">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Kembali ke Beranda
            </a>
            
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold tracking-wide text-white/90 bg-white/5 border border-white/10 hover:bg-white/10 transition-all backdrop-blur-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </button>
        </div>
        
    </div>
</x-layouts.app>
