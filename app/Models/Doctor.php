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
        'hospital_id',
        'physical_clinic_name',
        'room_no',
        'title',
        'license_number',
        'bio',
        'is_active',
        'is_aile_hekimi',
        'ameliyat_baslangic_saati',
        'ameliyat_bitis_saati',
        'ameliyat_tarihi',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_aile_hekimi' => 'boolean',
            'ameliyat_tarihi' => 'date',
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

    public function hospital(): BelongsTo
    {
        return $this->belongsTo(Hospital::class);
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

    public function actionHistories(): HasMany
    {
        return $this->hasMany(DoctorActionHistory::class)->orderByDesc('created_at');
    }
}
