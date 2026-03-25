<?php

namespace App\Models;

use App\Enums\GunlukDegisimTur;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GunlukDegisim extends Model
{
    protected $table = 'gunluk_degisimler';

    protected $fillable = [
        'doctor_id',
        'tarih',
        'tur',
        'aciklama',
    ];

    protected function casts(): array
    {
        return [
            'tarih' => 'date',
            'tur' => GunlukDegisimTur::class,
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function saatler(): HasMany
    {
        return $this->hasMany(GunlukDegisimSaati::class, 'gunluk_degisim_id')->orderBy('sort_order');
    }

    public function randevuSlotlari(): HasMany
    {
        return $this->hasMany(RandevuSlot::class, 'gunluk_degisim_id');
    }
}
