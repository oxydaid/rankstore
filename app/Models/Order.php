<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Agar kalau kita cari order via routing, dia baca UUID bukan ID biasa
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    // Event saat data akan dibuat
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Jika UUID kosong, buatkan otomatis
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // --- RELASI ---

    public function rank()
    {
        return $this->belongsTo(Rank::class);
    }

    // Relasi untuk mengambil data rank LAMA (jika upgrade)
    public function previousRank()
    {
        return $this->belongsTo(Rank::class, 'previous_rank_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }
}
