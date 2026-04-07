<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\BildirimAyarlari;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'days_before' => ['required', 'integer', 'min:1', 'max:30'],
            'message_template' => ['required', 'string', 'max:1000'],
        ], [], [
            'days_before' => 'gün',
            'message_template' => 'mesaj şablonu',
        ]);

        BildirimAyarlari::save(
            (int) $validated['days_before'],
            trim((string) $validated['message_template'])
        );

        return redirect()
            ->route('admin.bildirimler.index')
            ->with('success', 'Bildirim ayarları güncellendi.');
    }

    public function index(Request $request): View
    {
        $settings = BildirimAyarlari::get();
        $driver = DB::connection()->getDriverName();
        $status = $request->string('durum')->trim()->value();
        if (! in_array($status, ['tum', 'okunmamis', 'okunmus'], true)) {
            $status = 'tum';
        }

        $kind = $request->string('kind')->trim()->value();
        if ($kind === '') {
            $kind = null;
        }

        $q = $request->string('q')->trim()->value();
        if ($q === '') {
            $q = null;
        }

        $query = DatabaseNotification::query()
            ->where('notifiable_type', User::class)
            ->with('notifiable')
            ->latest();

        if ($status === 'okunmamis') {
            $query->whereNull('read_at');
        } elseif ($status === 'okunmus') {
            $query->whereNotNull('read_at');
        }

        if ($kind !== null) {
            if ($driver === 'pgsql') {
                $query->whereRaw("(data::jsonb ->> 'kind') = ?", [$kind]);
            } else {
                $query->where('data->kind', $kind);
            }
        }

        if ($q !== null) {
            $term = '%'.addcslashes($q, '%_\\').'%';
            $query->where(function ($w) use ($term) {
                $driver = DB::connection()->getDriverName();
                if ($driver === 'pgsql') {
                    $w->whereRaw("(data::jsonb ->> 'title') ILIKE ?", [$term])
                        ->orWhereRaw("(data::jsonb ->> 'message') ILIKE ?", [$term]);
                } else {
                    $w->where('data->title', 'like', $term)
                        ->orWhere('data->message', 'like', $term);
                }

                $w
                    ->orWhereHasMorph(
                        'notifiable',
                        [User::class],
                        fn ($uq) => $uq->where('name', 'like', $term)
                            ->orWhere('email', 'like', $term)
                    );
            });
        }

        $notifications = $query->paginate(20)->withQueryString();

        $base = DatabaseNotification::query()->where('notifiable_type', User::class);
        $summary = [
            'toplam' => (clone $base)->count(),
            'okunmamis' => (clone $base)->whereNull('read_at')->count(),
            'okunmus' => (clone $base)->whereNotNull('read_at')->count(),
            'katilimKontrolu' => $driver === 'pgsql'
                ? (clone $base)->whereRaw("(data::jsonb ->> 'kind') = 'appointment_attendance_check'")->count()
                : (clone $base)->where('data->kind', 'appointment_attendance_check')->count(),
        ];

        return view('admin.bildirimler.index', [
            'settings' => $settings,
            'notifications' => $notifications,
            'status' => $status,
            'kind' => $kind,
            'search' => $q,
            'summary' => $summary,
        ]);
    }
}
