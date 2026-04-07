@extends('layouts.admin')

@section('title', 'Bildirim Yönetimi')
@section('subtitle', 'Kaç gün kala ve mesaj şablonu ayarları')

@section('content')
    <section class="rounded-3xl border border-sky-100/80 bg-white/70 p-5 shadow-sm hospital-glass">
        <div class="text-sm font-bold text-slate-900">Gönderim ayarları</div>
        <p class="mt-1 text-xs text-slate-600">Mesaj içinde şu değişkenleri kullanabilirsiniz: <code>{tarih}</code>, <code>{saat}</code>, <code>{doktor}</code>, <code>{hasta}</code></p>

        <form method="post" action="{{ route('admin.bildirimler.update') }}" class="mt-4 grid gap-3 md:grid-cols-4">
            @csrf
            <label class="block md:col-span-1">
                <span class="text-xs font-semibold text-slate-600">Kaç gün kala gönderilsin?</span>
                <input type="number"
                       min="1"
                       max="30"
                       name="days_before"
                       value="{{ old('days_before', $settings['days_before']) }}"
                       class="mt-1 w-full rounded-2xl border border-sky-200/80 bg-white/90 px-3 py-2 text-sm text-slate-800">
            </label>

            <label class="block md:col-span-3">
                <span class="text-xs font-semibold text-slate-600">Mesaj şablonu</span>
                <textarea name="message_template"
                          rows="3"
                          class="mt-1 w-full rounded-2xl border border-sky-200/80 bg-white/90 px-3 py-2 text-sm text-slate-800">{{ old('message_template', $settings['message_template']) }}</textarea>
            </label>

            <div class="md:col-span-4 flex items-center gap-2">
                <button type="submit" class="rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition">
                    Ayarları Kaydet
                </button>
            </div>
        </form>
    </section>

    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-sky-100/80 bg-white/70 p-4 hospital-glass">
            <div class="text-xs font-bold uppercase tracking-wide text-sky-700">Toplam</div>
            <div class="mt-1 text-2xl font-extrabold text-slate-900">{{ $summary['toplam'] }}</div>
        </div>
        <div class="rounded-3xl border border-amber-100/90 bg-amber-50/40 p-4 hospital-glass">
            <div class="text-xs font-bold uppercase tracking-wide text-amber-800">Okunmamış</div>
            <div class="mt-1 text-2xl font-extrabold text-amber-900">{{ $summary['okunmamis'] }}</div>
        </div>
        <div class="rounded-3xl border border-emerald-100/90 bg-emerald-50/40 p-4 hospital-glass">
            <div class="text-xs font-bold uppercase tracking-wide text-emerald-800">Okunmuş</div>
            <div class="mt-1 text-2xl font-extrabold text-emerald-900">{{ $summary['okunmus'] }}</div>
        </div>
        <div class="rounded-3xl border border-violet-100/90 bg-violet-50/40 p-4 hospital-glass">
            <div class="text-xs font-bold uppercase tracking-wide text-violet-800">Katılım kontrolü</div>
            <div class="mt-1 text-2xl font-extrabold text-violet-900">{{ $summary['katilimKontrolu'] }}</div>
        </div>
    </div>

    <section class="mt-5 rounded-3xl border border-sky-100/80 bg-white/70 p-4 shadow-sm hospital-glass">
        <form method="get" action="{{ route('admin.bildirimler.index') }}" class="grid gap-3 md:grid-cols-4">
            <label class="block">
                <span class="text-xs font-semibold text-slate-600">Durum</span>
                <select name="durum" class="mt-1 w-full rounded-2xl border border-sky-200/80 bg-white/90 px-3 py-2 text-sm text-slate-800">
                    <option value="tum" @selected($status === 'tum')>Tümü</option>
                    <option value="okunmamis" @selected($status === 'okunmamis')>Okunmamış</option>
                    <option value="okunmus" @selected($status === 'okunmus')>Okunmuş</option>
                </select>
            </label>

            <label class="block">
                <span class="text-xs font-semibold text-slate-600">Bildirim tipi</span>
                <select name="kind" class="mt-1 w-full rounded-2xl border border-sky-200/80 bg-white/90 px-3 py-2 text-sm text-slate-800">
                    <option value="">Hepsi</option>
                    <option value="appointment_attendance_check" @selected($kind === 'appointment_attendance_check')>Randevu katılım kontrolü</option>
                </select>
            </label>

            <label class="block md:col-span-2">
                <span class="text-xs font-semibold text-slate-600">Ara (hasta adı, e-posta, başlık, mesaj)</span>
                <input type="text"
                       name="q"
                       value="{{ $search }}"
                       placeholder="Örn: Ahmet, gelebilecek misiniz"
                       class="mt-1 w-full rounded-2xl border border-sky-200/80 bg-white/90 px-3 py-2 text-sm text-slate-800 placeholder:text-slate-400">
            </label>

            <div class="md:col-span-4 flex items-center gap-2">
                <button type="submit" class="rounded-2xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-700 transition">
                    Filtrele
                </button>
                <a href="{{ route('admin.bildirimler.index') }}" class="rounded-2xl border border-sky-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-sky-50 transition">
                    Sıfırla
                </a>
            </div>
        </form>
    </section>

    <section class="mt-5 rounded-3xl border border-sky-100/80 bg-white/70 shadow-sm hospital-glass overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-sky-100">
                <thead class="bg-sky-50/60">
                    <tr class="text-left text-xs font-bold uppercase tracking-wide text-slate-600">
                        <th class="px-4 py-3">Hasta</th>
                        <th class="px-4 py-3">Başlık / Mesaj</th>
                        <th class="px-4 py-3">Tip</th>
                        <th class="px-4 py-3">Durum</th>
                        <th class="px-4 py-3">Tarih</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sky-100/80 text-sm">
                    @forelse($notifications as $n)
                        @php
                            $hasta = $n->notifiable;
                            $title = $n->data['title'] ?? 'Bildirim';
                            $message = $n->data['message'] ?? $n->type;
                            $kindValue = $n->data['kind'] ?? '-';
                            $kindLabel = $kindValue === 'appointment_attendance_check' ? 'Randevu katılım kontrolü' : $kindValue;
                        @endphp
                        <tr class="align-top {{ $n->read_at ? 'bg-white/40' : 'bg-amber-50/20' }}">
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $hasta?->name ?? 'Hasta bulunamadı' }}</div>
                                <div class="mt-0.5 text-xs text-slate-500">{{ $hasta?->email ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="font-semibold text-slate-900">{{ $title }}</div>
                                <div class="mt-0.5 text-xs leading-relaxed text-slate-600">{{ $message }}</div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600">{{ $kindLabel }}</td>
                            <td class="px-4 py-3">
                                @if($n->read_at)
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-900">Okunmuş</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-bold text-amber-900">Okunmamış</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500">
                                <div>{{ $n->created_at?->format('d.m.Y H:i') }}</div>
                                <div class="mt-0.5">{{ $n->created_at?->diffForHumans() }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-500">
                                Seçili filtrelere uygun bildirim bulunamadı.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-sky-100/80 bg-white/60 px-4 py-3">
            {{ $notifications->links() }}
        </div>
    </section>
@endsection
