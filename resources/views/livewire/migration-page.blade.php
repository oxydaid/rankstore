<div class="py-24 sm:py-32 relative isolate min-h-screen overflow-hidden">
    
    {{-- Background Glow --}}
    <div class="absolute top-0 left-0 -translate-y-1/2 w-[600px] h-[600px] bg-purple-500/10 blur-[120px] rounded-full pointer-events-none z-[-1]"></div>

    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h1 class="text-3xl font-heading font-bold text-white sm:text-5xl text-glow mb-4">
                JASA <span class="text-transparent bg-clip-text bg-linear-to-r from-purple-400 to-pink-500">MIGRASI RANK</span>
            </h1>
            <p class="text-gray-400 text-lg">
                Pindahkan rank Anda ke akun baru dengan aman dan legal.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
            
            {{-- KOLOM KIRI: FORM INPUT --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- 1. Pilih Produk --}}
                <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md">
                    <h2 class="text-xl font-heading text-white mb-6 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-600 text-white text-sm">1</span>
                        Pilih Rank Asal
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Mode Server</label>
                            <select wire:model.live="category_id" class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500">
                                <option value="">-- Pilih Mode --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Rank yang Dimiliki</label>
                            <select wire:model.live="rank_id" class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500" {{ !$category_id ? 'disabled' : '' }}>
                                <option value="">-- Pilih Rank --</option>
                                @foreach($ranks as $rank)
                                    <option value="{{ $rank->id }}">{{ $rank->name }} (Harga Asli: Rp {{ number_format($rank->price, 0, ',', '.') }})</option>
                                @endforeach
                            </select>
                            @error('rank_id') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                {{-- 2. Data Transfer --}}
                <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md">
                    <h2 class="text-xl font-heading text-white mb-6 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-600 text-white text-sm">2</span>
                        Data Akun
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-red-500/10 p-4 rounded-xl border border-red-500/20">
                            <label class="block text-xs font-bold text-red-400 uppercase mb-2">Dari (Akun Lama)</label>
                            <input wire:model="old_gamertag" type="text" placeholder="Gamertag Lama" 
                                class="w-full bg-dark-900 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:ring-1 focus:ring-red-500">
                            @error('old_gamertag') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="bg-green-500/10 p-4 rounded-xl border border-green-500/20">
                            <label class="block text-xs font-bold text-green-400 uppercase mb-2">Ke (Akun Baru)</label>
                            <input wire:model="new_gamertag" type="text" placeholder="Gamertag Baru" 
                                class="w-full bg-dark-900 border border-white/10 rounded-lg px-3 py-2 text-white text-sm focus:ring-1 focus:ring-green-500">
                            @error('new_gamertag') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">WhatsApp (Wajib)</label>
                            <input wire:model="whatsapp" type="number" placeholder="08xxxxxxxx" 
                                class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500">
                            @error('whatsapp') <span class="text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Discord (Opsional)</label>
                            <input wire:model="discord" type="text" placeholder="username#1234" 
                                class="w-full bg-dark-900 border border-white/10 rounded-xl px-4 py-3 text-white focus:ring-2 focus:ring-purple-500">
                        </div>
                    </div>
                </div>

                {{-- 3. Pembayaran --}}
                <div class="bg-dark-800/60 p-6 rounded-2xl ring-1 ring-white/10 backdrop-blur-md">
                    <h2 class="text-xl font-heading text-white mb-6 flex items-center gap-3">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-600 text-white text-sm">3</span>
                        Metode Pembayaran
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($paymentMethods as $method)
                            <label class="relative flex items-center gap-4 p-4 rounded-xl border cursor-pointer transition-all duration-200
                                {{ $paymentMethodId == $method->id 
                                    ? 'bg-purple-500/10 border-purple-500 ring-1 ring-purple-500' 
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
                                    <div class="absolute top-3 right-3 text-purple-500">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    </div>
                                @endif
                            </label>
                        @endforeach
                    </div>
                    @error('paymentMethodId') <span class="text-red-400 text-xs mt-2 block">{{ $message }}</span> @enderror

                    {{-- INFO REKENING & UPLOAD (HANYA MANUAL) --}}
                    @if($this->selectedPaymentMethod && $this->selectedPaymentMethod->is_manual)
                        
                        {{-- Info Rekening --}}
                        @php
                            // Logika Cek QRIS Manual
                            // Pastikan $appSettings dikirim dari component
                            $isQrisManual = \Illuminate\Support\Str::contains(strtoupper($this->selectedPaymentMethod->name), 'QRIS');
                        @endphp

                        <div class="mt-6 p-5 rounded-xl bg-dark-900/80 border border-purple-500/30 animate-fade-in-up">
                            
                            {{-- JIKA QRIS MANUAL & GAMBAR TERSEDIA --}}
                            @if($isQrisManual && $appSettings?->qris_image)
                                <div class="text-center space-y-4">
                                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-wider">Scan QRIS Manual:</h4>
                                    
                                    <div class="bg-white p-3 rounded-xl inline-block mx-auto shadow-lg ring-4 ring-white/10">
                                        <img src="{{ asset('img/'.$appSettings->qris_image) }}" alt="Scan QRIS Manual" class="w-48 h-48 object-contain mx-auto">
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm text-gray-400">A.N <span class="font-bold text-white">{{ $this->selectedPaymentMethod->account_holder }}</span></p>
                                        <p class="text-xs text-purple-400 mt-1 font-bold animate-pulse">Mohon transfer sesuai nominal Total Bayar!</p>
                                    </div>
                                </div>

                            {{-- JIKA BUKAN QRIS (TRANSFER BANK BIASA) --}}
                            @else
                                <h4 class="text-sm font-bold text-gray-400 mb-3 uppercase tracking-wider">Silakan Transfer Ke:</h4>
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                    <div>
                                        <p class="text-2xl font-heading text-white tracking-widest">{{ $this->selectedPaymentMethod->account_number }}</p>
                                        <p class="text-sm text-gray-400">A.N {{ $this->selectedPaymentMethod->account_holder }}</p>
                                    </div>
                                    <button onclick="navigator.clipboard.writeText('{{ $this->selectedPaymentMethod->account_number }}'); alert('Nomor disalin!')" class="text-xs bg-white/10 hover:bg-white/20 text-white px-3 py-2 rounded-lg transition border border-white/10">
                                        Salin
                                    </button>
                                </div>
                            @endif

                            {{-- Instruksi Tambahan --}}
                            @if($this->selectedPaymentMethod->description)
                                <div class="mt-4 text-sm text-gray-400 bg-white/5 p-3 rounded-lg border border-white/5">
                                    <strong class="text-purple-400">Instruksi:</strong> {{ $this->selectedPaymentMethod->description }}
                                </div>
                            @endif
                        </div>

                        {{-- FORM UPLOAD SESUAI DESAIN --}}
                        <div class="mt-8 animate-fade-in-up">
                            <h2 class="text-xl font-heading text-white mb-4 flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500 text-white text-sm">4</span>
                                Upload Bukti Transfer
                            </h2>

                            {{-- Alert Box Merah --}}
                            <div class="bg-red-500/10 border border-red-500/20 p-4 rounded-xl flex items-start gap-3 mb-4">
                                <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-sm text-red-400">
                                    Anda <strong>wajib</strong> mengupload bukti transfer sekarang.
                                </p>
                            </div>

                            {{-- Dropzone --}}
                            <div class="relative">
                                <input wire:model="paymentProof" type="file" id="paymentProof" class="peer hidden" accept="image/*">
                                <label for="paymentProof" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-600 rounded-2xl cursor-pointer bg-dark-900/50 hover:bg-dark-800/80 hover:border-purple-500 transition-all group">
                                    @if($paymentProof)
                                        {{-- Preview --}}
                                        <img src="{{ $paymentProof->temporaryUrl() }}" class="h-full w-full object-contain p-2 rounded-xl">
                                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                                            <span class="text-white text-sm font-bold">Ganti Gambar</span>
                                        </div>
                                    @else
                                        {{-- Placeholder --}}
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                            <svg class="w-12 h-12 mb-3 text-gray-400 group-hover:text-purple-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                            <p class="mb-1 text-sm font-semibold text-gray-300 group-hover:text-white">Klik untuk upload</p>
                                            <p class="text-xs text-gray-500">Max 2MB (JPG/PNG)</p>
                                        </div>
                                    @endif
                                </label>

                                {{-- Loading Indicator --}}
                                <div wire:loading wire:target="paymentProof" class="absolute inset-0 flex items-center justify-center bg-dark-900/80 rounded-2xl z-10">
                                    <div class="text-center">
                                        <svg class="animate-spin h-8 w-8 text-purple-500 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="text-xs text-purple-500 font-bold uppercase tracking-wider">Mengupload...</span>
                                    </div>
                                </div>
                            </div>
                            @error('paymentProof') <span class="text-red-400 text-sm font-bold block text-center mt-2 animate-pulse">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

            </div>

            {{-- KOLOM KANAN: RINGKASAN --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 space-y-6">
                    
                    <div class="bg-dark-800/80 p-6 rounded-3xl ring-1 ring-white/10 backdrop-blur-md shadow-2xl relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-linear-to-r from-purple-500 to-pink-500"></div>
                        
                        <h3 class="text-lg font-heading text-white mb-4">Ringkasan Biaya</h3>

                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between text-gray-400">
                                <span>Harga Rank Asli</span>
                                <span>{{ $this->selectedRank ? 'Rp '.number_format($this->selectedRank->price, 0, ',', '.') : '-' }}</span>
                            </div>
                            
                            <div class="flex justify-between text-purple-400 font-bold">
                                <span>Biaya Jasa ({{ $migrationFeePercent }}%)</span>
                                <span>{{ $this->selectedRank ? 'Rp '.number_format($this->migrationCost, 0, ',', '.') : '-' }}</span>
                            </div>

                            <div class="flex justify-between text-gray-400">
                                <span>Biaya Layanan</span>
                                <span>Rp {{ number_format($this->calculatedAdminFee, 0, ',', '.') }}</span>
                            </div>

                            <div class="pt-4 border-t border-white/10 flex justify-between items-center">
                                <span class="text-white font-bold">Total Bayar</span>
                                <span class="text-2xl font-heading text-white">
                                    Rp {{ number_format($this->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Terms & Action --}}
                    <div class="bg-dark-800/50 p-6 rounded-2xl border border-white/5 space-y-4">
                        
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input wire:model="tosAgreement" type="checkbox" class="mt-1 rounded border-gray-600 bg-dark-900 text-purple-500 focus:ring-purple-500">
                            <span class="text-xs text-gray-400 leading-relaxed">
                                Saya menyetujui <a href="{{ route('terms') }}" target="_blank" class="text-purple-400 underline">Syarat & Ketentuan</a>.
                            </span>
                        </label>
                        @error('tosAgreement') <span class="block text-xs text-red-400">{{ $message }}</span> @enderror

                        <label class="flex items-start gap-3 cursor-pointer bg-red-500/5 p-3 rounded-lg border border-red-500/20">
                            <input wire:model="riskAgreement" type="checkbox" class="mt-1 rounded border-red-500 bg-dark-900 text-red-500 focus:ring-red-500">
                            <span class="text-xs text-red-300 leading-relaxed">
                                <strong>Saya sadar risiko migrasi.</strong> Saya tidak akan komplain jika terjadi masalah keamanan/hackback di kemudian hari.
                            </span>
                        </label>
                        @error('riskAgreement') <span class="block text-xs text-red-400">{{ $message }}</span> @enderror

                        <button wire:click="submitMigration" wire:loading.attr="disabled" wire:target="submitMigration"
                            class="w-full rounded-xl bg-linear-to-r from-purple-600 to-pink-600 px-4 py-3.5 text-sm font-heading font-bold text-white shadow-lg hover:shadow-purple-500/20 hover:scale-[1.02] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2">
                            
                            <span wire:loading.remove wire:target="submitMigration">
                                {{ ($this->selectedPaymentMethod && !$this->selectedPaymentMethod->is_manual) ? 'BAYAR SEKARANG' : 'AJUKAN MIGRASI' }}
                            </span>
                            <span wire:loading.flex wire:target="submitMigration" class="items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>MEMPROSES...</span>
                            </span>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>