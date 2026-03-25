<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    protected $fillable = [
        'name',
        'username',
        'tc_kimlik_no',
        'email',
        'phone',
        'birth_date',
        'gender',
        'role',
        'password',
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
            'birth_date' => 'date',
            'password' => 'hashed',
        ];
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function randevular(): HasMany
    {
        return $this->hasMany(Randevu::class)->orderByDesc('created_at');
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
}
