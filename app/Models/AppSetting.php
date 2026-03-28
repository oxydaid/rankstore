<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'social_media' => 'array', // Wajib agar KeyValue berfungsi
        'license_verified_payload' => 'array',
        'license_last_checked_at' => 'datetime',
        'license_expires_at' => 'datetime',
    ];

    // Hapus file lama jika di update
    protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->isDirty('logo')) {
                $oldLogo = $model->getOriginal('logo');
                if ($oldLogo && Storage::disk('public_img')->exists($oldLogo)) {
                    Storage::disk('public_img')->delete($oldLogo);
                }
            }

            if ($model->isDirty('qr_code')) {
                $oldQrCode = $model->getOriginal('qr_code');
                if ($oldQrCode && Storage::disk('public_img')->exists($oldQrCode)) {
                    Storage::disk('public_img')->delete($oldQrCode);
                }
            }

            //favicon
            if ($model->isDirty('favicon')) {
                $oldFavicon = $model->getOriginal('favicon');
                if ($oldFavicon && Storage::disk('public_img')->exists($oldFavicon)) {
                    Storage::disk('public_img')->delete($oldFavicon);
                }
            }
        });
    }
}
