<?php

namespace App\Models;

use App\Enums\RandevuDurumu;
use App\Enums\RandevuKatilimDurumu;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Randevu extends Model
{
    protected $table = 'randevular';

    protected $fillable = [
        'user_id',
        'booked_by_user_id',
        'doctor_id',
        'randevu_slot_id',
        'sikayet',
        'gizli',
        'durum',
        'katilim_durumu',
        'hatirlatma_bildirildi_at',
        'katilim_bildirimi_at',
        'iptal_nedeni',
    ];

    protected function casts(): array
    {
        return [
            'durum' => RandevuDurumu::class,
            'katilim_durumu' => RandevuKatilimDurumu::class,
            'gizli' => 'boolean',
            'hatirlatma_bildirildi_at' => 'datetime',
            'katilim_bildirimi_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
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
