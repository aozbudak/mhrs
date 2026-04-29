@php
    $requiredDeptIdsMuayene = $requiredDeptIdsMuayene ?? [];
@endphp

@if (! empty($poliklinikSaatleri))
    <div class="border-t border-sky-100 pt-6 space-y-4" data-poliklinik-saat-root="1">
        <div>
            <h2 class="text-sm font-extrabold text-slate-900">Poliklinik (birim) muayene saatleri</h2>
            <p class="mt-1 text-xs text-slate-600 leading-relaxed">
                Her birim (ör. <strong class="font-semibold text-slate-800">Dermatoloji (Cildiye)</strong>) için hafta içi muayene aralığını ve randevu dilimini <strong class="font-semibold text-slate-800">ayrı ayrı</strong> girin.
                O birimde doktor varsa saatler zorunludur; doktoru olmayan birimleri boş bırakabilirsiniz.
            </p>
        </div>

        @foreach ($poliklinikSaatleri as $block)
            @php
                $deptMuayeneRequired = in_array($block['dept_id'], $requiredDeptIdsMuayene, true);
            @endphp
            <div class="rounded-2xl border border-indigo-100/90 bg-indigo-50/20 p-4 space-y-3"
                 data-poliklinik-dept-block="1"
                 data-poliklinik-dept-id="{{ $block['dept_id'] }}"
                 data-poliklinik-dept-name="{{ mb_strtolower(trim((string) $block['department']->name), 'UTF-8') }}">
                <h3 class="text-xs font-extrabold uppercase tracking-wide text-indigo-950">{{ $block['department']->name }}</h3>
                @include('admin.partials.muayene-saat-kutusu', [
                    'idSuffix' => 'Dept'.$block['dept_id'],
                    'label' => null,
                    'hint' => 'Bu birimin kendi muayene programıdır; diğer birimlerden bağımsızdır.',
                    'ogleOnceBaslangicName' => 'dept_muayene_simple['.$block['dept_id'].'][ogle_once_baslangic]',
                    'ogleOnceBitisName' => 'dept_muayene_simple['.$block['dept_id'].'][ogle_once_bitis]',
                    'ogleSonraBaslangicName' => 'dept_muayene_simple['.$block['dept_id'].'][ogle_sonra_baslangic]',
                    'ogleSonraBitisName' => 'dept_muayene_simple['.$block['dept_id'].'][ogle_sonra_bitis]',
                    'ogleOnceBaslangicValue' => old('dept_muayene_simple.'.$block['dept_id'].'.ogle_once_baslangic', $block['ogle_once_baslangic']),
                    'ogleOnceBitisValue' => old('dept_muayene_simple.'.$block['dept_id'].'.ogle_once_bitis', $block['ogle_once_bitis']),
                    'ogleSonraBaslangicValue' => old('dept_muayene_simple.'.$block['dept_id'].'.ogle_sonra_baslangic', $block['ogle_sonra_baslangic']),
                    'ogleSonraBitisValue' => old('dept_muayene_simple.'.$block['dept_id'].'.ogle_sonra_bitis', $block['ogle_sonra_bitis']),
                    'slotMinutes' => (int) ($block['slot_dakika'] ?? 30),
                    'showSlotDakika' => true,
                    'slotDakikaName' => 'dept_randevu_slot_dakika['.$block['dept_id'].']',
                    'slotDakikaValue' => old('dept_randevu_slot_dakika.'.$block['dept_id'], $block['slot_dakika'] ?? 30),
                    'required' => $deptMuayeneRequired,
                ])
            </div>
        @endforeach

        <p class="hidden text-xs font-medium text-amber-800 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2"
           data-poliklinik-empty-info="1">
            Eklenen doktor birimiyle eşleşen poliklinik bulunamadı. Birim adını aktif birim listesiyle aynı girin.
        </p>
    </div>
@endif

@once
    @push('scripts')
        <script>
            (function () {
                function norm(s) {
                    return (s || '').toString().trim().toLocaleLowerCase('tr-TR');
                }

                function hasRowSignal(row) {
                    var f = row.querySelectorAll('[data-doc-field]');
                    for (var i = 0; i < f.length; i++) {
                        var el = f[i];
                        if (!el) continue;
                        if (el.type === 'checkbox') {
                            if (el.checked) return true;
                            continue;
                        }
                        if (norm(el.value) !== '') return true;
                    }
                    return false;
                }

                function collectSelectedDepartments(docBody) {
                    var rows = docBody.querySelectorAll('.hospital-doc-row');
                    var hasAnyInput = false;
                    var byId = {};
                    var byName = {};

                    rows.forEach(function (row) {
                        if (hasRowSignal(row)) hasAnyInput = true;

                        row.querySelectorAll('[data-doc-field="department_id"]').forEach(function (el) {
                            var v = norm(el.value);
                            if (v !== '') byId[v] = true;
                        });

                        row.querySelectorAll('[data-doc-field="department_name"]').forEach(function (el) {
                            var v = norm(el.value);
                            if (v !== '') byName[v] = true;
                        });
                    });

                    return {
                        hasAnyInput: hasAnyInput,
                        byId: byId,
                        byName: byName,
                    };
                }

                function applyDeptFilter(root, selected) {
                    var blocks = root.querySelectorAll('[data-poliklinik-dept-block]');
                    var visibleCount = 0;
                    var hasFilter = Object.keys(selected.byId).length > 0 || Object.keys(selected.byName).length > 0;

                    blocks.forEach(function (block) {
                        var show = true;
                        var idKey = norm(block.getAttribute('data-poliklinik-dept-id'));
                        var nameKey = norm(block.getAttribute('data-poliklinik-dept-name'));

                        if (selected.hasAnyInput && hasFilter) {
                            show = !!selected.byId[idKey] || !!selected.byName[nameKey];
                        }

                        block.classList.toggle('hidden', !show);
                        if (show) visibleCount++;
                    });

                    var emptyInfo = root.querySelector('[data-poliklinik-empty-info]');
                    if (emptyInfo) {
                        var showEmpty = selected.hasAnyInput && hasFilter && visibleCount === 0;
                        emptyInfo.classList.toggle('hidden', !showEmpty);
                    }
                }

                function boot(root) {
                    var docBody = document.getElementById('hospitalDoctorsBody');
                    if (!docBody) return;

                    var run = function () {
                        var selected = collectSelectedDepartments(docBody);
                        applyDeptFilter(root, selected);
                    };

                    docBody.addEventListener('input', run, true);
                    docBody.addEventListener('change', run, true);
                    run();
                }

                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelectorAll('[data-poliklinik-saat-root]').forEach(boot);
                });
            })();
        </script>
    @endpush
@endonce
