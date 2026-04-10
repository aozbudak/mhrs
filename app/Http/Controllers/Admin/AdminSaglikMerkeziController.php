<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\HandlesHospitalDistrictInput;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\HospitalWorkingHour;
use App\Models\User;
use App\Services\DoctorRandevuSlotGenerator;
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

class AdminSaglikMerkeziController extends Controller
{
    use HandlesHospitalDistrictInput;

    public function __construct(
        private readonly HospitalWorkingHoursService $workingHoursService
    ) {}

    /** ISO hafta günü 1 = Pazartesi … 7 = Pazar */
    private const GUNLER = [
        1 => 'Pazartesi',
        2 => 'Salı',
        3 => 'Çarşamba',
        4 => 'Perşembe',
        5 => 'Cuma',
        6 => 'Cumartesi',
        7 => 'Pazar',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'city' => ['nullable', 'string', 'max:100'],
        ]);

        $filterCity = isset($validated['city']) && $validated['city'] !== ''
            ? $validated['city']
            : null;

        $iller = Hospital::query()
            ->where('is_saglik_merkezi', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->orderBy('city')
            ->pluck('city')
            ->values();

        $hastaneler = Hospital::query()
            ->where('is_saglik_merkezi', true)
            ->when($filterCity, fn ($q) => $q->where('city', $filterCity))
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('admin.saglik-merkezleri.index', [
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

        $intervals = old('intervals', null);
        if ($intervals === null) {
            $intervals = $this->defaultWorkingIntervals();
        } else {
            $intervals = array_values(is_array($intervals) ? $intervals : []);
        }

        return view('admin.saglik-merkezleri.create', [
            'departments' => $departments,
            'intervals' => $intervals,
            'gunler' => self::GUNLER,
            'doctorRows' => $this->doctorRowsFromOld(),
            'useFixedWeekdayColumn' => $this->shouldUseFixedWeekdayRowsForCreate($intervals),
        ]);
    }

    /**
     * Yalnızca Pazartesi–Cuma, her gün tek satır ve sıra bozulmamışsa gün sütununda açılır liste göstermeyiz (gün adı tek yazılır).
     *
     * @param  list<array<string, mixed>>  $intervals
     */
    private function shouldUseFixedWeekdayRowsForCreate(array $intervals): bool
    {
        if (count($intervals) !== 5) {
            return false;
        }
        foreach (array_values($intervals) as $i => $row) {
            if ((int) ($row['weekday'] ?? 0) !== $i + 1) {
                return false;
            }
        }

        return true;
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

        $normalizedIntervals = $this->workingHoursService->normalizeIntervalsFromRequest($request);
        $doctorRows = $this->validatedDoctorRowsFromRequest($request, uniqueEmail: true);

        $generator = app(DoctorRandevuSlotGenerator::class);

        $hospital = DB::transaction(function () use ($validated, $districts, $request, $normalizedIntervals, $doctorRows) {
            $h = Hospital::query()->create([
                'name' => $validated['name'],
                'city' => $validated['city'] ?? null,
                'districts' => $districts,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'latitude' => $request->filled('latitude') ? (float) $validated['latitude'] : null,
                'longitude' => $request->filled('longitude') ? (float) $validated['longitude'] : null,
                'is_active' => $request->boolean('is_active', true),
                'is_saglik_merkezi' => true,
            ]);

            $this->workingHoursService->sync($h->id, $normalizedIntervals);

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

            return $h;
        });

        $generator->resyncFutureSlotsForHospital((int) $hospital->id);

        return redirect()
            ->route('admin.saglik-merkezleri.index')
            ->with('success', 'Hastane kaydı oluşturuldu.');
    }

    public function edit(Hospital $hastane): View
    {
        $hastane->load(['workingHours', 'doctors.user', 'doctors.department', 'managedHospitalAdmins']);

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $intervals = old('intervals', null);
        if ($intervals === null) {
            $intervals = $hastane->workingHours->map(function (HospitalWorkingHour $wh) {
                return [
                    'weekday' => (int) $wh->weekday,
                    'start_time' => $this->formatTimeInput($wh->start_time),
                    'end_time' => $this->formatTimeInput($wh->end_time),
                ];
            })->values()->all();
        } else {
            $intervals = array_values(is_array($intervals) ? $intervals : []);
        }

        return view('admin.saglik-merkezleri.edit', [
            'hastane' => $hastane,
            'departments' => $departments,
            'intervals' => $intervals,
            'gunler' => self::GUNLER,
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

        $normalizedIntervals = $this->workingHoursService->normalizeIntervalsFromRequest($request);
        $generator = app(DoctorRandevuSlotGenerator::class);

        DB::transaction(function () use ($validated, $districts, $request, $hastane, $normalizedIntervals) {
            $hastane->update([
                'name' => $validated['name'],
                'city' => $validated['city'] ?? null,
                'districts' => $districts,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'latitude' => $request->filled('latitude') ? (float) $validated['latitude'] : null,
                'longitude' => $request->filled('longitude') ? (float) $validated['longitude'] : null,
                'is_active' => $request->boolean('is_active', true),
                'is_saglik_merkezi' => true,
            ]);

            $this->workingHoursService->sync($hastane->id, $normalizedIntervals);
        });

        $generator->resyncFutureSlotsForHospital((int) $hastane->id);

        return redirect()
            ->route('admin.saglik-merkezleri.edit', $hastane)
            ->with('success', 'Hastane bilgileri ve çalışma saatleri güncellendi; bu hastanedeki doktorların gelecekteki boş slotları yenilendi.');
    }

    public function storeDoctor(Request $request, Hospital $hastane): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'tc_kimlik_no' => ['nullable', 'string', 'size:11', 'unique:'.User::class.',tc_kimlik_no'],
            'phone' => ['nullable', 'string', 'max:20'],
            'department_name' => ['required', 'string', 'max:120'],
            'physical_clinic_name' => ['nullable', 'string', 'max:120'],
            'room_no' => ['nullable', 'string', 'max:32'],
            'title' => ['nullable', 'string', 'max:64'],
            'license_number' => ['nullable', 'string', 'max:64'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'is_aile_hekimi' => ['nullable', 'boolean'],
        ]);

        $tc = $validated['tc_kimlik_no'] ?? $this->generateUniqueTcKimlik($validated['email']);

        DB::transaction(function () use ($validated, $tc, $hastane, $request) {
            $department = $this->resolveDepartmentByName((string) $validated['department_name']);
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
                'department_id' => $department->id,
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
            ->route('admin.saglik-merkezleri.edit', $hastane)
            ->with('success', 'Doktor hesabı bu hastaneye eklendi.');
    }

    public function destroy(Hospital $hastane): RedirectResponse
    {
        $hastane->delete();

        return redirect()
            ->route('admin.saglik-merkezleri.index')
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

    private function defaultWorkingIntervals(): array
    {
        $rows = [];
        foreach ([1, 2, 3, 4, 5] as $weekday) {
            $rows[] = [
                'weekday' => $weekday,
                'start_time' => '09:00',
                'end_time' => '17:00',
            ];
        }

        return $rows;
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
                'department_name' => ['required', 'string', 'max:120'],
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
                'department_id' => $this->resolveDepartmentByName((string) $data['department_name'])->id,
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
