<x-layouts.app title="Lisensi Tidak Valid">
    <div class="max-w-3xl mx-auto px-4 py-16 flex flex-col items-center justify-center min-h-[70vh] relative z-10">
        
        <!-- Background Glow for Danger -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-64 h-64 rounded-full blur-[100px] opacity-20 pointer-events-none bg-red-500"></div>

        <!-- Danger Icon -->
        <div class="mb-8 p-6 rounded-full bg-red-500/10 border border-red-500/20 shadow-[0_0_30px_rgba(239,68,68,0.2)]">
            <svg class="w-20 h-20 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </div>
        
        <!-- Divider -->
        <div class="w-24 h-1.5 rounded-full mb-8 shadow-[0_0_15px_rgba(239,68,68,0.3)] bg-linear-to-r from-red-600 to-red-400"></div>
        
        <!-- Message -->
        <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 text-center tracking-tight">
            Lisensi Tidak Valid
        </h2>
        
        <p class="text-gray-300 text-center max-w-lg mb-4 text-lg md:text-xl leading-relaxed">
            Aplikasi ini belum diaktivasi atau lisensi tidak dapat diverifikasi. Hubungi penjual untuk aktivasi lisensi atau isi data lisensi Mayar di panel admin.
        </p>

        <div class="mt-4 px-6 py-4 rounded-xl bg-white/5 border border-white/10 text-red-400 font-mono text-sm w-full max-w-lg text-center backdrop-blur-md">
            {{ $reason ?? 'Tidak ada detail kesalahan.' }}
        </div>

        <!-- Action Buttons -->
        <div class="mt-10 flex flex-col sm:flex-row gap-4 items-center justify-center w-full sm:w-auto">
            <a href="{{ url('/admin') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold tracking-wide text-white transition-all transform hover:-translate-y-1 hover:shadow-lg bg-red-600 hover:bg-red-500 border border-red-500/50">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Masuk ke Panel Admin
            </a>
            
            <button onclick="window.history.back()" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl font-semibold tracking-wide text-white/90 bg-white/5 border border-white/10 hover:bg-white/10 transition-all backdrop-blur-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </button>
        </div>
        
    </div>
</x-layouts.app>
