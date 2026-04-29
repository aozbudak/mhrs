<?php

namespace App\Http\Controllers\Hospital;

use App\Enums\RandevuDurumu;
use App\Http\Controllers\Concerns\BuildsPoliklinikSaatFormBlocks;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Hospital;
use App\Models\Randevu;
use App\Models\User;
use App\Services\DoctorRandevuSlotGenerator;
use App\Services\HospitalDepartmentSettingService;
use App\Services\HospitalDepartmentWorkingHoursService;
use App\Services\HospitalWorkingHoursService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class HospitalPanelController extends Controller
{
    use BuildsPoliklinikSaatFormBlocks;

    protected function institutionRouteGroup(): string
    {
        return 'hastane';
    }

    protected function institutionRouteName(string $suffix): string
    {
        return $this->institutionRouteGroup().'.'.$suffix;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function institutionView(string $name, array $data = []): View
    {
        return view($name, array_merge($data, [
            'kr' => $this->institutionRouteGroup(),
        ]));
    }

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

    public function __construct(
        private readonly HospitalWorkingHoursService $workingHoursService,
        private readonly HospitalDepartmentWorkingHoursService $departmentWorkingHoursService,
        private readonly HospitalDepartmentSettingService $departmentSettingService,
    ) {}

    public function index(Request $request): View
    {
        $hastane = $this->authorizedHospital();

        $doktorSayisi = Doctor::query()->where('hospital_id', $hastane->id)->count();
        $aktifRandevu = Randevu::query()
            ->whereHas('doctor', fn ($q) => $q->where('hospital_id', $hastane->id))
            ->where('durum', '!=', RandevuDurumu::Iptal)
            ->count();

        $seciliPoliklinikId = $this->seciliPoliklinikId($request, $hastane);
        $poliklinikler = $this->polikliniklerForHospital($hastane);
        $doktorlar = $this->doktorlarOrdered($hastane, $seciliPoliklinikId);

        return $this->institutionView('hospital.panel', [
            'hastane' => $hastane,
            'doktorSayisi' => $doktorSayisi,
            'aktifRandevu' => $aktifRandevu,
            'doktorlar' => $doktorlar,
            'poliklinikler' => $poliklinikler,
            'seciliPoliklinikId' => $seciliPoliklinikId,
            'routeName' => $this->institutionRouteName('panel'),
            'poliklinikFiltreTemizRoute' => $this->institutionRouteName('panel'),
        ]);
    }

    public function doktorlar(Request $request): View
    {
        $hastane = $this->authorizedHospital();

        $seciliPoliklinikId = $this->seciliPoliklinikId($request, $hastane);
        $poliklinikler = $this->polikliniklerForHospital($hastane);
        $doktorToplam = Doctor::query()->where('hospital_id', $hastane->id)->count();

        return $this->institutionView('hospital.doktorlar', [
            'hastane' => $hastane,
            'doktorlar' => $this->doktorlarOrdered($hastane, $seciliPoliklinikId),
            'poliklinikler' => $poliklinikler,
            'seciliPoliklinikId' => $seciliPoliklinikId,
            'doktorToplam' => $doktorToplam,
            'routeName' => $this->institutionRouteName('doktorlar'),
            'poliklinikFiltreTemizRoute' => $this->institutionRouteName('doktorlar'),
        ]);
    }

    public function ayarlar(Request $request): View
    {
        $hastane = $this->authorizedHospital();
        $hastane->load(['departmentWorkingHours', 'departmentSettings', 'doctors.user', 'doctors.department', 'managedDepartmentHeads.managedDepartment']);

        $seciliPoliklinikId = $this->seciliPoliklinikId($request, $hastane);
        $poliklinikler = $this->polikliniklerForHospital($hastane);
        $doktorlarFiltreli = $this->doktorlarOrdered($hastane, $seciliPoliklinikId);
        $doktorToplam = Doctor::query()->where('hospital_id', $hastane->id)->count();

        $requiredDeptIdsMuayene = $this->departmentIdsWithDoctorsAtHospital($hastane);
        $poliklinikSaatleri = $this->buildPoliklinikSaatFormBlocks(
            $hastane,
            $requiredDeptIdsMuayene,
            $requiredDeptIdsMuayene,
        );

        return $this->institutionView('hospital.ayarlar', [
            'hastane' => $hastane,
            'poliklinikSaatleri' => $poliklinikSaatleri,
            'requiredDeptIdsMuayene' => $requiredDeptIdsMuayene,
            'doktorlarFiltreli' => $doktorlarFiltreli,
            'poliklinikler' => $poliklinikler,
            'seciliPoliklinikId' => $seciliPoliklinikId,
            'doktorToplam' => $doktorToplam,
            'routeName' => $this->institutionRouteName('ayarlar'),
            'poliklinikFiltreTemizRoute' => $this->institutionRouteName('ayarlar'),
            'hastaneBolumleri' => $hastane->doctors
                ->pluck('department')
                ->filter()
                ->unique('id')
                ->sortBy('name')
                ->values(),
        ]);
    }

    public function storeDepartmentHead(Request $request): RedirectResponse
    {
        $hastane = $this->authorizedHospital();
        $request->merge([
            'bolum_baskani_email' => strtolower(trim((string) $request->input('bolum_baskani_email', ''))),
        ]);

        $validated = $request->validate([
            'bolum_baskani_name' => ['required', 'string', 'max:255'],
            'bolum_baskani_email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class.',email'],
            'bolum_baskani_password' => ['required', 'confirmed', Password::defaults()],
            'bolum_baskani_department_id' => ['required', 'integer', 'exists:departments,id'],
        ]);

        $departmentId = (int) $validated['bolum_baskani_department_id'];
        $departmentExistsInHospital = Doctor::query()
            ->where('hospital_id', $hastane->id)
            ->where('department_id', $departmentId)
            ->exists();
        if (! $departmentExistsInHospital) {
            return redirect()->route('hastane.ayarlar')->with('error', 'Bölüm başkanı sadece kurumdaki aktif bölümlerden birine atanabilir.');
        }

        $alreadyAssigned = User::query()
            ->where('managed_hospital_id', $hastane->id)
            ->where('managed_department_id', $departmentId)
            ->whereRaw('LOWER(TRIM(role)) = ?', ['department_head'])
            ->exists();
        if ($alreadyAssigned) {
            return redirect()->route('hastane.ayarlar')->with('error', 'Bu bölüm için zaten bir bölüm başkanı tanımlı.');
        }

        User::query()->create([
            'name' => $validated['bolum_baskani_name'],
            'email' => $validated['bolum_baskani_email'],
            'tc_kimlik_no' => $this->generateUniqueTcKimlik($validated['bolum_baskani_email']),
            'phone' => null,
            'birth_date' => null,
            'gender' => null,
            'role' => 'department_head',
            'password' => $validated['bolum_baskani_password'],
            'managed_hospital_id' => $hastane->id,
            'managed_department_id' => $departmentId,
        ]);

        return redirect()
            ->route('hastane.ayarlar')
            ->with('success', 'Bölüm başkanı hesabı eklendi.');
    }

    public function destroyDepartmentHead(User $bolumBaskani): RedirectResponse
    {
        $hastane = $this->authorizedHospital();
        if (
            ! $bolumBaskani->isDepartmentHead()
            || (int) $bolumBaskani->managed_hospital_id !== (int) $hastane->id
        ) {
            abort(404);
        }

        $bolumBaskani->delete();

        return redirect()
            ->route('hastane.ayarlar')
            ->with('success', 'Bölüm başkanı kaldırıldı.');
    }

    public function update(Request $request): RedirectResponse
    {
        $hastane = $this->authorizedHospital();

        $requiredDeptIds = $this->departmentIdsWithDoctorsAtHospital($hastane);
        $byDept = $this->departmentWorkingHoursService->normalizeDeptMuayeneSimpleFromRequest(
            $request,
            $requiredDeptIds,
            $requiredDeptIds
        );
        $generator = app(DoctorRandevuSlotGenerator::class);

        DB::transaction(function () use ($hastane, $byDept, $request) {
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

        $redirectQuery = [];
        if ($request->filled('poliklinik')) {
            $pid = (int) $request->input('poliklinik');
            if (Doctor::query()->where('hospital_id', $hastane->id)->where('department_id', $pid)->exists()) {
                $redirectQuery['poliklinik'] = $pid;
            }
        }

        return redirect()
            ->route($this->institutionRouteName('ayarlar'), $redirectQuery)
            ->with('success', 'Birim muayene saatleri ve randevu dilimi güncellendi; gelecekteki boş slotlar yenilendi.');
    }

    public function profil(): View
    {
        $user = $this->authorizedHospitalUser();
        $hastane = $this->authorizedHospital();

        return $this->institutionView('hospital.profil', [
            'user' => $user->fresh(),
            'hastane' => $hastane,
        ]);
    }

    public function profilGuncelle(Request $request): RedirectResponse
    {
        $user = $this->authorizedHospitalUser();

        $request->merge([
            'email' => strtolower(trim((string) $request->input('email', ''))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:password', 'current_password:hospital'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ], [], [
            'current_password' => 'mevcut şifre',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();

        return redirect()
            ->route($this->institutionRouteName('profil'))
            ->with('success', 'Profiliniz güncellendi.');
    }

    protected function authorizedHospitalUser(): User
    {
        $user = auth('hospital')->user();
        if (! $user instanceof User || ! $user->isHospitalAdmin() || ((int) $user->managed_hospital_id) < 1) {
            abort(403);
        }

        return $user;
    }

    protected function authorizedHospital(): Hospital
    {
        $user = $this->authorizedHospitalUser();

        return Hospital::query()
            ->whereKey($user->managed_hospital_id)
            ->where('is_saglik_merkezi', false)
            ->firstOrFail();
    }

    /**
     * Geçerli kurumda bu birime bağlı en az bir doktor yoksa filtre uygulanmaz.
     */
    private function seciliPoliklinikId(Request $request, Hospital $hastane): ?int
    {
        $validated = $request->validate([
            'poliklinik' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $id = isset($validated['poliklinik']) ? (int) $validated['poliklinik'] : null;
        if ($id === null || $id === 0) {
            return null;
        }

        $exists = Doctor::query()
            ->where('hospital_id', $hastane->id)
            ->where('department_id', $id)
            ->exists();

        return $exists ? $id : null;
    }

    /** @return Collection<int, Department> */
    private function polikliniklerForHospital(Hospital $hastane): Collection
    {
        return Department::query()
            ->whereHas('doctors', fn ($q) => $q->where('hospital_id', $hastane->id))
            ->withCount([
                'doctors as hospital_doctor_count' => fn ($q) => $q->where('hospital_id', $hastane->id),
            ])
            ->orderBy('name')
            ->get();
    }

    /** @return Collection<int, Doctor> */
    private function doktorlarOrdered(Hospital $hastane, ?int $departmentId = null): Collection
    {
        $q = Doctor::query()
            ->where('hospital_id', $hastane->id)
            ->with(['user', 'department']);

        if ($departmentId !== null) {
            $q->where('department_id', $departmentId);
        }

        return $q->get()
            ->sortBy(function (Doctor $d) {
                return mb_strtolower($d->user?->name ?? $d->title ?? (string) $d->id);
            })
            ->values();
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
        $base = abs(crc32($email.'dept-a'));
        $checksum = abs(crc32($email.'dept-b'));

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
