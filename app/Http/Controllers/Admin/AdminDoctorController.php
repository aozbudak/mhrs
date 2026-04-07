<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;
use App\Services\DoctorRandevuSlotGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function index(): View
    {
        $doktorlar = Doctor::query()
            ->with(['user', 'department', 'hospital'])
            ->orderByDesc('is_active')
            ->orderBy('department_id')
            ->orderBy('id')
            ->paginate(25);

        return view('admin.doktorlar.index', [
            'doktorlar' => $doktorlar,
        ]);
    }

    public function edit(Doctor $doktor): View|RedirectResponse
    {
        $doktor->loadMissing('user');
        if (! $doktor->user) {
            return redirect()
                ->route('admin.doktorlar.index')
                ->with('error', 'Bu doktor kaydına bağlı kullanıcı bulunamadı; düzenlenemez.');
        }

        $departments = Department::query()
            ->where(function ($q) use ($doktor) {
                $q->where('is_active', true)->orWhere('id', $doktor->department_id);
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();
        $hospitals = Hospital::query()
            ->where(function ($q) use ($doktor) {
                $q->where('is_active', true)->orWhere('id', $doktor->hospital_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.doktorlar.edit', [
            'doktor' => $doktor,
            'departments' => $departments,
            'hospitals' => $hospitals,
        ]);
    }

    public function update(Request $request, Doctor $doktor): RedirectResponse
    {
        $doktor->loadMissing('user');
        if (! $doktor->user) {
            return redirect()
                ->route('admin.doktorlar.index')
                ->with('error', 'Bu doktor kaydına bağlı kullanıcı bulunamadı.');
        }

        $userId = $doktor->user_id;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)->ignore($userId)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'tc_kimlik_no' => ['nullable', 'string', 'size:11', Rule::unique(User::class, 'tc_kimlik_no')->ignore($userId)],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'hospital_id' => ['required', 'integer', 'exists:hospitals,id'],
            'physical_clinic_name' => ['nullable', 'string', 'max:120'],
            'room_no' => ['nullable', 'string', 'max:32'],
            'title' => ['nullable', 'string', 'max:64'],
            'license_number' => ['nullable', 'string', 'max:64'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
            'is_aile_hekimi' => ['nullable', 'boolean'],
        ]);

        $tc = $validated['tc_kimlik_no'] ?? null;
        if (! $tc) {
            $tc = $doktor->user->tc_kimlik_no ?? $this->generateUniqueTcKimlik($validated['email']);
        }

        $previousHospitalId = $doktor->hospital_id;

        DB::transaction(function () use ($validated, $tc, $request, $doktor) {
            $userData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'tc_kimlik_no' => $tc,
                'phone' => $validated['phone'] ?? null,
            ];
            if ($request->filled('password')) {
                $userData['password'] = $validated['password'];
            }
            $doktor->user->update($userData);

            $doktor->update([
                'department_id' => $validated['department_id'],
                'hospital_id' => $validated['hospital_id'],
                'physical_clinic_name' => $validated['physical_clinic_name'] ?? null,
                'room_no' => $validated['room_no'] ?? null,
                'title' => $validated['title'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'is_active' => $request->boolean('is_active', true),
                'is_aile_hekimi' => $request->boolean('is_aile_hekimi'),
            ]);
        });

        $doktor->refresh();
        $generator = app(DoctorRandevuSlotGenerator::class);
        if ((int) $previousHospitalId !== (int) $doktor->hospital_id) {
            if ($previousHospitalId) {
                $generator->resyncFutureSlotsForHospital((int) $previousHospitalId);
            }
            if ($doktor->hospital_id) {
                $generator->resyncFutureSlotsForHospital((int) $doktor->hospital_id);
            }
        }

        return redirect()
            ->route('admin.doktorlar.index')
            ->with('success', 'Doktor bilgileri güncellendi.');
    }

    public function destroy(Doctor $doktor): RedirectResponse
    {
        $doktor->loadMissing('user');

        DB::transaction(function () use ($doktor) {
            $user = $doktor->user;
            $doktor->delete();
            if ($user) {
                $user->delete();
            }
        });

        return redirect()
            ->route('admin.doktorlar.index')
            ->with('success', 'Doktor kaydı silindi.');
    }

    private function generateUniqueTcKimlik(string $email): string
    {
        $email = strtolower(trim($email));
        $base = abs(crc32($email.'admin-a'));
        $checksum = abs(crc32($email.'admin-b'));

        for ($i = 0; $i < 5000; $i++) {
            $body10 = str_pad((string) ((($base + $i) % 10000000000)), 10, '0', STR_PAD_LEFT);
            $last = (string) ((($checksum + $i) % 9) + 1);
            $candidate = $body10.$last;

            if (! User::query()->where('tc_kimlik_no', $candidate)->exists()) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Benzersiz T.C. kimlik numarası üretilemedi.');
    }
}
