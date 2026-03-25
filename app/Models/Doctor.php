<?php

namespace App\Models;

use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'department_id',
        'title',
        'license_number',
        'bio',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(DoctorWorkingHour::class)->orderBy('weekday')->orderBy('sort_order');
    }

    public function gunlukDegisimler(): HasMany
    {
        return $this->hasMany(GunlukDegisim::class)->orderBy('tarih');
    }

    public function randevuSlotlari(): HasMany
    {
        return $this->hasMany(RandevuSlot::class)->orderBy('baslangic');
    }

    public function randevular(): HasMany
    {
        return $this->hasMany(Randevu::class)->orderByDesc('created_at');
    }
}
