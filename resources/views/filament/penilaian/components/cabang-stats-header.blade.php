@php
    $activeTahun = \App\Models\Tahun::where('is_active', true)->first();
    $tahunId = session('selected_tahun_id', $activeTahun?->id);

    $isGrup = ($modelClass === \App\Models\NilaiMfq::class);

    $statsCacheKey = 'cabang_stats_' . md5($modelClass . '_' . ($tahunId ?? '0'));
    $stats = \Illuminate\Support\Facades\Cache::remember($statsCacheKey, 30, function () use ($modelClass, $tahunId, $isGrup) {
        if ($isGrup) {
            $query = $modelClass::query()
                ->join('grups', 'nilai_mfqs.grup_id', '=', 'grups.id');
            if ($tahunId) {
                $query->where('grups.tahun_id', $tahunId);
            }
            return [
                'total' => (clone $query)->count(),
                'putra' => (clone $query)->where('grups.jenis_kelamin', 'like', '%putra%')->count(),
                'putri' => (clone $query)->where('grups.jenis_kelamin', 'like', '%putri%')->count(),
                'unitLabel' => 'Regu',
                'putraLabel' => 'Regu Putra',
                'putriLabel' => 'Regu Putri',
            ];
        } else {
            $table = (new $modelClass)->getTable();
            $query = $modelClass::query()
                ->join('pesertas', "{$table}.peserta_id", '=', 'pesertas.id');
            if ($tahunId) {
                $query->where('pesertas.tahun_id', $tahunId);
            }
            return [
                'total' => (clone $query)->count(),
                'putra' => (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putra%')->count(),
                'putri' => (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putri%')->count(),
                'unitLabel' => 'Peserta',
                'putraLabel' => 'Putra',
                'putriLabel' => 'Putri',
            ];
        }
    });

    $total = $stats['total'];
    $putra = $stats['putra'];
    $putri = $stats['putri'];
    $unitLabel = $stats['unitLabel'];
    $putraLabel = $stats['putraLabel'];
    $putriLabel = $stats['putriLabel'];
@endphp

<style>
.fi-header-stat-card {
    display: flex;
    align-items: center;
    gap: 10px;
    border-radius: 10px;
    padding: 7px 14px;
    transition: transform 0.2s, border-color 0.2s, box-shadow 0.2s;
    background: #ffffff;
}
.fi-header-stat-total {
    border: 1.5px solid #fcd34d;
    box-shadow: 0 2px 8px rgba(217, 119, 6, 0.08);
}
.fi-header-stat-total:hover {
    border-color: #d97706;
    transform: translateY(-1px);
}
.fi-header-stat-total .fi-header-stat-title {
    color: #b45309;
}
.fi-header-stat-total .fi-header-stat-num {
    color: #0f172a;
}
.fi-header-stat-total .fi-header-stat-unit {
    color: #64748b;
}

.fi-header-stat-putra {
    border: 1.5px solid #7dd3fc;
    box-shadow: 0 2px 8px rgba(2, 132, 199, 0.08);
}
.fi-header-stat-putra:hover {
    border-color: #0284c7;
    transform: translateY(-1px);
}
.fi-header-stat-putra .fi-header-stat-title {
    color: #0284c7;
}
.fi-header-stat-putra .fi-header-stat-num {
    color: #0369a1;
}
.fi-header-stat-putra .fi-header-stat-unit {
    color: #0284c7;
}

.fi-header-stat-putri {
    border: 1.5px solid #f9a8d4;
    box-shadow: 0 2px 8px rgba(225, 29, 72, 0.08);
}
.fi-header-stat-putri:hover {
    border-color: #e11d48;
    transform: translateY(-1px);
}
.fi-header-stat-putri .fi-header-stat-title {
    color: #db2777;
}
.fi-header-stat-putri .fi-header-stat-num {
    color: #be185d;
}
.fi-header-stat-putri .fi-header-stat-unit {
    color: #db2777;
}

/* DARK MODE */
html.dark .fi-header-stat-total {
    background: rgba(212, 175, 55, 0.08);
    border-color: rgba(212, 175, 55, 0.35);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}
html.dark .fi-header-stat-total .fi-header-stat-title {
    color: #e5b958;
}
html.dark .fi-header-stat-total .fi-header-stat-num {
    color: #ffffff;
}
html.dark .fi-header-stat-total .fi-header-stat-unit {
    color: #cbd5e1;
}

html.dark .fi-header-stat-putra {
    background: rgba(56, 189, 248, 0.08);
    border-color: rgba(56, 189, 248, 0.35);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}
html.dark .fi-header-stat-putra .fi-header-stat-title {
    color: #38bdf8;
}
html.dark .fi-header-stat-putra .fi-header-stat-num {
    color: #38bdf8;
}
html.dark .fi-header-stat-putra .fi-header-stat-unit {
    color: #bae6fd;
}

html.dark .fi-header-stat-putri {
    background: rgba(244, 114, 182, 0.08);
    border-color: rgba(244, 114, 182, 0.35);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25);
}
html.dark .fi-header-stat-putri .fi-header-stat-title {
    color: #f472b6;
}
html.dark .fi-header-stat-putri .fi-header-stat-num {
    color: #f472b6;
}
html.dark .fi-header-stat-putri .fi-header-stat-unit {
    color: #fbcfe8;
}
</style>

<div class="fi-cabang-stats-group" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 4px 0;">
    {{-- Card Total Peserta --}}
    <div class="fi-header-stat-card fi-header-stat-total">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #d4af37 0%, #a67c1e 100%); display: flex; align-items: center; justify-content: center; color: #071524; flex-shrink: 0; box-shadow: 0 2px 6px rgba(212, 175, 55, 0.35);">
            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div style="display: flex; flex-direction: column;">
            <span class="fi-header-stat-title" style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">
                Total Pendaftar
            </span>
            <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                <span class="fi-header-stat-num" style="font-size: 17px; font-weight: 900; line-height: 1; font-family: monospace;">
                    {{ $total }}
                </span>
                <span class="fi-header-stat-unit" style="font-size: 11.5px; font-weight: 700;">
                    {{ $unitLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Card Laki-laki (Putra) --}}
    <div class="fi-header-stat-card fi-header-stat-putra">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; box-shadow: 0 2px 6px rgba(56, 189, 248, 0.35);">
            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div style="display: flex; flex-direction: column;">
            <span class="fi-header-stat-title" style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">
                Laki-Laki
            </span>
            <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                <span class="fi-header-stat-num" style="font-size: 17px; font-weight: 900; line-height: 1; font-family: monospace;">
                    {{ $putra }}
                </span>
                <span class="fi-header-stat-unit" style="font-size: 11.5px; font-weight: 700;">
                    {{ $putraLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Card Perempuan (Putri) --}}
    <div class="fi-header-stat-card fi-header-stat-putri">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #f472b6 0%, #db2777 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; box-shadow: 0 2px 6px rgba(244, 114, 182, 0.35);">
            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div style="display: flex; flex-direction: column;">
            <span class="fi-header-stat-title" style="font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">
                Perempuan
            </span>
            <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                <span class="fi-header-stat-num" style="font-size: 17px; font-weight: 900; line-height: 1; font-family: monospace;">
                    {{ $putri }}
                </span>
                <span class="fi-header-stat-unit" style="font-size: 11.5px; font-weight: 700;">
                    {{ $putriLabel }}
                </span>
            </div>
        </div>
    </div>
</div>
