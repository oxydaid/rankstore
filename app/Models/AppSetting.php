<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $guarded = [];

    protected $casts = [
        'social_media' => 'array', // Wajib agar KeyValue berfungsi
        'license_verified_payload' => 'array',
        'license_last_checked_at' => 'datetime',
        'license_expires_at' => 'datetime',
    ];
}
