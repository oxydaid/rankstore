<div x-data="{ showModal: false }">
    @if($getState())
        {{-- THUMBNAIL (Preview Kecil di Infolist) --}}
        <div 
            class="relative group cursor-pointer overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 shadow-sm bg-gray-100 dark:bg-gray-800"
            @click="showModal = true"
        >
            <img 
                src="{{ \Illuminate\Support\Facades\Storage::url($getState()) }}" 
                alt="Bukti Transaksi" 
                class="h-48 w-full object-cover transition duration-500 transform group-hover:scale-105"
                loading="lazy"
            >
            
            {{-- Overlay Icon Mata saat Hover --}}
            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition-all duration-300 flex items-center justify-center opacity-0 group-hover:opacity-100">
                <div class="bg-white/20 backdrop-blur-sm p-2 rounded-full">
                    <svg class="w-6 h-6 text-white drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m4-3H6" /></svg>
                </div>
            </div>
        </div>

        {{-- MODAL FULLSCREEN --}}
        <template x-teleport="body">
            <div
                x-show="showModal"
                x-cloak
                {{-- PERBAIKAN: Gunakan style inline untuk memastikan background gelap --}}
                style="background-color: rgba(0, 0, 0, 0.9); z-index: 99999; position: fixed; inset: 0;"
                class="flex items-center justify-center p-4 backdrop-blur-sm"
                @keydown.escape.window="showModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                {{-- Klik area gelap untuk tutup --}}
                <div class="absolute inset-0 cursor-pointer" @click="showModal = false"></div>

                {{-- Container Gambar --}}
                <div class="relative z-10 max-w-6xl w-full max-h-full flex flex-col items-center justify-center" @click.stop>
                    
                    {{-- Tombol Close --}}
                    <button 
                        @click="showModal = false"
                        class="absolute -top-12 right-0 md:right-0 md:-top-12 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-full p-2 transition focus:outline-none"
                    >
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>

                    <img 
                        src="{{ \Illuminate\Support\Facades\Storage::url($getState()) }}" 
                        class="max-w-full max-h-[85vh] rounded-lg shadow-2xl object-contain ring-1 ring-white/20"
                    >
                </div>
            </div>
        </template>
    @else
        {{-- Placeholder jika file tidak ada --}}
        <div class="h-48 w-full bg-gray-50 dark:bg-gray-800/50 rounded-xl flex flex-col items-center justify-center text-gray-400 text-sm border border-dashed border-gray-300 dark:border-gray-700">
            <svg class="w-8 h-8 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
            <span class="font-medium">Tidak ada gambar</span>
        </div>
    @endif
</div>