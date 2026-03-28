<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Rank extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'description' => 'array',
        'kits' => 'array',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // hapus file lama ketika di update
    protected static function booted()
    {
        static::updated(function ($model) {
            if ($model->isDirty('image')) {
                $oldImage = $model->getOriginal('image');
                if ($oldImage && Storage::disk('public_img')->exists($oldImage)) {
                    Storage::disk('public_img')->delete($oldImage);
                }
            }
        });
    }

    

}
