<?php

namespace App\Livewire;

use App\Models\AppSetting;
use App\Models\Category;
use App\Models\MigrationRequest;
use App\Models\PaymentMethod;
use App\Models\Rank;
use App\Services\AriePulsaService;
use App\Services\TokopayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Title('Jasa Migrasi Rank - Oxyda Store')]
class MigrationPage extends Component
{
    use WithFileUploads;

    // Form Inputs
    public $category_id;

    public $rank_id;

    public $old_gamertag;

    public $new_gamertag;

    public $whatsapp;

    public $discord;

    public $paymentMethodId;

    public $paymentProof;

    public $tosAgreement = false;

    public $riskAgreement = false;

    // Logic Properties
    public $migrationFeePercent = 20;

    public function mount()
    {
        $settings = AppSetting::first();
        if ($settings) {
            $this->migrationFeePercent = $settings->migration_fee_percent ?? 20;
        }
    }

    // --- COMPUTED PROPERTIES ---

    public function getSelectedRankProperty()
    {
        if (! $this->rank_id) {
            return null;
        }

        return Rank::find($this->rank_id);
    }

    public function getSelectedPaymentMethodProperty()
    {
        if (! $this->paymentMethodId) {
            return null;
        }

        return PaymentMethod::find($this->paymentMethodId);
    }

    public function getMigrationCostProperty()
    {
        $rank = $this->selectedRank;
        if (! $rank) {
            return 0;
        }

        return $rank->price * ($this->migrationFeePercent / 100);
    }

    public function getCalculatedAdminFeeProperty()
    {
        $method = $this->selectedPaymentMethod;
        if (! $method) {
            return 0;
        }

        $baseAmount = $this->migrationCost;

        if ($method->fee_type === 'fixed') {
            return $method->fee_flat;
        } elseif ($method->fee_type === 'percent') {
            return $baseAmount * ($method->fee_percent / 100);
        } elseif ($method->fee_type === 'mixed') {
            return ($baseAmount * ($method->fee_percent / 100)) + $method->fee_flat;
        }

        return 0;
    }

    public function getTotalProperty()
    {
        return $this->migrationCost + $this->calculatedAdminFee;
    }

    // --- ACTIONS ---

    public function updatedCategoryId()
    {
        $this->rank_id = null;
    }

    public function submitMigration()
    {
        $paymentMethod = $this->selectedPaymentMethod;

        // 1. Validasi
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'rank_id' => 'required|exists:ranks,id',
            'old_gamertag' => 'required|string|max:255',
            'new_gamertag' => 'required|string|max:255|different:old_gamertag',
            'whatsapp' => 'required|numeric|min_digits:10',
            'paymentMethodId' => 'required|exists:payment_methods,id',
            'tosAgreement' => 'accepted',
            'riskAgreement' => 'accepted',
        ];

        if ($paymentMethod && $paymentMethod->is_manual) {
            $rules['paymentProof'] = 'required|image|max:2048';
        } else {
            $rules['paymentProof'] = 'nullable';
        }

        $this->validate($rules, [
            'new_gamertag.different' => 'Gamertag baru tidak boleh sama dengan yang lama.',
            'riskAgreement.accepted' => 'Anda wajib menyetujui pernyataan risiko migrasi.',
            'paymentProof.required' => 'Wajib upload bukti transfer untuk metode manual.',
        ]);

        try {
            DB::beginTransaction();

            $paymentProofPath = ($this->paymentProof) ? $this->paymentProof->store('migration-proofs', 'public') : null;

            $migration = MigrationRequest::create([
                'uuid' => (string) Str::uuid(),
                'old_gamertag' => $this->old_gamertag,
                'new_gamertag' => $this->new_gamertag,
                'whatsapp_number' => $this->formatPhoneNumber($this->whatsapp),
                'discord_username' => $this->discord,

                'category_id' => $this->category_id,
                'rank_id' => $this->rank_id,

                'rank_price_snapshot' => $this->selectedRank->price,
                'fee_percent_snapshot' => $this->migrationFeePercent,
                'total_amount' => $this->total,
                'admin_fee' => $this->calculatedAdminFee,

                'payment_method_id' => $this->paymentMethodId,
                'payment_proof' => $paymentProofPath,

                'status' => 'pending',
                'tos_agreed_at' => now(),
            ]);

            // --- PAYMENT PROCESSING ---

            if ($paymentMethod->is_manual) {
                // MANUAL
                DB::commit();
                session()->flash('success_migration', 'Permintaan migrasi berhasil dibuat! Admin akan segera memproses.');

                return redirect()->route('migration.detail', ['uuid' => $migration->uuid]);

            } else {
                // OTOMATIS (GATEWAY)

                // Cek AriePulsa
                if ($paymentMethod->code === 'QRIS_ARIEPULSA') {
                    $ariePulsa = new AriePulsaService;
                    $response = $ariePulsa->createTransaction($migration);

                    if ($response['success']) {
                        $migration->update([
                            'payment_url' => $response['payment_url'],
                            'trx_id' => $response['trx_id'],
                            'total_amount' => $response['total_bayar'], // Update nominal unik
                        ]);
                        DB::commit();

                        return redirect()->route('migration.detail', ['uuid' => $migration->uuid]);
                    } else {
                        DB::rollBack();
                        $this->addError('paymentMethodId', 'Gagal AriePulsa: '.$response['message']);

                        return;
                    }

                } else {
                    // Tokopay
                    $tokopay = new TokopayService;
                    $response = $tokopay->createOrder($migration);

                    if ($response['success']) {
                        $migration->update([
                            'payment_url' => $response['pay_url'],
                            'trx_id' => $response['trx_id'],
                        ]);
                        DB::commit();

                        // Redirect langsung ke halaman pembayaran Tokopay
                        return redirect()->away($response['pay_url']);
                    } else {
                        DB::rollBack();
                        $this->addError('paymentMethodId', 'Gagal menghubungi Gateway: '.$response['message']);

                        return;
                    }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('whatsapp', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    private function formatPhoneNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '08')) {
            return '62'.substr($number, 1);
        }

        return $number;
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $appSettings = AppSetting::first();

        $ranks = [];
        if ($this->category_id) {
            $ranks = Rank::where('category_id', $this->category_id)
                ->where('is_active', true)
                ->orderBy('price', 'asc')
                ->get();
        }

        return view('livewire.migration-page', [
            'categories' => $categories,
            'ranks' => $ranks,
            'paymentMethods' => $paymentMethods,
            'appSettings' => $appSettings,
        ]);
    }
}
