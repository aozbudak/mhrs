<?php

namespace App\Models;

use App\Enums\RandevuDurumu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Randevu extends Model
{
    protected $table = 'randevular';

    protected $fillable = [
        'user_id',
        'doctor_id',
        'randevu_slot_id',
        'sikayet',
        'durum',
        'iptal_nedeni',
    ];

    protected function casts(): array
    {
        return [
            'durum' => RandevuDurumu::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(RandevuSlot::class, 'randevu_slot_id');
    }
}
