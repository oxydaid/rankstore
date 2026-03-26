<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden">
    
    {{-- Background Glow --}}
    <div class="absolute top-0 left-0 -translate-y-1/2 w-[600px] h-[600px] bg-primary/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="mb-10">
            <a href="{{ route('shop') }}" class="text-sm font-semibold text-gray-400 hover:text-white transition flex items-center gap-2 mb-4">
                &larr; Kembali ke Shop
            </a>
            <h1 class="text-3xl font-heading font-bold text-white sm:text-4xl">CHECKOUT</h1>
            <p class="text-gray-400 mt-2">Lengkapi data di bawah ini untuk menyelesaikan pesanan Anda.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            
            {{-- KOLOM KIRI: FORM & DATA --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- 1. Data Player --}}
                <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md">
                    <h2 class="text-xl font-heading text-white mb-6 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm">1</span>
                        Informasi Player
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Gamertag (Wajib)</label>
                            <input wire:model="gamertag" type="text" placeholder="Contoh: Steve123" 
                                class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            @error('gamertag') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">WhatsApp (Wajib)</label>
                            <input wire:model="whatsapp" type="number" placeholder="08xxxxxxxx" 
                                class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent transition">
                            @error('whatsapp') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-300 mb-2">Discord Username (Opsional)</label>
                            <input wire:model="discord" type="text" placeholder="Contoh: user#1234" 
                                class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-primary focus:border-transparent transition">
                        </div>
                    </div>
                </div>

                {{-- 2. Upgrade Option --}}
                @if($previousRanks->count() > 0)
                    <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-heading text-white flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-secondary text-white text-sm">2</span>
                                Upgrade Rank?
                            </h2>
                            <label class="inline-flex items-center cursor-pointer">
                                <input wire:model.live="isUpgrade" type="checkbox" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-0.5 after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>

                        @if($isUpgrade)
                            <div class="space-y-4 bg-dark-900/50 p-4 rounded-xl border border-white/5 animate-fade-in-up">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Pilih Rank Lama Anda</label>
                                    <select wire:model.live="previousRankId" class="w-full bg-dark-800 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-primary">
                                        <option value="">-- Pilih Rank --</option>
                                        @foreach($previousRanks as $prev)
                                            <option value="{{ $prev->id }}">{{ $prev->name }} (Rp {{ number_format($prev->price, 0, ',', '.') }})</option>
                                        @endforeach
                                    </select>
                                    @error('previousRankId') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-2">Upload Bukti Rank Lama (Screenshot Profile)</label>
                                    <input wire:model="upgradeProof" type="file" class="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition cursor-pointer">
                                    @error('upgradeProof') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- 3. Metode Pembayaran --}}
                <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md">
                    <h2 class="text-xl font-heading text-white mb-6 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-green-600 text-white text-sm">3</span>
                        Pilih Metode Pembayaran
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($paymentMethods as $method)
                            <label class="relative flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all duration-200
                                {{ $paymentMethodId == $method->id 
                                    ? 'bg-primary/10 border-primary ring-1 ring-primary' 
                                    : 'bg-dark-900 border-white/10 hover:border-white/30' 
                                }}">
                                <input wire:model.live="paymentMethodId" type="radio" value="{{ $method->id }}" class="sr-only">
                                
                                @if($method->logo)
                                    <img src="{{ asset('img/'.$method->logo) }}" alt="{{ $method->name }}" class="h-8 w-auto object-contain">
                                @else
                                    <div class="h-8 w-8 bg-gray-700 rounded flex items-center justify-center text-xs">Logo</div>
                                @endif
                                
                                <div class="flex-1">
                                    <div class="font-bold text-white text-sm">{{ $method->name }}</div>
                                    @if($method->is_manual)
                                        <div class="text-xs text-gray-400">Manual Check</div>
                                    @else
                                        <div class="text-xs text-green-400">Otomatis</div>
                                    @endif
                                </div>

                                @if($paymentMethodId == $method->id)
                                    <div class="absolute top-3 right-3 text-primary">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('paymentMethodId') <span class="text-red-400 text-xs mt-2 block">{{ $message }}</span> @enderror

                    {{-- INFORMASI REKENING / QRIS (HANYA MANUAL) --}}
                    @if($this->selectedPaymentMethod && $this->selectedPaymentMethod->is_manual)
                        <div class="mt-6 p-5 rounded-xl bg-dark-900/80 border border-primary/30 animate-fade-in-up">
                            @if($this->isQris && $appSettings?->qris_image)
                                <div class="text-center space-y-4">
                                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Scan QRIS di bawah ini:</h4>
                                    <div class="bg-white p-3 rounded-xl inline-block mx-auto shadow-lg ring-4 ring-white/10">
                                        <img src="{{ asset('img/'.$appSettings->qris_image) }}" alt="Scan QRIS" class="w-48 h-48 object-contain mx-auto">
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-400">A.N <span class="font-bold text-white">{{ $this->selectedPaymentMethod->account_holder }}</span></p>
                                        <p class="text-xs text-primary mt-1 font-bold animate-pulse">Mohon transfer sesuai nominal Total Bayar!</p>
                                    </div>
                                </div>
                            @else
                                <h4 class="text-sm font-bold text-gray-400 mb-3 uppercase tracking-wider">Silakan Transfer Ke:</h4>
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div>
                                        <p class="text-2xl font-heading text-white tracking-widest">{{ $this->selectedPaymentMethod->account_number }}</p>
                                        <p class="text-sm text-gray-400">A.N {{ $this->selectedPaymentMethod->account_holder }}</p>
                                    </div>
                                    <button onclick="navigator.clipboard.writeText('{{ $this->selectedPaymentMethod->account_number }}'); alert('Nomor disalin!')" class="text-xs bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded-lg transition flex items-center gap-2 border border-white/10">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                        Salin
                                    </button>
                                </div>
                            @endif
                            
                            @if($this->selectedPaymentMethod->description)
                                <div class="mt-4 text-sm text-gray-400 bg-white/5 p-3 rounded-lg border border-white/5">
                                    <strong class="text-primary">Instruksi:</strong> {{ $this->selectedPaymentMethod->description }}
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- 4. UPLOAD BUKTI (HANYA MANUAL) --}}
                @if($this->selectedPaymentMethod && $this->selectedPaymentMethod->is_manual)
                    <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md animate-fade-in-up">
                        <h2 class="text-xl font-heading text-white mb-6 flex items-center gap-3">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500 text-white text-sm">4</span>
                            Upload Bukti Transfer
                        </h2>
                        
                        <div class="space-y-4">
                            <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl">
                                <p class="text-sm text-red-400 flex gap-2">
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>Anda <strong>wajib</strong> mengupload bukti transfer sekarang.</span>
                                </p>
                            </div>

                            <div class="relative">
                                <input wire:model="paymentProof" type="file" id="paymentProof" class="peer hidden">
                                <label for="paymentProof" class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed border-gray-600 rounded-2xl cursor-pointer bg-dark-900 hover:bg-dark-800 hover:border-primary transition-all group">
                                    @if($paymentProof)
                                        <img src="{{ $paymentProof->temporaryUrl() }}" class="h-full w-auto object-contain py-2">
                                    @else
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-primary transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                            <p class="mb-2 text-sm text-gray-400"><span class="font-semibold">Klik untuk upload</span></p>
                                            <p class="text-xs text-gray-500">Max 2MB</p>
                                        </div>
                                    @endif
                                </label>
                            </div>
                            @error('paymentProof') <span class="text-red-400 text-sm font-bold">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

            </div>

            {{-- KOLOM KANAN: RINGKASAN --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    
                    {{-- Card Produk --}}
                    <div class="bg-dark-800/80 p-6 rounded-3xl ring-1 ring-white/10 backdrop-blur-md shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-primary to-secondary"></div>
                        
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-heading text-white">{{ $rank->name }}</h3>
                            <span class="px-2 py-1 rounded text-xs font-bold bg-white/10 text-gray-300">{{ $rank->category->name }}</span>
                        </div>

                        {{-- Benefit Rank --}}
                        <div class="border-t border-white/10 py-4 mb-4">
                            <h4 class="text-sm font-bold text-gray-400 mb-3 uppercase tracking-wider">Benefit Rank:</h4>
                            <ul class="space-y-2 text-sm text-gray-300 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                                @if($rank->description)
                                    @foreach($rank->description as $item)
                                        <li class="flex gap-2 items-start">
                                            <svg class="w-4 h-4 text-green-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            <span>{{ $item['feature'] }}</span>
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>

                        {{-- Kode Promo --}}
                        <div class="border-t border-white/10 py-4">
                            <label class="block text-xs font-bold text-gray-400 mb-2 uppercase tracking-wider">Punya Kode Promo?</label>
                            @if($appliedPromoCodeId)
                                <div class="flex items-center justify-between bg-green-500/10 border border-green-500/20 p-3 rounded-xl">
                                    <div class="flex items-center gap-2 text-green-400">
                                        <span class="font-bold tracking-wide">{{ $promoCodeInput }}</span>
                                    </div>
                                    <button wire:click="removePromo" class="text-xs text-red-400 hover:text-red-300 underline">Hapus</button>
                                </div>
                            @else
                                <div class="flex gap-2">
                                    <input wire:model="promoCodeInput" type="text" placeholder="Masukkan Kode" class="w-full bg-dark-900 border border-white/10 rounded-lg px-3 py-2 text-sm text-white uppercase font-mono">
                                    <button wire:click="applyPromo" class="bg-primary hover:bg-primary/80 text-white px-4 py-2 rounded-lg text-xs font-bold transition">
                                        Gunakan
                                    </button>
                                </div>
                                @if($promoError) <p class="text-xs text-red-400 mt-1">{{ $promoError }}</p> @endif
                            @endif
                        </div>

                        {{-- Rincian Harga --}}
                        <div class="border-t border-white/10 pt-4 space-y-2">
                            <div class="flex justify-between text-sm text-gray-400">
                                <span>Harga Rank</span>
                                <span>Rp {{ number_format($rank->price, 0, ',', '.') }}</span>
                            </div>
                            
                            @if($isUpgrade && $previousRankId)
                                @php $prev = $previousRanks->find($previousRankId); @endphp
                                @if($prev)
                                    <div class="flex justify-between text-sm text-green-400">
                                        <span>Potongan ({{ $prev->name }})</span>
                                        <span>- Rp {{ number_format($prev->price, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                            @endif

                            @if($appliedPromoCodeId && $this->discountAmount > 0)
                                <div class="flex justify-between text-sm text-yellow-400 font-bold">
                                    <span>Diskon Promo</span>
                                    <span>- Rp {{ number_format($this->discountAmount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="flex justify-between text-sm text-gray-400">
                                <span>Biaya Layanan</span>
                                <span>Rp {{ number_format($this->calculatedAdminFee, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-white/10">
                                <span class="text-white font-bold">Total Bayar</span>
                                <span class="text-2xl font-heading text-primary">
                                    Rp {{ number_format($this->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Terms & Button --}}
                    <div class="bg-dark-800/50 p-6 rounded-2xl border border-white/5">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input wire:model="tosAgreement" type="checkbox" class="mt-1 rounded border-gray-600 bg-dark-900 text-primary focus:ring-primary cursor-pointer">
                            <span class="text-xs text-gray-400 leading-relaxed">
                                Saya menyetujui <a href="{{ route('terms') }}" target="_blank" class="text-primary underline hover:text-white transition">Syarat & Ketentuan</a>.
                            </span>
                        </label>
                        @error('tosAgreement') <span class="block mt-2 text-xs text-red-400">{{ $message }}</span> @enderror

                        <button wire:click="submitOrder" wire:loading.attr="disabled" wire:target="submitOrder"
                            class="w-full mt-6 rounded-xl bg-linear-to-r from-primary to-secondary px-4 py-3.5 text-sm font-heading font-bold text-white shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2 relative">
                            
                            {{-- Teks Normal --}}
                            <span wire:loading.remove wire:target="submitOrder">
                                {{ ($this->selectedPaymentMethod && !$this->selectedPaymentMethod->is_manual) ? 'BAYAR SEKARANG' : 'KONFIRMASI & BAYAR' }}
                            </span>

                            {{-- Loading State --}}
                            <span wire:loading.flex wire:target="submitOrder" class="items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>MEMPROSES...</span>
                            </span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>