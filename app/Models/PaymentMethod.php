<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Hapus file lama saat update logo
    protected static function booted()
    {
        static::updating(function ($paymentMethod) {
            if ($paymentMethod->isDirty('logo')) {
                $oldLogo = $paymentMethod->getOriginal('logo');
                if ($oldLogo && \Storage::disk('public_img')->exists($oldLogo)) {
                    \Storage::disk('public_img')->delete($oldLogo);
                }
            }
        });
    }
}
