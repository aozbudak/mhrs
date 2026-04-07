<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;
use App\Services\AileHekimiOneriService;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        $cities = Hospital::query()
            ->where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        return view('auth.register', compact('cities'));
    }

    public function districtsForCity(Request $request): JsonResponse
    {
        $city = $request->string('city')->trim()->value();
        if ($city === '') {
            return response()->json(['districts' => []]);
        }

        $exists = Hospital::query()->where('is_active', true)->where('city', $city)->exists();
        if (! $exists) {
            return response()->json(['message' => 'Geçersiz il seçimi.'], 422);
        }

        $districts = Hospital::query()
            ->where('is_active', true)
            ->where('city', $city)
            ->get()
            ->flatMap(fn (Hospital $h) => collect($h->districts ?? []))
            ->map(fn ($d) => trim((string) $d))
            ->filter()
            ->unique()
            ->sort()
            ->values();

        return response()->json(['districts' => $districts->all()]);
    }

    public function aileHekimleriJson(Request $request, AileHekimiOneriService $aileHekimiOneriService): JsonResponse
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
        ]);

        $city = trim($validated['city']);
        $district = isset($validated['district']) ? trim((string) $validated['district']) : '';

        $exists = Hospital::query()->where('is_active', true)->where('city', $city)->exists();
        if (! $exists) {
            return response()->json(['message' => 'Geçersiz il.'], 422);
        }

        $doctors = $aileHekimiOneriService->listNearest($city, $district !== '' ? $district : null);

        return response()->json(['doctors' => $doctors]);
    }

    public function store(Request $request, AileHekimiOneriService $aileHekimiOneriService): RedirectResponse
    {
        $tcDigits = preg_replace('/\D/', '', (string) $request->input('tc_kimlik_no', ''));
        $request->merge(['tc_kimlik_no' => $tcDigits]);
        $veliDigits = preg_replace('/\D/', '', (string) $request->input('veli_tc_kimlik_no', ''));
        $request->merge(['veli_tc_kimlik_no' => $veliDigits !== '' ? $veliDigits : null]);

        $secAile = $request->boolean('sec_aile_hekimi');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'tc_kimlik_no' => ['required', 'digits:11', 'unique:'.User::class.',tc_kimlik_no'],
            'birth_date' => ['required', 'date', 'before_or_equal:today'],
            'engelli' => ['required', Rule::in(['0', '1'])],
            'veli_tc_kimlik_no' => ['nullable', 'digits:11', 'different:tc_kimlik_no'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'patient_city' => [
                Rule::requiredIf($secAile),
                'nullable',
                'string',
                'max:100',
                Rule::exists('hospitals', 'city')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'patient_district' => ['nullable', 'string', 'max:100'],
            'aile_hekimi_doctor_id' => [
                Rule::requiredIf($secAile),
                'nullable',
                'integer',
                Rule::exists('doctors', 'id')->where(fn ($q) => $q->where('is_aile_hekimi', true)->where('is_active', true)),
            ],
        ], [], [
            'tc_kimlik_no' => 'T.C. kimlik no',
            'birth_date' => 'doğum tarihi',
            'engelli' => 'engel durumu',
            'veli_tc_kimlik_no' => 'veli T.C. kimlik no',
            'patient_city' => 'il',
            'patient_district' => 'ilçe',
            'aile_hekimi_doctor_id' => 'aile hekimi',
        ]);

        $needsVeliTc = false;
        $veliTcToSave = null;
        if (! empty($validated['birth_date'])) {
            $bd = Carbon::parse($validated['birth_date'])->startOfDay();
            $needsVeliTc = $bd->isAfter(now()->copy()->subYears(18));
        }
        if ($needsVeliTc) {
            if (empty($validated['veli_tc_kimlik_no'])) {
                throw ValidationException::withMessages([
                    'veli_tc_kimlik_no' => '18 yaşından küçük hastalar için veli veya vasi T.C. kimlik numarası zorunludur (nüfus kaydıyla uyumlu).',
                ]);
            }
            $veliUser = User::query()->where('tc_kimlik_no', $validated['veli_tc_kimlik_no'])->first();
            if (! $veliUser || ! $veliUser->isPatient()) {
                throw ValidationException::withMessages([
                    'veli_tc_kimlik_no' => 'Veli olarak yalnızca sistemde kayıtlı bir hasta hesabı seçilebilir.',
                ]);
            }
            if ($veliUser->isUnderEighteen()) {
                throw ValidationException::withMessages([
                    'veli_tc_kimlik_no' => 'Veli reşit olmalıdır (18 yaş ve üzeri).',
                ]);
            }
            $veliTcToSave = $validated['veli_tc_kimlik_no'];
        }

        $resolvedDistrict = '';

        if ($secAile) {
            $city = trim((string) $validated['patient_city']);
            $district = trim((string) ($validated['patient_district'] ?? ''));

            $allowedDistricts = Hospital::query()
                ->where('is_active', true)
                ->where('city', $city)
                ->get()
                ->flatMap(fn (Hospital $h) => collect($h->districts ?? []))
                ->map(fn ($d) => trim((string) $d))
                ->filter()
                ->unique();

            if ($allowedDistricts->isNotEmpty()) {
                if ($district === '' || ! $allowedDistricts->contains($district)) {
                    throw ValidationException::withMessages([
                        'patient_district' => 'Seçilen il için geçerli bir ilçe seçin.',
                    ]);
                }
                $resolvedDistrict = $district;
            }

            $doctor = Doctor::query()->find($validated['aile_hekimi_doctor_id']);
            if (! $doctor || ! $aileHekimiOneriService->doctorMatchesPatientArea($doctor, $city, $resolvedDistrict !== '' ? $resolvedDistrict : null)) {
                throw ValidationException::withMessages([
                    'aile_hekimi_doctor_id' => 'Seçilen aile hekimi, girdiğiniz il ve ilçe ile uyumlu olmalıdır.',
                ]);
            }
        }

        $fullName = trim($validated['name'].' '.$validated['surname']);

        $user = User::query()->create([
            'name' => $fullName,
            'email' => $validated['email'],
            'tc_kimlik_no' => $validated['tc_kimlik_no'],
            'veli_tc_kimlik_no' => $veliTcToSave,
            'phone' => null,
            'birth_date' => $validated['birth_date'],
            'gender' => null,
            'engelli' => $validated['engelli'] === '1',
            'role' => 'patient',
            'password' => $validated['password'],
            'patient_city' => $secAile ? trim((string) $validated['patient_city']) : null,
            'patient_district' => $secAile ? ($resolvedDistrict !== '' ? $resolvedDistrict : null) : null,
            'aile_hekimi_doctor_id' => $secAile ? (int) $validated['aile_hekimi_doctor_id'] : null,
        ]);

        event(new Registered($user));

        Auth::guard('patient')->login($user);

        return redirect()->route('musteri.panel');
    }
}
