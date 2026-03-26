<div class="relative isolate min-h-screen flex items-center justify-center px-6 py-24 lg:px-8 overflow-hidden">
    
    {{-- Background Glow Effect --}}
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-primary/20 blur-[150px] rounded-full pointer-events-none z-[-1]"></div>
    <div class="absolute bottom-0 right-0 translate-y-1/4 w-[400px] h-[400px] bg-secondary/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="w-full max-w-xl">
        
        {{-- Header Text --}}
        <div class="text-center mb-10">
            <h1 class="text-4xl font-heading font-bold text-white sm:text-5xl text-glow mb-4">
                CEK <span class="text-transparent bg-clip-text bg-linear-to-r from-primary to-secondary">PESANAN</span>
            </h1>
            <p class="text-gray-400 text-lg">
                Masukkan Order ID / UUID yang Anda dapatkan saat checkout untuk melihat status transaksi.
            </p>
        </div>

        {{-- Card Form --}}
        <div class="rounded-3xl bg-dark-800/60 p-8 sm:p-10 ring-1 ring-white/10 backdrop-blur-md shadow-2xl relative overflow-hidden group hover:ring-primary/30 transition-all duration-500">
            
            {{-- Decorative Line Top --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-primary to-secondary opacity-50 group-hover:opacity-100 transition-opacity"></div>

            <form wire:submit="search" class="space-y-6">
                <div>
                    <label for="orderId" class="block text-sm font-medium leading-6 text-white mb-2">Order ID (UUID)</label>
                    <div class="relative">
                        <input 
                            wire:model="orderId"
                            type="text" 
                            id="orderId"
                            class="block w-full rounded-xl border-0 bg-dark-900/80 py-4 pl-4 pr-12 text-white shadow-sm ring-1 ring-inset ring-white/10 placeholder:text-gray-500 focus:ring-2 focus:ring-inset focus:ring-primary sm:text-sm sm:leading-6 transition-all"
                            placeholder="Contoh: 550e8400-e29b..."
                        >
                        {{-- Icon Search Absolute --}}
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-gray-500">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    @error('orderId') 
                        <p class="mt-2 text-sm text-red-400 flex items-center gap-1">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p> 
                    @enderror
                </div>

                <button 
                    type="submit" 
                    class="w-full rounded-xl bg-linear-to-r from-primary to-secondary px-3.5 py-4 text-sm font-heading font-bold text-white shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-[1.02] transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled"
                >
                    <span wire:loading.remove>LACAK PESANAN</span>
                    
                    {{-- PERBAIKAN: Tambahkan .flex agar layout tetap terjaga saat loading --}}
                    <span wire:loading.flex class="items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        MENCARI...
                    </span>
                </button>
            </form>

            {{-- Helper Text --}}
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    Lupa Order ID? Cek riwayat chat WhatsApp Anda atau hubungi Admin.
                </p>
            </div>
        </div>
    </div>
</div>