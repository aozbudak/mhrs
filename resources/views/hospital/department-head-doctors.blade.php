@extends('layouts.hospital')

@section('title', 'Bölüm doktor listesi')
@section('subtitle', ($doctors->first()?->department?->name ?? 'Bölüm').' · '.$headUser->managedHospital?->name)

@section('content')
    <div class="mx-auto max-w-5xl space-y-4">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-sm font-extrabold text-slate-900">Doktorların ayrı listesi</h2>
            <a href="{{ route('bolum-baskanligi.panel') }}" class="rounded-xl border border-sky-200 bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-800 hover:bg-sky-100 transition">
                Panele dön
            </a>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-sky-200/70 bg-white/70 hospital-glass">
            <table class="min-w-full text-sm">
                <thead class="bg-sky-50/70 text-xs font-bold text-slate-700">
                    <tr>
                        <th class="px-3 py-2 text-left">Doktor</th>
                        <th class="px-3 py-2 text-left">Bugünkü randevu</th>
                        <th class="px-3 py-2 text-left">İzinler</th>
                        <th class="px-3 py-2 text-left">Son işlemler</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-100">
                    @forelse ($doctors as $doctor)
                        @php $doctorLeaves = $leaveMap->get($doctor->id, collect()); @endphp
                        <tr class="hover:bg-sky-50/40">
                            <td class="px-3 py-2.5 font-medium text-slate-900">
                                <button type="button"
                                        class="underline decoration-sky-300 underline-offset-4 hover:text-sky-800 transition"
                                        data-toggle-history="list-doctor-history-{{ $doctor->id }}">
                                    {{ $doctor->user?->name ?? 'Doktor #'.$doctor->id }}
                                </button>
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-700">{{ (int) ($dailyAppointmentCounts[$doctor->id] ?? 0) }}</td>
                            <td class="px-3 py-2.5 text-xs text-slate-600">
                                @if($doctorLeaves->isEmpty())
                                    —
                                @else
                                    {{ $doctorLeaves->take(4)->pluck('tarih')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d.m.Y'))->implode(', ') }}
                                @endif
                            </td>
                            <td class="px-3 py-2.5 text-xs text-slate-600">
                                @php $latestActions = $doctor->actionHistories->take(3); @endphp
                                @if($latestActions->isEmpty())
                                    —
                                @else
                                    {{ $latestActions->pluck('action_text')->implode(' | ') }}
                                @endif
                            </td>
                        </tr>
                        <tr id="list-doctor-history-{{ $doctor->id }}" class="hidden bg-white">
                            <td colspan="4" class="px-3 py-3">
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <h4 class="text-xs font-bold text-slate-800">İşlem geçmişi (son 5)</h4>
                                    <ul class="mt-2 space-y-1.5 text-xs text-slate-700">
                                        @forelse($doctor->actionHistories->take(5) as $history)
                                            <li class="rounded-lg border border-slate-100 bg-white px-2.5 py-1.5">
                                                <span class="font-semibold text-slate-800">{{ $history->created_at?->format('d.m.Y H:i') }}</span>
                                                — {{ $history->action_text }}
                                            </li>
                                        @empty
                                            <li class="text-slate-500">Henüz işlem kaydı yok.</li>
                                        @endforelse
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-sm text-slate-500">Bu bölüme bağlı doktor bulunamadı.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('[data-toggle-history]').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var targetId = btn.getAttribute('data-toggle-history');
                    var panel = document.getElementById(targetId);
                    if (!panel) return;
                    var isCurrentlyHidden = panel.classList.contains('hidden');

                    document.querySelectorAll('[id^="list-doctor-history-"]').forEach(function (item) {
                        item.classList.add('hidden');
                    });

                    if (isCurrentlyHidden) {
                        panel.classList.remove('hidden');
                    }
                });
            });
        </script>
    @endpush
@endsection
