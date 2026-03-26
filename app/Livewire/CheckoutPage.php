<?php

namespace App\Livewire;

use App\Models\AppSetting;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PromoCode;
use App\Models\Rank;
use App\Services\AriePulsaService;
use App\Services\TokopayService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CheckoutPage extends Component
{
    use WithFileUploads;

    public Rank $rank;

    public $gamertag;

    public $whatsapp;

    public $discord;

    public $paymentMethodId;

    public $notes;

    public $tosAgreement = false;

    public $paymentProof;

    public $isUpgrade = false;

    public $previousRankId = null;

    public $upgradeProof;

    public $promoCodeInput = '';

    public $appliedPromoCodeId = null;

    public $promoError = null;

    public $gatewayPaymentUrl = null;

    public function mount(Rank $rank)
    {
        $this->rank = $rank;
        if (! $this->rank->is_active) {
            return redirect()->route('shop');
        }
    }

    public function getSelectedPaymentMethodProperty()
    {
        if (! $this->paymentMethodId) {
            return null;
        }

        return PaymentMethod::find($this->paymentMethodId);
    }

    public function getIsQrisProperty()
    {
        $method = $this->selectedPaymentMethod;
        if (! $method) {
            return false;
        }

        return Str::contains(strtoupper($method->name), 'QRIS') ||
            Str::contains(strtoupper($method->code), 'QRIS');
    }

    public function getSubtotalProperty()
    {
        $price = $this->rank->price;

        if ($this->isUpgrade && $this->previousRankId) {
            $prevRank = Rank::find($this->previousRankId);
            if ($prevRank) {
                $price = $price - $prevRank->price;
            }
        }

        return max(0, $price);
    }

    public function getDiscountAmountProperty()
    {
        if (! $this->appliedPromoCodeId) {
            return 0;
        }

        $promo = PromoCode::find($this->appliedPromoCodeId);
        if (! $promo || ! $promo->isValid()) {
            return 0;
        }

        $subtotal = $this->subtotal;

        if ($promo->type === 'fixed') {
            return min($promo->amount, $subtotal);
        } elseif ($promo->type === 'percent') {
            return $subtotal * ($promo->amount / 100);
        }

        return 0;
    }

    public function getCalculatedAdminFeeProperty()
    {
        $method = $this->selectedPaymentMethod;
        if (! $method) {
            return 0;
        }

        $baseAmount = max(0, $this->subtotal - $this->discountAmount);

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
        return ($this->subtotal - $this->discountAmount) + $this->calculatedAdminFee;
    }

    public function applyPromo()
    {
        $this->reset('promoError');

        if (empty($this->promoCodeInput)) {
            $this->promoError = 'Masukkan kode promo.';

            return;
        }

        $promo = PromoCode::where('code', $this->promoCodeInput)->first();

        if (! $promo) {
            $this->promoError = 'Kode tidak ditemukan.';

            return;
        }

        if (! $promo->isValid()) {
            $this->promoError = 'Kode tidak valid/kadaluarsa.';

            return;
        }

        $this->appliedPromoCodeId = $promo->id;
        session()->flash('promo_success', 'Kode berhasil digunakan!');
    }

    public function removePromo()
    {
        $this->appliedPromoCodeId = null;
        $this->promoCodeInput = '';
    }

    public function submitOrder()
    {

        if ($this->gatewayPaymentUrl) {
            return;
        }

        $paymentMethod = $this->selectedPaymentMethod;

        $rules = [
            'gamertag' => 'required|string|max:255',
            'whatsapp' => 'required|numeric|min_digits:10',
            'paymentMethodId' => 'required|exists:payment_methods,id',
            'tosAgreement' => 'accepted',
            'previousRankId' => $this->isUpgrade ? 'required' : 'nullable',
            'upgradeProof' => $this->isUpgrade ? 'required|image|max:2048' : 'nullable',
        ];

        if ($paymentMethod && $paymentMethod->is_manual) {
            $rules['paymentProof'] = 'required|image|max:2048';
        } else {
            $rules['paymentProof'] = 'nullable';
        }

        $this->validate($rules, [
            'whatsapp.numeric' => 'Nomor WhatsApp harus berupa angka.',
            'whatsapp.min_digits' => 'Nomor WhatsApp minimal 10 digit.',
            'tosAgreement.accepted' => 'Anda harus menyetujui Syarat & Ketentuan sebelum melanjutkan.',
            'previousRankId.required' => 'Pilih rank sebelumnya untuk upgrade.',
            'upgradeProof.required' => 'Wajib upload bukti kepemilikan rank sebelumnya untuk upgrade.',
            'paymentProof.required' => 'Wajib upload bukti transfer untuk metode pembayaran manual.',
            'paymentMethodId.required' => 'Pilih metode pembayaran.',
        ]);

        try {
            DB::beginTransaction();

            $paymentProofPath = ($this->paymentProof) ? $this->paymentProof->store('payment-proofs', 'public') : null;
            $upgradeProofPath = ($this->isUpgrade && $this->upgradeProof) ? $this->upgradeProof->store('upgrade-proofs', 'public') : null;

            $order = Order::create([
                'uuid' => (string) Str::uuid(),
                'gamertag' => $this->gamertag,
                'whatsapp_number' => $this->formatPhoneNumber($this->whatsapp),
                'discord_username' => $this->discord,
                'rank_id' => $this->rank->id,
                'payment_method_id' => $this->paymentMethodId,

                'total_amount' => $this->total,
                'subtotal_amount' => $this->subtotal,
                'discount_amount' => $this->discountAmount,
                'admin_fee' => $this->calculatedAdminFee,
                'promo_code_id' => $this->appliedPromoCodeId,

                'status' => 'pending',
                'payment_proof' => $paymentProofPath,
                'is_upgrade' => $this->isUpgrade,
                'previous_rank_id' => $this->isUpgrade ? $this->previousRankId : null,
                'upgrade_proof' => $upgradeProofPath,
                'notes' => $this->notes,
                'tos_agreed_at' => now(),
            ]);

            if ($this->appliedPromoCodeId) {
                PromoCode::where('id', $this->appliedPromoCodeId)->increment('used_count');
            }

            if ($paymentMethod->is_manual) {

                DB::commit();
                session()->flash('success_order', 'Pesanan manual berhasil dibuat! Admin akan segera memverifikasi.');

                return redirect()->route('tracking.detail', ['uuid' => $order->uuid]);

            } else {

                if ($paymentMethod->code === 'QRIS_ARIEPULSA') {

                    $ariePulsa = new AriePulsaService;
                    $response = $ariePulsa->createTransaction($order);

                    if ($response['success']) {

                        $order->update([
                            'payment_url' => $response['payment_url'],

                            'trx_id' => $response['trx_id'],

                            'total_amount' => $response['total_bayar'],
                        ]);

                        DB::commit();

                        session()->flash('success_order', 'QRIS berhasil dibuat. Silakan scan!');

                        return redirect()->route('tracking.detail', ['uuid' => $order->uuid]);

                    } else {
                        DB::rollBack();
                        $this->addError('paymentMethodId', 'Gagal AriePulsa: '.$response['message']);

                        return;
                    }

                } else {

                    $tokopay = new TokopayService;
                    $response = $tokopay->createOrder($order);

                    if ($response['success']) {
                        $order->update([
                            'payment_url' => $response['pay_url'],
                            'trx_id' => $response['trx_id'],
                        ]);

                        DB::commit();

                        return redirect()->away($response['pay_url']);
                    } else {
                        DB::rollBack();
                        $this->addError('paymentMethodId', 'Gagal Tokopay: '.$response['message']);

                        return;
                    }
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('gamertag', 'Terjadi kesalahan sistem: '.$e->getMessage());
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
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        $appSettings = AppSetting::first();

        $previousRanks = Rank::where('category_id', $this->rank->category_id)
            ->where('id', '!=', $this->rank->id)
            ->where('price', '<', $this->rank->price)
            ->where('is_active', true)
            ->orderBy('price', 'desc')
            ->get();

        return view('livewire.checkout-page', [
            'paymentMethods' => $paymentMethods,
            'previousRanks' => $previousRanks,
            'appSettings' => $appSettings,
        ]);
    }
}
