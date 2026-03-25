<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $tcDigits = preg_replace('/\D/', '', (string) $request->input('tc_kimlik_no', ''));
        $request->merge(['tc_kimlik_no' => $tcDigits]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'tc_kimlik_no' => ['required', 'digits:11', 'unique:'.User::class.',tc_kimlik_no'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [], [
            'tc_kimlik_no' => 'T.C. kimlik no',
        ]);

        $fullName = trim($validated['name'].' '.$validated['surname']);

        $user = User::query()->create([
            'name' => $fullName,
            'email' => $validated['email'],
            'tc_kimlik_no' => $validated['tc_kimlik_no'],
            'phone' => null,
            'birth_date' => null,
            'gender' => null,
            'role' => 'patient',
            'password' => $validated['password'],
        ]);

        event(new Registered($user));

        Auth::guard('web')->login($user);

        return redirect()->route('musteri.panel');
    }
}
