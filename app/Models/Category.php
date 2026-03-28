<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function ranks()
    {
        return $this->hasMany(Rank::class);
    }

    // hapus file lama ketika di update
    protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->isDirty('image')) {
                Storage::disk('public_img')->delete($model->getOriginal('image'));
            }
        });
    }
}
