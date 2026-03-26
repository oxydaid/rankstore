<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Helper untuk mengecek validitas kode
    public function isValid()
    {
        // 1. Cek Aktif
        if (! $this->is_active) {
            return false;
        }

        // 2. Cek Tanggal (Jika diisi)
        $now = now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        } // Belum mulai
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        } // Sudah lewat

        // 3. Cek Kuota (Jika diisi)
        if (! is_null($this->max_uses) && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
