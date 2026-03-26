<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden" wire:poll.10s>
    
    {{-- Background Glow (Warna Ungu untuk Migrasi) --}}
    <div class="absolute top-0 right-0 -translate-y-1/2 w-[500px] h-[500px] bg-purple-500/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-4xl px-6 lg:px-8">

        {{-- ALERT SUKSES --}}
        @if (session()->has('success_migration'))
            <div class="mb-10 bg-purple-500/10 border border-purple-500/20 p-6 rounded-2xl flex items-start gap-4 animate-fade-in-up">
                <div class="bg-purple-500/20 p-2 rounded-full">
                    <svg class="w-6 h-6 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-heading text-white">Permintaan Migrasi Diterima!</h3>
                    <p class="text-gray-400 text-sm mt-1">
                        Data migrasi Anda telah masuk sistem. Mohon tunggu admin memverifikasi dan memproses perpindahan rank.
                    </p>
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="text-center mb-12">
            <p class="text-sm font-mono text-purple-400 mb-2 tracking-widest">REF ID: {{ $migration->uuid }}</p>
            <h1 class="text-4xl font-heading font-bold text-white text-glow">STATUS MIGRASI</h1>
        </div>

        {{-- STATUS TRACKER --}}
        <div class="bg-dark-800/60 rounded-3xl ring-1 ring-white/10 backdrop-blur-md shadow-2xl overflow-hidden p-8 sm:p-12">
            
            {{-- Progress Bar --}}
            @php
                $progress = match($migration->status) {
                    'pending' => '33%',
                    'processing' => '66%',
                    'completed', 'cancelled' => '100%',
                    default => '0%'
                };
                
                $barColor = match($migration->status) {
                    'cancelled' => 'bg-red-500',
                    'completed' => 'bg-green-500',
                    default => 'bg-purple-500'
                };
            @endphp

            <div class="relative flex justify-between mb-16">
                <div class="absolute top-1/2 left-0 w-full h-1 bg-dark-700 -translate-y-1/2 z-0"></div>
                <div class="absolute top-1/2 left-0 h-1 {{ $barColor }} -translate-y-1/2 z-0 transition-all duration-1000" style="width: {{ $progress }}"></div>

                {{-- Step 1: Request --}}
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-purple-500 flex items-center justify-center text-white text-xs font-bold shadow-lg">1</div>
                    <span class="text-xs font-bold text-white uppercase absolute -bottom-8 w-32 text-center">Request</span>
                </div>

                {{-- Step 2: Proses --}}
                <div class="relative z-10 flex flex-col items-center gap-2">
                    @php
                        $step2Active = in_array($migration->status, ['processing', 'completed', 'cancelled']);
                        $step2Color = $migration->status === 'cancelled' ? 'bg-red-500' : 'bg-purple-500';
                    @endphp
                    <div class="w-8 h-8 rounded-full {{ $step2Active ? $step2Color : 'bg-dark-700 border-2 border-dark-600' }} flex items-center justify-center text-white text-xs font-bold transition-colors">2</div>
                    <span class="text-xs font-bold {{ $step2Active ? 'text-white' : 'text-gray-500' }} uppercase absolute -bottom-8 w-32 text-center">Proses Pindah</span>
                </div>

                {{-- Step 3: Selesai --}}
                <div class="relative z-10 flex flex-col items-center gap-2">
                    @php
                        $isCompleted = $migration->status === 'completed';
                        $isCancelled = $migration->status === 'cancelled';
                    @endphp
                    <div class="w-8 h-8 rounded-full {{ $isCompleted ? 'bg-green-500' : ($isCancelled ? 'bg-red-500' : 'bg-dark-700 border-2 border-dark-600') }} flex items-center justify-center text-white text-xs font-bold transition-colors">
                        @if($isCancelled) <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        @elseif($isCompleted) <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        @else 3 @endif
                    </div>
                    <span class="text-xs font-bold {{ $isCompleted ? 'text-green-400' : ($isCancelled ? 'text-red-500' : 'text-gray-500') }} uppercase absolute -bottom-8 w-32 text-center">
                        {{ $isCancelled ? 'Dibatalkan' : 'Selesai' }}
                    </span>
                </div>
            </div>

            {{-- DETAIL INFO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mt-8">
                
                {{-- Info Status --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Status Saat Ini</h3>
                    
                    @if($migration->status === 'pending')
                        <div class="bg-yellow-500/10 border border-yellow-500/20 p-4 rounded-xl">
                            <p class="text-yellow-400 font-bold flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Menunggu Verifikasi
                            </p>
                            <p class="text-sm text-gray-400 mt-2">Admin akan segera mengecek request migrasi Anda.</p>
                        </div>
                    
                    @elseif($migration->status === 'processing')
                        <div class="bg-blue-500/10 border border-blue-500/20 p-4 rounded-xl">
                            <p class="text-blue-400 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Sedang Diproses
                            </p>
                            <p class="text-sm text-gray-400 mt-2">Admin sedang memindahkan rank ke akun baru. Mohon jangan login dulu.</p>
                        </div>
                    
                    @elseif($migration->status === 'completed')
                        <div class="bg-green-500/10 border border-green-500/20 p-4 rounded-xl">
                            <p class="text-green-400 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Migrasi Berhasil
                            </p>
                            <p class="text-sm text-gray-400 mt-2">Rank telah sukses dipindahkan ke akun tujuan!</p>

                            {{-- TOMBOL DOWNLOAD INVOICE (BARU) --}}
                            @if($migration->server_invoice)
                                <a href="{{ \Illuminate\Support\Facades\Storage::url($migration->server_invoice) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-white bg-green-600 px-3 py-2 rounded-lg hover:bg-green-500 transition w-full justify-center">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download Bukti / Invoice
                                </a>
                            @endif
                        </div>

                    @elseif($migration->status === 'cancelled')
                        <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl">
                            <p class="text-red-400 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Dibatalkan
                            </p>
                            @if($migration->notes)
                                <p class="text-sm text-red-300 mt-2"><strong>Alasan:</strong> {{ $migration->notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Detail Transfer --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Rincian Perpindahan</h3>
                    <div class="bg-dark-900/50 p-4 rounded-xl border border-white/5 space-y-4">
                        
                        {{-- Rank & Mode --}}
                        <div class="flex justify-between items-start border-b border-white/5 pb-3">
                            <div class="flex flex-col">
                                <span class="text-gray-500 text-xs mb-1">Item / Rank</span>
                                <span class="text-white font-bold">{{ $migration->rank->name }}</span>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-gray-500 text-xs mb-1">Mode Server</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                    {{ $migration->category->name }}
                                </span>
                            </div>
                        </div>

                        {{-- Akun --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="bg-red-500/5 p-2.5 rounded-lg border border-red-500/10">
                                <span class="block text-[10px] text-red-400 uppercase font-bold mb-1">Dari (Lama)</span>
                                <span class="block text-sm text-white font-mono truncate">{{ $migration->old_gamertag }}</span>
                            </div>
                            <div class="bg-green-500/5 p-2.5 rounded-lg border border-green-500/10">
                                <span class="block text-[10px] text-green-400 uppercase font-bold mb-1">Ke (Baru)</span>
                                <span class="block text-sm text-white font-mono truncate">{{ $migration->new_gamertag }}</span>
                            </div>
                        </div>

                        {{-- Info Tambahan --}}
                        <div class="space-y-2 pt-2 border-t border-white/5">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-xs">Metode Pembayaran</span>
                                <span class="text-gray-300 text-xs">{{ $migration->paymentMethod->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-xs">Waktu</span>
                                <span class="text-gray-300 text-xs">{{ $migration->created_at->translatedFormat('d F Y H:i') }}</span>
                            </div>
                            @if($migration->whatsapp_number)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-xs">WhatsApp</span>
                                <span class="text-gray-300 text-xs">{{ $migration->whatsapp_number }}</span>
                            </div>
                            @endif
                        </div>

                        {{-- Total --}}
                        <div class="flex justify-between items-center pt-3 border-t border-white/5">
                            <span class="text-gray-400 text-xs font-bold uppercase">Total Biaya</span>
                            <span class="font-heading text-lg text-primary">Rp {{ number_format($migration->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Tombol Bantuan --}}
                    @if($settings?->admin_phone)
                        <div class="mt-6">
                            <a href="https://wa.me/{{ $settings->admin_phone }}?text=Halo Admin, saya mau tanya soal Migrasi ID: {{ $migration->uuid }}" target="_blank" class="w-full block text-center py-3 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/30 transition text-sm font-bold">
                                Hubungi Admin
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>