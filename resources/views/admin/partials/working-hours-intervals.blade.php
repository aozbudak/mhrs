{{-- Haftalık çalışma aralıkları: {fieldBase}[N][weekday|start_time|end_time] (varsayılan fieldBase: intervals) --}}
@php
    $bodyId = $bodyId ?? 'whIntervalsBody';
    $tplId = $tplId ?? 'whIntervalsRowTpl';
    $addBtnId = $addBtnId ?? 'whIntervalsAdd';
    $useFixedWeekdayColumn = $useFixedWeekdayColumn ?? false;
    $fieldBase = $fieldBase ?? 'intervals';
@endphp

<div class="rounded-2xl border border-sky-200/80 bg-white/80 overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-sky-50/70 text-left text-xs font-bold uppercase tracking-wide text-slate-600">
            <tr>
                <th class="px-3 py-2.5">Gün</th>
                <th class="px-3 py-2.5">Başlangıç</th>
                <th class="px-3 py-2.5">Bitiş</th>
                <th class="px-3 py-2.5 w-24"></th>
            </tr>
        </thead>
        <tbody id="{{ $bodyId }}" class="divide-y divide-sky-100">
            @foreach ($intervals as $i => $row)
                <tr class="wh-int-row bg-white/90 hover:bg-sky-50/30">
                    <td class="px-3 py-2 align-middle">
                        @if ($useFixedWeekdayColumn && isset($row['weekday']))
                            <span class="block min-w-[9.5rem] font-semibold text-slate-800">{{ $gunler[(int) $row['weekday']] ?? $row['weekday'] }}</span>
                            <input type="hidden" name="{{ $fieldBase }}[{{ $i }}][weekday]" value="{{ (int) $row['weekday'] }}" data-wh-field="weekday" data-wh-fixed="1">
                        @else
                            <label class="sr-only" for="wh-wd-{{ $i }}">Gün</label>
                            <select name="{{ $fieldBase }}[{{ $i }}][weekday]" id="wh-wd-{{ $i }}" data-wh-field="weekday" required
                                    class="w-full min-w-[9.5rem] rounded-xl border border-sky-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                @foreach ($gunler as $val => $label)
                                    <option value="{{ $val }}" @selected((int) ($row['weekday'] ?? 1) === (int) $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        @endif
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <label class="sr-only" for="wh-st-{{ $i }}">Başlangıç</label>
                        <input type="time" name="{{ $fieldBase }}[{{ $i }}][start_time]" id="wh-st-{{ $i }}" data-wh-field="start" step="300" required
                               value="{{ $row['start_time'] ?? '09:00' }}"
                               class="w-full rounded-xl border border-sky-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <label class="sr-only" for="wh-en-{{ $i }}">Bitiş</label>
                        <input type="time" name="{{ $fieldBase }}[{{ $i }}][end_time]" id="wh-en-{{ $i }}" data-wh-field="end" step="300" required
                               value="{{ $row['end_time'] ?? '13:00' }}"
                               class="w-full rounded-xl border border-sky-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle text-right">
                        <button type="button" class="wh-int-remove rounded-xl border border-red-200 bg-red-50/80 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100/80 transition">
                            Kaldır
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if (count($intervals) === 0)
    <p class="rounded-2xl border border-dashed border-sky-200 bg-sky-50/40 px-4 py-3 text-sm text-slate-600">
        Henüz satır yok. <strong class="font-semibold text-slate-800">Satır ekle</strong> ile gün ve saat aralığı ekleyin. Boş bırakıp kaydederseniz bu hastane için otomatik randevu slotu üretilmez.
    </p>
@endif

<div class="flex flex-wrap items-center gap-3 mt-3">
    <button type="button" id="{{ $addBtnId }}" class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
        Satır ekle
    </button>
</div>

<template id="{{ $tplId }}">
    <tr class="wh-int-row bg-white/90 hover:bg-sky-50/30">
        <td class="px-3 py-2 align-middle">
            <select data-wh-field="weekday" required
                    class="w-full min-w-[9.5rem] rounded-xl border border-sky-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                <option value="" selected disabled>— Gün seçin —</option>
                @foreach ($gunler as $val => $label)
                    <option value="{{ $val }}">{{ $label }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="time" data-wh-field="start" step="300" required value="09:00"
                   class="w-full rounded-xl border border-sky-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="time" data-wh-field="end" step="300" required value="13:00"
                   class="w-full rounded-xl border border-sky-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle text-right">
            <button type="button" class="wh-int-remove rounded-xl border border-red-200 bg-red-50/80 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100/80 transition">
                Kaldır
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    (function () {
        var body = document.getElementById(@json($bodyId));
        var tpl = document.getElementById(@json($tplId));
        var addBtn = document.getElementById(@json($addBtnId));
        var fieldBase = @json($fieldBase);
        var form = body && body.closest('form');
        if (!body || !tpl || !addBtn) return;

        function reindex() {
            var rows = body.querySelectorAll('.wh-int-row');
            rows.forEach(function (tr, i) {
                var sel = tr.querySelector('select[data-wh-field="weekday"]');
                var hid = tr.querySelector('input[type="hidden"][data-wh-field="weekday"]');
                var st = tr.querySelector('input[data-wh-field="start"]');
                var en = tr.querySelector('input[data-wh-field="end"]');
                if (sel) {
                    sel.name = fieldBase + '[' + i + '][weekday]';
                    sel.id = 'wh-wd-' + i;
                }
                if (hid) {
                    hid.name = fieldBase + '[' + i + '][weekday]';
                }
                if (st) {
                    st.name = fieldBase + '[' + i + '][start_time]';
                    st.id = 'wh-st-' + i;
                }
                if (en) {
                    en.name = fieldBase + '[' + i + '][end_time]';
                    en.id = 'wh-en-' + i;
                }
            });
        }

        function bindRemove(tr) {
            var btn = tr.querySelector('.wh-int-remove');
            if (!btn) return;
            btn.addEventListener('click', function () {
                tr.remove();
                reindex();
            });
        }

        function addRow() {
            var frag = tpl.content.cloneNode(true);
            var tr = frag.querySelector('tr');
            body.appendChild(tr);
            bindRemove(tr);
            reindex();
        }

        body.querySelectorAll('.wh-int-row').forEach(bindRemove);
        addBtn.addEventListener('click', addRow);
        if (form) {
            form.addEventListener('submit', reindex);
        }
    })();
</script>
@endpush
