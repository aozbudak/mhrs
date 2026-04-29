<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $loginAs = strtolower(trim((string) $request->input('login_as', 'patient')));
        if (! in_array($loginAs, ['patient', 'admin', 'hospital_admin'], true)) {
            $loginAs = 'patient';
        }

        $password = (string) $request->input('password', '');

        if ($loginAs === 'admin') {
            $data = $request->validate([
                'login_as' => ['required', 'in:admin'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string'],
            ]);

            $emailNorm = strtolower(trim($data['email']));

            /** @var User|null $user */
            $user = User::query()
                ->whereRaw('LOWER(TRIM(email)) = ?', [$emailNorm])
                ->first();

            if (! $user || ! $user->isAdmin() || ! Hash::check($password, $user->getAuthPassword())) {
                throw ValidationException::withMessages([
                    'email' => __('Yönetici giriş bilgileri eşleşmiyor.'),
                ]);
            }
        } elseif ($loginAs === 'hospital_admin') {
            $data = $request->validate([
                'login_as' => ['required', 'in:hospital_admin'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'string'],
            ]);

            $emailNorm = strtolower(trim($data['email']));

            /** @var User|null $user */
            $user = User::query()
                ->with('managedHospital')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$emailNorm])
                ->first();

            $managed = $user?->managedHospital;
            $hasKurum = $user && ((int) $user->managed_hospital_id) > 0 && $managed instanceof Hospital;

            $isKurumKullanici = $user && ($user->isHospitalAdmin() || $user->isDepartmentHead());
            $hasDepartment = $user && (! $user->isDepartmentHead() || ((int) $user->managed_department_id) > 0);

            if (! $user || ! $isKurumKullanici || ! $hasKurum || ! $hasDepartment || ! Hash::check($password, $user->getAuthPassword())) {
                throw ValidationException::withMessages([
                    'email' => __('Kurum paneli giriş bilgileri eşleşmiyor veya hesaba kurum atanmamış.'),
                ]);
            }
        } else {
            $tcDigits = preg_replace('/\D/', '', (string) $request->input('tc_kimlik_no', ''));
            $request->merge(['tc_kimlik_no' => $tcDigits]);

            $request->validate([
                'login_as' => ['required', 'in:patient'],
                'tc_kimlik_no' => ['required', 'digits:11'],
                'password' => ['required', 'string'],
            ], [], [
                'tc_kimlik_no' => 'T.C. kimlik no',
            ]);

            /** @var User|null $user */
            $user = User::query()
                ->where('tc_kimlik_no', $tcDigits)
                ->first();

            if (! $user || ! $user->isPatient() || ! Hash::check($password, $user->getAuthPassword())) {
                throw ValidationException::withMessages([
                    'tc_kimlik_no' => __('Hasta giriş bilgileri eşleşmiyor.'),
                ]);
            }
        }

        Auth::guard('patient')->logout();
        Auth::guard('admin')->logout();
        Auth::guard('hospital')->logout();
        Auth::guard('web')->logout();

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            Auth::guard('admin')->login($user, $request->boolean('remember'));

            return redirect()->intended(route('admin.panel'));
        }

        if ($user->isHospitalAdmin() || $user->isDepartmentHead()) {
            Auth::guard('hospital')->login($user, $request->boolean('remember'));

            if ($user->isDepartmentHead()) {
                return redirect()->intended(route('bolum-baskanligi.panel'));
            }

            $user->loadMissing('managedHospital');
            $mh = $user->managedHospital;
            $panelUrl = ($mh && $mh->is_saglik_merkezi)
                ? route('saglik-merkezi.panel')
                : route('hastane.panel');

            return redirect()->intended($panelUrl);
        }

        if ($user->isPatient()) {
            Auth::guard('patient')->login($user, $request->boolean('remember'));

            return redirect()->intended(route('musteri.panel'));
        }

        return redirect()->intended('/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('patient')->logout();
        Auth::guard('web')->logout();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function destroyAdmin(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function destroyHospital(Request $request): RedirectResponse
    {
        Auth::guard('hospital')->logout();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
