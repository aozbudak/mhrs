{{-- doctors[N][name|email|password|password_confirmation|department_id/department_name|title|...] --}}
@php
    $docBodyId = $docBodyId ?? 'hospitalDoctorsBody';
    $docTplId = $docTplId ?? 'hospitalDoctorRowTpl';
    $docAddId = $docAddId ?? 'hospitalDoctorsAdd';
    $departmentInputMode = $departmentInputMode ?? 'select'; // select|text
@endphp

<div class="rounded-2xl border border-emerald-200/80 bg-white/80 overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-emerald-50/70 text-left text-xs font-bold uppercase tracking-wide text-slate-600">
            <tr>
                <th class="px-3 py-2.5">Ad soyad</th>
                <th class="px-3 py-2.5">E-posta</th>
                <th class="px-3 py-2.5">Şifre</th>
                <th class="px-3 py-2.5">Şifre (tekrar)</th>
                <th class="px-3 py-2.5">Birim</th>
                <th class="px-3 py-2.5">Fiziksel poliklinik</th>
                <th class="px-3 py-2.5">Oda</th>
                <th class="px-3 py-2.5">Ünvan</th>
                <th class="px-3 py-2.5 whitespace-nowrap">Aile hekimi</th>
                <th class="px-3 py-2.5 w-24"></th>
            </tr>
        </thead>
        <tbody id="{{ $docBodyId }}" class="divide-y divide-emerald-100">
            @foreach ($doctorRows as $i => $dr)
                <tr class="hospital-doc-row bg-white/90 hover:bg-emerald-50/20">
                    <td class="px-3 py-2 align-middle">
                        <input type="text" name="doctors[{{ $i }}][name]" value="{{ old("doctors.$i.name", $dr['name'] ?? '') }}"
                               data-doc-field="name"
                               class="w-full min-w-[8rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <input type="email" name="doctors[{{ $i }}][email]" value="{{ old("doctors.$i.email", $dr['email'] ?? '') }}"
                               data-doc-field="email"
                               class="w-full min-w-[10rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <input type="password" name="doctors[{{ $i }}][password]" value=""
                               data-doc-field="password" autocomplete="new-password"
                               class="w-full min-w-[7rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <input type="password" name="doctors[{{ $i }}][password_confirmation]" value=""
                               data-doc-field="password_confirmation" autocomplete="new-password"
                               class="w-full min-w-[7rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        @if ($departmentInputMode === 'text')
                            <input type="text" name="doctors[{{ $i }}][department_name]" value="{{ old("doctors.$i.department_name", $dr['department_name'] ?? '') }}"
                                   data-doc-field="department_name"
                                   class="w-full min-w-[8rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        @else
                            <select name="doctors[{{ $i }}][department_id]" data-doc-field="department_id"
                                    class="w-full min-w-[8rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                                <option value="">—</option>
                                @foreach ($departments as $dep)
                                    <option value="{{ $dep->id }}" @selected((string) old("doctors.$i.department_id", $dr['department_id'] ?? '') === (string) $dep->id)>{{ $dep->name }}</option>
                                @endforeach
                            </select>
                        @endif
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <input type="text" name="doctors[{{ $i }}][physical_clinic_name]" value="{{ old("doctors.$i.physical_clinic_name", $dr['physical_clinic_name'] ?? '') }}"
                               data-doc-field="physical_clinic_name" placeholder="Örn. Dahiliye Pol. 2"
                               class="w-full min-w-[9rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <input type="text" name="doctors[{{ $i }}][room_no]" value="{{ old("doctors.$i.room_no", $dr['room_no'] ?? '') }}"
                               data-doc-field="room_no" placeholder="Örn. A-204"
                               class="w-full min-w-[6rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle">
                        <input type="text" name="doctors[{{ $i }}][title]" value="{{ old("doctors.$i.title", $dr['title'] ?? '') }}"
                               data-doc-field="title" placeholder="İsteğe bağlı"
                               class="w-full min-w-[6rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    </td>
                    <td class="px-3 py-2 align-middle text-center">
                        <input type="checkbox" name="doctors[{{ $i }}][is_aile_hekimi]" value="1" data-doc-field="is_aile_hekimi"
                               class="h-4 w-4 rounded border-emerald-300"
                               @checked(old("doctors.$i.is_aile_hekimi", ! empty($dr['is_aile_hekimi'] ?? false)))>
                    </td>
                    <td class="px-3 py-2 align-middle text-right">
                        <button type="button" class="hospital-doc-remove rounded-xl border border-red-200 bg-red-50/80 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100/80 transition">
                            Kaldır
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<p class="mt-2 text-[11px] text-slate-500">E-posta doldurmadığınız satırlar yok sayılır. T.C. kimlik boşsa sistem benzersiz bir değer üretir.</p>

