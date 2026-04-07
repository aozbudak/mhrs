<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const ONCELIKLI_YAS_ESIGI = 65;

    protected $fillable = [
        'name',
        'username',
        'tc_kimlik_no',
        'veli_tc_kimlik_no',
        'email',
        'phone',
        'birth_date',
        'gender',
        'engelli',
        'role',
        'password',
        'patient_city',
        'patient_district',
        'aile_hekimi_doctor_id',
        'last_login_at',
        'last_logout_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
            'birth_date' => 'date',
            'engelli' => 'boolean',
            'password' => 'hashed',
            'patient_favorites' => 'array',
        ];
    }

    /** @return array{hospitals: list<int>, clinics: list<array{hospital_id: int, department_id: int}>} */
    public function patientFavoritesNormalized(): array
    {
        $raw = $this->patient_favorites;
        if (! is_array($raw)) {
            return ['hospitals' => [], 'clinics' => []];
        }

        $hospitalIds = [];
        foreach ($raw['hospitals'] ?? [] as $id) {
            $id = (int) $id;
            if ($id > 0) {
                $hospitalIds[] = $id;
            }
        }
        $hospitalIds = array_values(array_unique($hospitalIds));

        $clinics = [];
        foreach ($raw['clinics'] ?? [] as $row) {
            if (! is_array($row)) {
                continue;
            }
            $hid = (int) ($row['hospital_id'] ?? 0);
            $did = (int) ($row['department_id'] ?? 0);
            if ($hid > 0 && $did > 0) {
                $clinics[] = ['hospital_id' => $hid, 'department_id' => $did];
            }
        }

        $seen = [];
        $clinicsUnique = [];
        foreach ($clinics as $c) {
            $k = $c['hospital_id'].':'.$c['department_id'];
            if (isset($seen[$k])) {
                continue;
            }
            $seen[$k] = true;
            $clinicsUnique[] = $c;
        }

        return ['hospitals' => $hospitalIds, 'clinics' => $clinicsUnique];
    }

    public function isFavoriteHospital(int $hospitalId): bool
    {
        return in_array($hospitalId, $this->patientFavoritesNormalized()['hospitals'], true);
    }

    public function isFavoriteClinic(int $hospitalId, int $departmentId): bool
    {
        foreach ($this->patientFavoritesNormalized()['clinics'] as $c) {
            if ($c['hospital_id'] === $hospitalId && $c['department_id'] === $departmentId) {
                return true;
            }
        }

        return false;
    }

    public function toggleFavoriteHospital(int $hospitalId): void
    {
        $norm = $this->patientFavoritesNormalized();
        $ids = $norm['hospitals'];
        if (in_array($hospitalId, $ids, true)) {
            $ids = array_values(array_diff($ids, [$hospitalId]));
        } else {
            $ids[] = $hospitalId;
            $ids = array_values(array_unique($ids));
        }
        $this->patient_favorites = [
            'hospitals' => $ids,
            'clinics' => $norm['clinics'],
        ];
        $this->save();
    }

    public function toggleFavoriteClinic(int $hospitalId, int $departmentId): void
    {
        $norm = $this->patientFavoritesNormalized();
        $clinics = $norm['clinics'];
        $found = false;
        $next = [];
        foreach ($clinics as $c) {
            if ($c['hospital_id'] === $hospitalId && $c['department_id'] === $departmentId) {
                $found = true;

                continue;
            }
            $next[] = $c;
        }
        if (! $found) {
            $next[] = ['hospital_id' => $hospitalId, 'department_id' => $departmentId];
        }
        $this->patient_favorites = [
            'hospitals' => $norm['hospitals'],
            'clinics' => $next,
        ];
        $this->save();
    }

    /** 65 yaş üstü veya engelli kayıtlı hastalar öncelikli kabul edilir. */
    public function isOncelikliHasta(): bool
    {
        if ($this->engelli) {
            return true;
        }

        if ($this->birth_date === null) {
            return false;
        }

        return $this->birth_date->diffInYears(now()) >= self::ONCELIKLI_YAS_ESIGI;
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function aileHekimi(): BelongsTo
    {
        return $this->belongsTo(Doctor::class, 'aile_hekimi_doctor_id');
    }

    public function randevular(): HasMany
    {
        return $this->hasMany(Randevu::class)->orderByDesc('created_at');
    }

    /**
     * T.C. ile manuel eklenen, adına randevu alınabilen hastalar.
     *
     * @return BelongsToMany<User, $this>
     */
    public function proxyPatients(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'patient_proxy_links',
            'proxy_user_id',
            'patient_user_id'
        )->withPivot([
            'kimlik_tc_kimlik_no',
            'kimlik_seri_no',
            'kimlik_dogum_tarihi',
            'kimlik_cinsiyet',
        ])->withTimestamps();
    }

    /**
     * Otomatik (nüfus) + manuel vekalet hastaları, tekrarsız.
     *
     * @return Collection<int, User>
     */
    public function tumVekaletHastalari(): Collection
    {
        return $this->nufusKaynakliCocukHastalar()
            ->concat($this->proxyPatients()->orderBy('name')->get())
            ->unique('id')
            ->values();
    }

    /** Doğum tarihi tanımlı ve 18 yaşın altında mı? */
    public function isUnderEighteen(): bool
    {
        if ($this->birth_date === null) {
            return false;
        }

        return $this->birth_date->isAfter(now()->copy()->subYears(18));
    }

    /**
     * Bu kullanıcı, verilen hastanın nüfus kayıtlarındaki veli/vasisi mi? (18 yaş altı + veli T.C. eşleşmesi.)
     */
    public function isNufusKaynakliVekilFor(User $patient): bool
    {
        if (! $patient->isPatient() || ! $patient->isUnderEighteen()) {
            return false;
        }

        $veli = $patient->veli_tc_kimlik_no;
        if ($veli === null || $veli === '' || strlen((string) $veli) !== 11) {
            return false;
        }

        return (string) $veli === (string) $this->tc_kimlik_no;
    }

    /**
     * Veli T.C. kimlik numarası bu kullanıcıya eşleşen, 18 yaş altı hasta hesapları (nüfus / MERNİS verisi simülasyonu).
     *
     * @return Collection<int, User>
     */
    public function nufusKaynakliCocukHastalar(): Collection
    {
        return self::query()
            ->where('veli_tc_kimlik_no', $this->tc_kimlik_no)
            ->whereKeyNot($this->id)
            ->whereNotNull('birth_date')
            ->where('birth_date', '>', now()->subYears(18))
            ->whereRaw('LOWER(TRIM(role)) = ?', ['patient'])
            ->orderBy('name')
            ->get();
    }

    /** Normalize edilmiş rol (boşluk / büyük-küçük harf farklarını tolere eder). */
    public function normalizedRole(): string
    {
        return strtolower(trim((string) $this->role));
    }

    public function isAdmin(): bool
    {
        return $this->normalizedRole() === 'admin';
    }

    public function isPatient(): bool
    {
        return $this->normalizedRole() === 'patient';
    }

    public function isDoctor(): bool
    {
        return $this->normalizedRole() === 'doctor';
    }
}
