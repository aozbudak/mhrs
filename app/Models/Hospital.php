<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hospital extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'districts',
        'address',
        'latitude',
        'longitude',
        'phone',
        'randevu_slot_dakika',
        'is_active',
        'is_saglik_merkezi',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_saglik_merkezi' => 'boolean',
            'districts' => 'array',
            'latitude' => 'float',
            'longitude' => 'float',
            'randevu_slot_dakika' => 'integer',
        ];
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(HospitalWorkingHour::class)->orderBy('weekday')->orderBy('sort_order');
    }

    public function departmentWorkingHours(): HasMany
    {
        return $this->hasMany(HospitalDepartmentWorkingHour::class)
            ->orderBy('department_id')
            ->orderBy('weekday')
            ->orderBy('sort_order');
    }

    public function departmentSettings(): HasMany
    {
        return $this->hasMany(HospitalDepartmentSetting::class)->orderBy('department_id');
    }

    /** @return HasMany<User, $this> */
    public function managedHospitalAdmins(): HasMany
    {
        return $this->hasMany(User::class, 'managed_hospital_id')
            ->whereRaw('LOWER(TRIM(role)) = ?', ['hospital_admin'])
            ->orderBy('name');
    }

    /** @return HasMany<User, $this> */
    public function managedDepartmentHeads(): HasMany
    {
        return $this->hasMany(User::class, 'managed_hospital_id')
            ->whereRaw('LOWER(TRIM(role)) = ?', ['department_head'])
            ->orderBy('name');
    }
}
