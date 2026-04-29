<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

trait CreatesHospitalAdminFromOptionalForm
{
    /**
     * Kurum oluşturma formunda doldurulduysa kurum (hastane) paneli yöneticisi kullanıcısı oluşturur.
     * Tüm alanlar boşsa hiçbir şey yapmaz.
     */
    private function createHospitalAdminIfRequested(Request $request, Hospital $hospital): void
    {
        $name = trim((string) $request->input('kurum_admin_name', ''));
        $email = trim((string) $request->input('kurum_admin_email', ''));
        $pw = (string) $request->input('kurum_admin_password', '');
        $pwc = (string) $request->input('kurum_admin_password_confirmation', '');

        if ($name === '' && $email === '' && $pw === '' && $pwc === '') {
            return;
        }

        $request->merge([
            'kurum_admin_email' => strtolower($email),
        ]);

        $validated = $request->validate([
            'kurum_admin_name' => ['required', 'string', 'max:255'],
            'kurum_admin_email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')],
            'kurum_admin_password' => ['required', 'confirmed', Password::defaults()],
        ], [], [
            'kurum_admin_name' => 'kurum yöneticisi adı',
            'kurum_admin_email' => 'kurum yöneticisi e-postası',
            'kurum_admin_password' => 'kurum yöneticisi şifresi',
        ]);

        User::query()->create([
            'name' => $validated['kurum_admin_name'],
            'email' => $validated['kurum_admin_email'],
            'tc_kimlik_no' => $this->generateUniqueTcKimlik($validated['kurum_admin_email']),
            'phone' => null,
            'birth_date' => null,
            'gender' => null,
            'role' => 'hospital_admin',
            'password' => $validated['kurum_admin_password'],
            'managed_hospital_id' => $hospital->id,
        ]);
    }
}
