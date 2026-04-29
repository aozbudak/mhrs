<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\BuildsPoliklinikSaatFormBlocks;
use App\Http\Controllers\Concerns\CreatesHospitalAdminFromOptionalForm;
use App\Http\Controllers\Concerns\HandlesHospitalDistrictInput;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\User;
use App\Services\DoctorRandevuSlotGenerator;
use App\Services\HospitalDepartmentSettingService;
use App\Services\HospitalDepartmentWorkingHoursService;
use App\Services\HospitalWorkingHoursService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminHospitalController extends Controller
{
    use BuildsPoliklinikSaatFormBlocks;
    use CreatesHospitalAdminFromOptionalForm;
    use HandlesHospitalDistrictInput;

    public function __construct(
        private readonly HospitalWorkingHoursService $workingHoursService,
        private readonly HospitalDepartmentWorkingHoursService $departmentWorkingHoursService,
        private readonly HospitalDepartmentSettingService $departmentSettingService,
    ) {}

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        $filterCity = isset($validated['city']) && $validated['city'] !== ''
            ? $validated['city']
            : null;

        $iller = Hospital::query()
            ->where('is_saglik_merkezi', false)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        $hastaneler = Hospital::query()
            ->where('is_saglik_merkezi', false)
            ->when($filterCity, fn ($q) => $q->where('city', $filterCity))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.hastaneler.index', [
            'hastaneler' => $hastaneler,
            'iller' => $iller,
            'filterCity' => $filterCity,
        ]);
    }

    public function create(): View
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.hastaneler.create', [
            'departments' => $departments,
            'doctorRows' => $this->doctorRowsFromOld(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $districts = $this->validatedDistrictsFromInput($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:5000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $doctorRows = $this->validatedDoctorRowsFromRequest($request, uniqueEmail: true);
        $requiredDeptIds = collect($doctorRows)->pluck('department_id')->unique()->values()->all();
        $byDept = $this->departmentWorkingHoursService->buildDefaultWeekdayRowsWithLunchForDepartments($requiredDeptIds);

        $generator = app(DoctorRandevuSlotGenerator::class);

        $hospital = DB::transaction(function () use ($validated, $districts, $request, $doctorRows, $byDept) {
            $h = Hospital::query()->create([
                'name' => $validated['name'],
                'city' => $validated['city'] ?? null,
                'districts' => $districts,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'latitude' => $request->filled('latitude') ? (float) $validated['latitude'] : null,
                'longitude' => $request->filled('longitude') ? (float) $validated['longitude'] : null,
                'randevu_slot_dakika' => 30,
                'is_active' => $request->boolean('is_active', true),
                'is_saglik_merkezi' => false,
            ]);

            $this->workingHoursService->sync($h->id, $this->workingHoursService->buildWeekdayOneToFiveWithLunchBreak());
            $this->departmentWorkingHoursService->syncForHospital($h->id, $byDept);
            $this->departmentSettingService->syncFromMuayeneRequest($h->id, $request, $byDept, 30);

            foreach ($doctorRows as $row) {
                $tc = $row['tc_kimlik_no'] ?? $this->generateUniqueTcKimlik($row['email']);
                $user = User::query()->create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'tc_kimlik_no' => $tc,
                    'phone' => $row['phone'] ?? null,
                    'birth_date' => null,
                    'gender' => null,
                    'role' => 'doctor',
                    'password' => $row['password'],
                ]);

                Doctor::query()->create([
                    'user_id' => $user->id,
                    'department_id' => $row['department_id'],
                    'hospital_id' => $h->id,
                    'physical_clinic_name' => $row['physical_clinic_name'] ?? null,
                    'room_no' => $row['room_no'] ?? null,
                    'title' => $row['title'] ?? null,
                    'license_number' => $row['license_number'] ?? null,
                    'bio' => $row['bio'] ?? null,
                    'is_active' => true,
                    'is_aile_hekimi' => $row['is_aile_hekimi'] ?? false,
                ]);
            }

            $this->createHospitalAdminIfRequested($request, $h);

            return $h;
        });

        $generator->resyncFutureSlotsForHospital((int) $hospital->id);

        $hasKurumAdmin = User::query()
            ->where('managed_hospital_id', $hospital->id)
            ->whereRaw('LOWER(TRIM(role)) = ?', ['hospital_admin'])
            ->exists();

        return redirect()
            ->route('admin.hastaneler.index')
            ->with('success', $hasKurumAdmin
                ? 'Hastane kaydı oluşturuldu; kurum paneli yöneticisi de tanımlandı.'
                : 'Hastane kaydı oluşturuldu.');
    }

    public function edit(Hospital $hastane): View
    {
        $hastane->load(['departmentWorkingHours', 'departmentSettings', 'doctors.user', 'doctors.department', 'managedHospitalAdmins']);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $requiredDeptIdsMuayene = $this->departmentIdsWithDoctorsAtHospital($hastane);
        $poliklinikSaatleri = $this->buildPoliklinikSaatFormBlocks(
            $hastane,
            $this->departmentIdsForHospitalPoliklinik($hastane),
            $requiredDeptIdsMuayene,
        );

        return view('admin.hastaneler.edit', [
            'hastane' => $hastane,
            'departments' => $departments,
            'poliklinikSaatleri' => $poliklinikSaatleri,
            'requiredDeptIdsMuayene' => $requiredDeptIdsMuayene,
        ]);
    }

    public function update(Request $request, Hospital $hastane): RedirectResponse
    {
        $districts = $this->validatedDistrictsFromInput($request);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:5000'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $requiredDeptIds = $this->departmentIdsWithDoctorsAtHospital($hastane);
        $byDept = $this->departmentWorkingHoursService->normalizeDeptMuayeneSimpleFromRequest(
            $request,
            $this->allActiveDepartmentIds(),
            $requiredDeptIds
        );
        $generator = app(DoctorRandevuSlotGenerator::class);

        DB::transaction(function () use ($validated, $districts, $request, $hastane, $byDept) {
            $hastane->update([
                'name' => $validated['name'],
                'city' => $validated['city'] ?? null,
                'districts' => $districts,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'latitude' => $request->filled('latitude') ? (float) $validated['latitude'] : null,
                'longitude' => $request->filled('longitude') ? (float) $validated['longitude'] : null,
                'is_active' => $request->boolean('is_active', true),
                'is_saglik_merkezi' => false,
            ]);

            $this->workingHoursService->sync($hastane->id, $this->workingHoursService->buildWeekdayOneToFiveWithLunchBreak());
            $this->departmentWorkingHoursService->syncForHospital($hastane->id, $byDept);
            $this->departmentSettingService->syncFromMuayeneRequest(
                $hastane->id,
                $request,
                $byDept,
                (int) ($hastane->randevu_slot_dakika ?? 30)
            );
        });

        $generator->resyncFutureSlotsForHospital((int) $hastane->id);

        return redirect()
            ->route('admin.hastaneler.edit', $hastane)
            ->with('success', 'Hastane bilgileri, birim muayene saatleri ve randevu dilimi güncellendi; boş slotlar yenilendi.');
    }

    public function storeDoctor(Request $request, Hospital $hastane): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'tc_kimlik_no' => ['nullable', 'string', 'size:11', 'unique:'.User::class.',tc_kimlik_no'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'physical_clinic_name' => ['nullable', 'string', 'max:120'],
            'room_no' => ['nullable', 'string', 'max:32'],
            'title' => ['nullable', 'string', 'max:64'],
            'license_number' => ['nullable', 'string', 'max:64'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'is_aile_hekimi' => ['nullable', 'boolean'],
        ]);

        $tc = $validated['tc_kimlik_no'] ?? $this->generateUniqueTcKimlik($validated['email']);

        DB::transaction(function () use ($validated, $tc, $hastane, $request) {
            $user = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'tc_kimlik_no' => $tc,
                'phone' => $validated['phone'] ?? null,
                'birth_date' => null,
                'gender' => null,
                'role' => 'doctor',
                'password' => $validated['password'],
            ]);

            Doctor::query()->create([
                'user_id' => $user->id,
                'department_id' => $validated['department_id'],
                'hospital_id' => $hastane->id,
                'physical_clinic_name' => $validated['physical_clinic_name'] ?? null,
                'room_no' => $validated['room_no'] ?? null,
                'title' => $validated['title'] ?? null,
                'license_number' => $validated['license_number'] ?? null,
                'bio' => $validated['bio'] ?? null,
                'is_active' => true,
                'is_aile_hekimi' => $request->boolean('is_aile_hekimi'),
            ]);
        });

        app(DoctorRandevuSlotGenerator::class)->resyncFutureSlotsForHospital((int) $hastane->id);

        return redirect()
            ->route('admin.hastaneler.edit', $hastane)
            ->with('success', 'Doktor hesabı bu hastaneye eklendi.');
    }

    public function storeHospitalAdmin(Request $request, Hospital $hastane): RedirectResponse
    {
        $request->merge([
            'kurum_admin_email' => strtolower(trim((string) $request->input('kurum_admin_email', ''))),
        ]);

        $validated = $request->validate([
            'kurum_admin_name' => ['required', 'string', 'max:255'],
            'kurum_admin_email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email'],
            'kurum_admin_password' => ['required', 'confirmed', Password::defaults()],
        ], [], [
            'kurum_admin_name' => 'ad soyad',
            'kurum_admin_email' => 'e-posta',
            'kurum_admin_password' => 'şifre',
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
            'managed_hospital_id' => $hastane->id,
        ]);

        $hastane->refresh();

        $editRoute = $hastane->is_saglik_merkezi
            ? 'admin.saglik-merkezleri.edit'
            : 'admin.hastaneler.edit';

        return redirect()
            ->route($editRoute, $hastane)
            ->with('success', 'Kurum paneli kullanıcısı oluşturuldu. Giriş sayfasında «Kurum» sekmesini seçmelidir.');
    }

    public function updateHospitalAdmin(Request $request, Hospital $hastane, User $kurumYoneticisi): RedirectResponse
    {
        $this->assertHospitalAdminForHospital($kurumYoneticisi, $hastane);

        $aid = (int) $kurumYoneticisi->id;
        $prefix = "kurum_admins.$aid";

        $kurumAdmins = $request->input('kurum_admins', []);
        $kurumAdmins = is_array($kurumAdmins) ? $kurumAdmins : [];
        $sub = $kurumAdmins[$aid] ?? [];
        $sub = is_array($sub) ? $sub : [];
        $kurumAdmins[$aid] = array_merge($sub, [
            'email' => strtolower(trim((string) ($sub['email'] ?? ''))),
        ]);
        $request->merge(['kurum_admins' => $kurumAdmins]);

        $validated = $request->validate([
            "$prefix.name" => ['required', 'string', 'max:255'],
            "$prefix.email" => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($kurumYoneticisi->id)],
            "$prefix.phone" => ['nullable', 'string', 'max:20'],
            "$prefix.password" => ['nullable', 'confirmed', Password::defaults()],
        ], [], [
            "$prefix.name" => 'ad soyad',
            "$prefix.email" => 'e-posta',
            "$prefix.phone" => 'telefon',
            "$prefix.password" => 'şifre',
        ]);

        $row = $validated['kurum_admins'][$aid];

        $kurumYoneticisi->fill([
            'name' => $row['name'],
            'email' => $row['email'],
            'phone' => $row['phone'] ?? null,
        ]);

        if (! empty($row['password'])) {
            $kurumYoneticisi->password = $row['password'];
        }

        $kurumYoneticisi->save();

        $hastane->refresh();
        $editRoute = $hastane->is_saglik_merkezi
            ? 'admin.saglik-merkezleri.edit'
            : 'admin.hastaneler.edit';

        return redirect()
            ->route($editRoute, $hastane)
            ->with('success', 'Kurum yöneticisi bilgileri güncellendi.');
    }

    public function destroyHospitalAdmin(Hospital $hastane, User $kurumYoneticisi): RedirectResponse
    {
        $this->assertHospitalAdminForHospital($kurumYoneticisi, $hastane);

        $kurumYoneticisi->delete();

        $hastane->refresh();
        $editRoute = $hastane->is_saglik_merkezi
            ? 'admin.saglik-merkezleri.edit'
            : 'admin.hastaneler.edit';

        return redirect()
            ->route($editRoute, $hastane)
            ->with('success', 'Kurum yöneticisi silindi.');
    }

    private function assertHospitalAdminForHospital(User $user, Hospital $hastane): void
    {
        if (! $user->isHospitalAdmin() || (int) $user->managed_hospital_id !== (int) $hastane->id) {
            abort(404);
        }
    }

    public function destroy(Hospital $hastane): RedirectResponse
    {
        $hastane->delete();

        return redirect()
            ->route('admin.hastaneler.index')
            ->with('success', 'Hastane kaydı silindi.');
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function doctorRowsFromOld(): array
    {
        $raw = old('doctors');
        if (! is_array($raw)) {
            return [[]];
        }
        $raw = array_values($raw);

        return $raw === [] ? [[]] : $raw;
    }

    /**
     * @return list<array{name:string, email:string, password:string, department_id:int, title:?string, license_number:?string, bio:?string, phone:?string, tc_kimlik_no:?string}>
     */
    private function validatedDoctorRowsFromRequest(Request $request, bool $uniqueEmail): array
    {
        $raw = $request->input('doctors', []);
        if (! is_array($raw)) {
            return [];
        }

        $usedEmails = [];
        $out = [];

        foreach (array_values($raw) as $idx => $row) {
            if (! is_array($row)) {
                continue;
            }
            $email = isset($row['email']) ? trim((string) $row['email']) : '';
            if ($email === '') {
                continue;
            }

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'department_id' => ['nullable', 'integer', 'exists:departments,id'],
                'department_name' => ['nullable', 'string', 'max:120'],
                'physical_clinic_name' => ['nullable', 'string', 'max:120'],
                'room_no' => ['nullable', 'string', 'max:32'],
                'title' => ['nullable', 'string', 'max:64'],
                'license_number' => ['nullable', 'string', 'max:64'],
                'bio' => ['nullable', 'string', 'max:5000'],
                'phone' => ['nullable', 'string', 'max:20'],
                'tc_kimlik_no' => ['nullable', 'string', 'size:11'],
                'is_aile_hekimi' => ['sometimes', 'boolean'],
            ];

            if ($uniqueEmail) {
                $rules['email'][] = Rule::unique(User::class, 'email');
            }

            $validator = Validator::make($row, $rules, [], [
                'name' => 'doktor adı',
                'email' => 'e-posta',
                'password' => 'şifre',
                'department_id' => 'birim',
                'department_name' => 'birim',
            ]);

            if ($validator->fails()) {
                throw ValidationException::withMessages(
                    collect($validator->errors()->messages())
                        ->mapWithKeys(fn ($msgs, $key) => ["doctors.$idx.$key" => $msgs])
                        ->all()
                );
            }

            $data = $validator->validated();
            $departmentName = trim((string) ($data['department_name'] ?? ''));

            if (empty($data['department_id']) && $departmentName === '') {
                throw ValidationException::withMessages([
                    "doctors.$idx.department_name" => 'Birim zorunludur.',
                ]);
            }

            if ($uniqueEmail && in_array(strtolower($data['email']), $usedEmails, true)) {
                throw ValidationException::withMessages([
                    "doctors.$idx.email" => 'Aynı formda bu e-posta birden fazla kez kullanılamaz.',
                ]);
            }
            $usedEmails[] = strtolower($data['email']);

            if (! empty($data['tc_kimlik_no'])) {
                if (User::query()->where('tc_kimlik_no', $data['tc_kimlik_no'])->exists()) {
                    throw ValidationException::withMessages([
                        "doctors.$idx.tc_kimlik_no" => 'Bu T.C. kimlik numarası zaten kayıtlı.',
                    ]);
                }
            }

            $out[] = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'department_id' => ! empty($data['department_id'])
                    ? (int) $data['department_id']
                    : $this->resolveDepartmentByName($departmentName)->id,
                'title' => $data['title'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'physical_clinic_name' => $data['physical_clinic_name'] ?? null,
                'room_no' => $data['room_no'] ?? null,
                'bio' => $data['bio'] ?? null,
                'phone' => $data['phone'] ?? null,
                'tc_kimlik_no' => $data['tc_kimlik_no'] ?? null,
                'is_aile_hekimi' => (bool) ($data['is_aile_hekimi'] ?? false),
            ];
        }

        return $out;
    }

    private function formatTimeInput(mixed $t): string
    {
        if ($t instanceof \DateTimeInterface) {
            return $t->format('H:i');
        }

        try {
            return Carbon::parse((string) $t)->format('H:i');
        } catch (\Throwable) {
            return '09:00';
        }
    }

    private function resolveDepartmentByName(string $rawName): Department
    {
        $name = trim($rawName);
        $department = Department::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();

        if (! $department) {
            return Department::query()->create([
                'name' => $name,
                'is_active' => true,
                'sort_order' => 0,
            ]);
        }

        if (! $department->is_active) {
            $department->update(['is_active' => true]);
        }

        return $department;
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
