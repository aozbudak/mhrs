<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $loginAs = $request->input('login_as', 'patient');
        if (! in_array($loginAs, ['patient', 'admin'], true)) {
            $loginAs = 'patient';
        }

        $password = (string) $request->input('password', '');

        if ($loginAs === 'admin') {
            $data = $request->validate([
                'login_as' => ['required', 'in:admin'],
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);

            /** @var User|null $user */
            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [strtolower($data['email'])])
                ->first();

            if (! $user || ! $user->isAdmin() || ! Hash::check($password, $user->getAuthPassword())) {
                throw ValidationException::withMessages([
                    'email' => __('Yönetici giriş bilgileri eşleşmiyor.'),
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

        $request->session()->regenerate();

        if ($user->isAdmin()) {
            Auth::guard('admin')->login($user, $request->boolean('remember'));

            return redirect()->intended(route('admin.panel'));
        }

        if ($user->isPatient()) {
            Auth::guard('web')->login($user, $request->boolean('remember'));

            return redirect()->intended(route('musteri.panel'));
        }

        return redirect()->intended('/');
    }

    public function destroy(Request $request): RedirectResponse
    {
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
}
