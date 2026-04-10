{{-- @var \Illuminate\Support\Collection<int, \App\Models\Doctor> $doktorlar --}}
<div class="overflow-x-auto rounded-2xl border border-sky-200/60">
    <table class="min-w-full text-sm">
        <thead class="bg-sky-50/60 text-xs font-bold text-slate-700">
            <tr>
                <th class="px-3 py-2 text-left">Doktor</th>
                <th class="px-3 py-2 text-left">E-posta</th>
                <th class="px-3 py-2 text-left">Birim</th>
                <th class="px-3 py-2 text-left">Poliklinik / oda</th>
                <th class="px-3 py-2 text-left">Durum</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-sky-100">
            @forelse ($doktorlar as $d)
                <tr class="hover:bg-sky-50/40">
                    <td class="px-3 py-2.5 font-medium text-slate-900">
                        {{ $d->user?->name ?? $d->title ?? 'Kayıt #'.$d->id }}
                        @if($d->is_aile_hekimi)
                            <span class="ml-1 inline-block rounded-md bg-emerald-100 px-1.5 py-0.5 text-[10px] font-bold text-emerald-800">Aile hek.</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-xs text-slate-600">{{ $d->user?->email ?? '—' }}</td>
                    <td class="px-3 py-2.5 text-xs text-slate-600">{{ $d->department?->name ?? '—' }}</td>
                    <td class="px-3 py-2.5 text-xs text-slate-600">
                        {{ $d->physical_clinic_name ?? '—' }}
                        @if($d->room_no)
                            <span class="text-slate-500">· Oda {{ $d->room_no }}</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5">
                        @if($d->is_active)
                            <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-bold text-emerald-900">Aktif</span>
                        @else
                            <span class="inline-flex rounded-full bg-slate-200 px-2 py-0.5 text-[11px] font-bold text-slate-700">Pasif</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-3 py-8 text-center text-sm text-slate-500">
                        @if(! empty($seciliPoliklinikId) && ! empty($poliklinikFiltreTemizRoute))
                            Bu poliklinlikte kayıtlı doktor bulunmuyor.
                            <a href="{{ route($poliklinikFiltreTemizRoute) }}" class="ml-1 font-semibold text-sky-800 underline hover:text-sky-950">Tümünü göster</a>
                        @else
                            Henüz doktor kaydı yok. Doktor eklemek için merkez yöneticinin <span class="font-semibold text-slate-800">Admin → Hastaneler → düzenle</span> ekranını kullanması gerekir.
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