<div class="flex flex-wrap items-center gap-3 mt-3">
    <button type="button" id="{{ $docAddId }}" class="rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-2 text-sm font-semibold text-emerald-900 hover:bg-emerald-100/90 transition">
        Doktor satırı ekle
    </button>
</div>

<template id="{{ $docTplId }}">
    <tr class="hospital-doc-row bg-white/90 hover:bg-emerald-50/20">
        <td class="px-3 py-2 align-middle">
            <input type="text" data-doc-field="name"
                   class="w-full min-w-[8rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="email" data-doc-field="email"
                   class="w-full min-w-[10rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="password" data-doc-field="password" autocomplete="new-password"
                   class="w-full min-w-[7rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="password" data-doc-field="password_confirmation" autocomplete="new-password"
                   class="w-full min-w-[7rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            @if ($departmentInputMode === 'text')
                <input type="text" data-doc-field="department_name"
                       class="w-full min-w-[8rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
            @else
                <select data-doc-field="department_id"
                        class="w-full min-w-[8rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <option value="">—</option>
                    @foreach ($departments as $dep)
                        <option value="{{ $dep->id }}">{{ $dep->name }}</option>
                    @endforeach
                </select>
            @endif
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="text" data-doc-field="physical_clinic_name" placeholder="Örn. Dahiliye Pol. 2"
                   class="w-full min-w-[9rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="text" data-doc-field="room_no" placeholder="Örn. A-204"
                   class="w-full min-w-[6rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle">
            <input type="text" data-doc-field="title" placeholder="İsteğe bağlı"
                   class="w-full min-w-[6rem] rounded-xl border border-emerald-200 bg-white px-2 py-2 text-slate-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
        </td>
        <td class="px-3 py-2 align-middle text-center">
            <input type="checkbox" value="1" data-doc-field="is_aile_hekimi"
                   class="h-4 w-4 rounded border-emerald-300">
        </td>
        <td class="px-3 py-2 align-middle text-right">
            <button type="button" class="hospital-doc-remove rounded-xl border border-red-200 bg-red-50/80 px-2.5 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100/80 transition">
                Kaldır
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    (function () {
        var body = document.getElementById(@json($docBodyId));
        var tpl = document.getElementById(@json($docTplId));
        var addBtn = document.getElementById(@json($docAddId));
        var form = body && body.closest('form');
        if (!body || !tpl || !addBtn) return;

        function reindex() {
            var rows = body.querySelectorAll('.hospital-doc-row');
            rows.forEach(function (tr, i) {
                tr.querySelectorAll('[data-doc-field]').forEach(function (el) {
                    var f = el.getAttribute('data-doc-field');
                    if (!f) return;
                    el.name = 'doctors[' + i + '][' + f + ']';
                });
            });
        }

        function bindRemove(tr) {
            var btn = tr.querySelector('.hospital-doc-remove');
            if (!btn) return;
            btn.addEventListener('click', function () {
                tr.remove();
                if (body.querySelectorAll('.hospital-doc-row').length === 0) {
                    addBtn.click();
                }
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

        body.querySelectorAll('.hospital-doc-row').forEach(bindRemove);
        addBtn.addEventListener('click', addRow);
        if (form) {
            form.addEventListener('submit', reindex);
        }
    })();
</script>
@endpush
