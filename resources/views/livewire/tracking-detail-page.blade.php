<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden" >
    
    {{-- Background Glow --}}
    <div class="absolute top-0 right-0 -translate-y-1/2 w-[500px] h-[500px] bg-green-500/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-4xl px-6 lg:px-8">

        {{-- ALERT SUKSES (Pesanan Dibuat) --}}
        @if (session()->has('success_order'))
            <div class="mb-10 bg-green-500/10 border border-green-500/20 p-6 rounded-2xl flex items-start gap-4 animate-fade-in-up">
                <div class="bg-green-500/20 p-2 rounded-full">
                    <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-heading text-white">Pesanan Berhasil Dibuat!</h3>
                    <p class="text-gray-400 text-sm mt-1">
                        Terima kasih telah melakukan pemesanan.
                        {{-- Hanya tampilkan instruksi jika masih pending --}}
                        @if($order->status === 'pending' && $order->payment_url)
                            Silakan selesaikan pembayaran melalui tombol di bawah.
                        @else
                            Data dan bukti transfer Anda telah kami terima dan sedang dalam antrian verifikasi Admin.
                        @endif
                    </p>
                </div>
            </div>
        @endif

        {{-- INSTRUKSI PEMBAYARAN (PERBAIKAN LOGIKA TAMPIL) --}}
        {{-- Hanya tampil jika status PENDING. Jika Processing/Completed, sembunyikan --}}
        @if($order->status === 'pending' && $order->payment_url)
            <div class="mb-12 text-center animate-fade-in-up">
                <div class="bg-dark-800/80 border border-white/10 p-6 rounded-3xl inline-block max-w-md w-full shadow-2xl relative overflow-hidden group">
                    
                    {{-- Glow Effect Internal --}}
                    <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-primary to-secondary"></div>
                    <div class="absolute -bottom-20 -right-20 w-40 h-40 bg-primary/20 blur-3xl rounded-full pointer-events-none"></div>

                    {{-- Cek apakah URL adalah Gambar (AriePulsa) atau Link (Tokopay) --}}
                    @php
                        $isImage = \Illuminate\Support\Str::endsWith($order->payment_url, ['.png', '.jpg', '.jpeg']);
                    @endphp

                    @if($isImage)
                        {{-- TAMPILAN KHUSUS ARIEPULSA (QRIS) --}}
                        <h3 class="text-xl font-heading font-bold text-white mb-2">Scan QRIS Berikut</h3>
                        <p class="text-gray-400 text-xs mb-6">
                            Scan menggunakan DANA/OVO/Gopay/ShopeePay/Mobile Banking.
                        </p>
                        
                        {{-- Container QRIS --}}
                        <div class="bg-white p-4 rounded-2xl shadow-inner mb-6 mx-auto w-fit group-hover:scale-[1.02] transition-transform duration-300">
                            <img src="{{ $order->payment_url }}" alt="QRIS Payment" class="w-56 h-56 object-contain rounded-lg">
                        </div>

                        {{-- Total Bayar (Statis) --}}
                        <div class="mb-6 flex justify-center">
                            <div class="bg-dark-900 border border-white/10 rounded-xl px-6 py-3 text-center shadow-lg">
                                <span class="block text-[10px] text-gray-400 uppercase tracking-wider font-bold mb-1">Total Yang Harus Dibayar</span>
                                <span class="block text-2xl font-heading font-bold text-primary tracking-wide">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        {{-- Instruksi Transfer --}}
                        <div class="bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-3 mb-6">
                            <p class="text-xs text-yellow-200 font-medium flex items-start justify-center gap-2">
                                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>WAJIB TRANSFER SESUAI NOMINAL (HINGGA 3 DIGIT TERAKHIR) AGAR TERDETEKSI OTOMATIS.</span>
                            </p>
                        </div>

                        {{-- Tombol Cek Status --}}
                        <button wire:click="checkPaymentStatus" wire:loading.attr="disabled" class="w-full rounded-xl bg-dark-700 hover:bg-dark-600 border border-white/10 px-4 py-3 text-sm font-bold text-white transition flex items-center justify-center gap-2">
                            <span wire:loading.remove>CEK STATUS PEMBAYARAN</span>
                            <span wire:loading>MEMERIKSA...</span>
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>

                    @else
                        {{-- TAMPILAN TOKOPAY (LINK) --}}
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <svg class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h3 class="text-xl font-heading text-white">Pembayaran Siap!</h3>
                        </div>
                        <p class="text-gray-300 text-sm mb-6">Silakan selesaikan pembayaran Anda melalui halaman Payment Gateway.</p>
                        <a href="{{ $order->payment_url }}" target="_blank" class="inline-flex items-center justify-center gap-2 rounded-xl bg-green-600 px-8 py-4 text-base font-heading font-bold text-white shadow-lg shadow-green-500/20 hover:bg-green-500 hover:scale-105 transition-all w-full sm:w-auto">
                            <span>LANJUTKAN KE PEMBAYARAN</span>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                        </a>
                    @endif
                    
                </div>
            </div>
        @endif

        {{-- HEADER --}}
        <div class="text-center mb-12">
            <p class="text-sm font-mono text-primary mb-2 tracking-widest">ORDER ID: {{ $order->uuid }}</p>
            <h1 class="text-4xl font-heading font-bold text-white text-glow">STATUS PESANAN</h1>
        </div>

        {{-- STATUS TRACKER (QUEST LOG) --}}
        <div class="bg-dark-800/60 rounded-3xl ring-1 ring-white/10 backdrop-blur-md shadow-2xl overflow-hidden p-8 sm:p-12">
            
            {{-- Progress Bar Logic --}}
            @php
                $progress = match($order->status) {
                    'pending' => '33%',
                    'processing' => '66%',
                    'completed', 'cancelled' => '100%',
                    default => '0%'
                };
                
                $barColor = match($order->status) {
                    'cancelled' => 'bg-red-500',
                    'completed' => 'bg-green-500',
                    default => 'bg-primary'
                };
            @endphp

            <div class="relative flex justify-between mb-16">
                {{-- Garis Background --}}
                <div class="absolute top-1/2 left-0 w-full h-1 bg-dark-700 -translate-y-1/2 z-0"></div>
                
                {{-- Garis Active --}}
                <div class="absolute top-1/2 left-0 h-1 {{ $barColor }} -translate-y-1/2 z-0 transition-all duration-1000" style="width: {{ $progress }}"></div>

                {{-- Step 1: Order Dibuat --}}
                <div class="relative z-10 flex flex-col items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold shadow-lg shadow-primary/50">1</div>
                    <span class="text-xs font-bold text-white uppercase absolute -bottom-8 w-32 text-center">Order Dibuat</span>
                </div>

                {{-- Step 2: Verifikasi / Bayar --}}
                <div class="relative z-10 flex flex-col items-center gap-2">
                    @php
                        $step2Active = in_array($order->status, ['processing', 'completed', 'cancelled']);
                        // Label dinamis: Jika pending & ada URL -> Menunggu Bayar
                        $step2Label = ($order->payment_url && $order->status === 'pending') ? 'Menunggu Bayar' : 'Verifikasi';
                        $step2Color = $order->status === 'cancelled' ? 'bg-red-500' : 'bg-primary';
                    @endphp
                    <div class="w-8 h-8 rounded-full {{ $step2Active ? $step2Color : 'bg-dark-700 border-2 border-dark-600' }} flex items-center justify-center text-white text-xs font-bold transition-colors duration-500">2</div>
                    <span class="text-xs font-bold {{ $step2Active ? 'text-white' : 'text-gray-500' }} uppercase absolute -bottom-8 w-32 text-center">{{ $step2Label }}</span>
                </div>

                {{-- Step 3: Selesai / Batal --}}
                <div class="relative z-10 flex flex-col items-center gap-2">
                    @php
                        $isCompleted = $order->status === 'completed';
                        $isCancelled = $order->status === 'cancelled';
                    @endphp
                    
                    <div class="w-8 h-8 rounded-full {{ $isCompleted ? 'bg-green-500' : ($isCancelled ? 'bg-red-500' : 'bg-dark-700 border-2 border-dark-600') }} flex items-center justify-center text-white text-xs font-bold transition-colors duration-500">
                        @if($isCancelled)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        @elseif($isCompleted)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        @else
                            3
                        @endif
                    </div>

                    @if($isCancelled)
                        <span class="text-xs font-bold text-red-500 uppercase absolute -bottom-8 w-32 text-center">Dibatalkan</span>
                    @elseif($isCompleted)
                        <span class="text-xs font-bold text-green-400 uppercase absolute -bottom-8 w-32 text-center">Selesai</span>
                    @else
                        <span class="text-xs font-bold text-gray-500 uppercase absolute -bottom-8 w-32 text-center">Selesai</span>
                    @endif
                </div>
            </div>

            {{-- DETAIL STATUS & INFO --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mt-8">
                
                {{-- Info Status --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Status Saat Ini</h3>
                    
                    @if($order->status === 'pending')
                        <div class="bg-yellow-500/10 border border-yellow-500/20 p-4 rounded-xl">
                            <p class="text-yellow-400 font-bold flex items-center gap-2">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                
                                @if($order->payment_url)
                                    Menunggu Pembayaran
                                @else
                                    Menunggu Verifikasi Admin
                                @endif
                            </p>
                            
                            @if($order->payment_url)
                                <p class="text-sm text-gray-400 mt-2">Silakan selesaikan pembayaran Anda melalui link yang tersedia di atas.</p>
                            @else
                                <p class="text-sm text-gray-400 mt-2">Bukti pembayaran Anda sudah masuk. Admin akan segera mengeceknya (Estimasi 10-60 menit).</p>
                            @endif
                        </div>
                    
                    @elseif($order->status === 'processing')
                        <div class="bg-blue-500/10 border border-blue-500/20 p-4 rounded-xl">
                            <p class="text-blue-400 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                Sedang Diproses
                            </p>
                            <p class="text-sm text-gray-400 mt-2">Pembayaran diterima! Admin sedang memproses rank ke akun Anda. Mohon tunggu sebentar lagi.</p>
                        </div>

                    @elseif($order->status === 'completed')
                        <div class="bg-green-500/10 border border-green-500/20 p-4 rounded-xl">
                            <p class="text-green-400 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Transaksi Selesai
                            </p>
                            <p class="text-sm text-gray-400 mt-2">Rank sudah aktif! Silakan cek di dalam game. Terima kasih sudah berbelanja.</p>
                            
                            @if($order->server_invoice)
                                <a href="{{ Storage::url($order->server_invoice) }}" target="_blank" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-white bg-green-600 px-3 py-2 rounded-lg hover:bg-green-500 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download Invoice Server
                                </a>
                            @endif
                        </div>

                    @elseif($order->status === 'cancelled')
                        <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl">
                            <p class="text-red-400 font-bold flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Dibatalkan
                            </p>
                            <p class="text-sm text-gray-400 mt-2">Pesanan dibatalkan oleh Admin atau Pembayaran Expired.</p>
                            @if($order->notes)
                                <p class="text-sm text-red-300 mt-2"><strong>Alasan:</strong> {{ $order->notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Info Player & Item --}}
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase mb-4">Detail Item</h3>
                    <div class="bg-dark-900/50 p-4 rounded-xl border border-white/5 space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Gamertag</span>
                            <span class="text-white font-bold">{{ $order->gamertag }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Rank</span>
                            <span class="text-primary font-bold">{{ $order->rank->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Total Bayar</span>
                            <span class="text-white font-mono">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500 text-sm">Metode</span>
                            <span class="text-gray-300">{{ $order->paymentMethod->name }}</span>
                        </div>
                    </div>

                    {{-- Tombol Bantuan (Nomor HP dari AppSetting) --}}
                    @if($settings?->admin_phone)
                        <div class="mt-6">
                            <a href="https://wa.me/{{ $settings->admin_phone }}?text=Halo Admin, saya butuh bantuan soal Order ID: {{ $order->uuid }}" target="_blank" class="w-full block text-center py-3 rounded-xl border border-white/10 text-gray-400 hover:text-white hover:border-white/30 transition text-sm font-bold">
                                Butuh Bantuan? Hubungi Admin
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>