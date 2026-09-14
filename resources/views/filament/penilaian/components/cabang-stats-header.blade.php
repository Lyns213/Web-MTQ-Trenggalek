@php
    $activeTahun = \App\Models\Tahun::where('is_active', true)->first();
    $tahunId = session('selected_tahun_id', $activeTahun?->id);

    $isGrup = ($modelClass === \App\Models\NilaiMfq::class);

    if ($isGrup) {
        $query = $modelClass::query()
            ->join('grups', 'nilai_mfqs.grup_id', '=', 'grups.id');
        if ($tahunId) {
            $query->where('grups.tahun_id', $tahunId);
        }
        $total = (clone $query)->count();
        $putra = (clone $query)->where('grups.jenis_kelamin', 'like', '%putra%')->count();
        $putri = (clone $query)->where('grups.jenis_kelamin', 'like', '%putri%')->count();
        $unitLabel = 'Regu';
        $putraLabel = 'Regu Putra';
        $putriLabel = 'Regu Putri';
    } else {
        $table = (new $modelClass)->getTable();
        $query = $modelClass::query()
            ->join('pesertas', "{$table}.peserta_id", '=', 'pesertas.id');
        if ($tahunId) {
            $query->where('pesertas.tahun_id', $tahunId);
        }
        $total = (clone $query)->count();
        $putra = (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putra%')->count();
        $putri = (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putri%')->count();
        $unitLabel = 'Peserta';
        $putraLabel = 'Putra';
        $putriLabel = 'Putri';
    }
@endphp

<div class="fi-cabang-stats-group" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 4px 0;">
    {{-- Card Total Peserta --}}
    <div style="display: flex; align-items: center; gap: 10px; background: rgba(212, 175, 55, 0.08); border: 1px solid rgba(212, 175, 55, 0.35); border-radius: 10px; padding: 7px 14px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25); transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.borderColor='#e5b958'; this.style.transform='translateY(-1px)';" onmouseout="this.style.borderColor='rgba(212, 175, 55, 0.35)'; this.style.transform='translateY(0)';">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #d4af37 0%, #a67c1e 100%); display: flex; align-items: center; justify-content: center; color: #071524; flex-shrink: 0; box-shadow: 0 2px 6px rgba(212, 175, 55, 0.35);">
            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
        </div>
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 10px; font-weight: 800; color: #e5b958; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">
                Total Pendaftar
            </span>
            <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                <span style="font-size: 17px; font-weight: 900; color: #ffffff; line-height: 1; font-family: monospace;">
                    {{ $total }}
                </span>
                <span style="font-size: 11.5px; font-weight: 700; color: #cbd5e1;">
                    {{ $unitLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Card Laki-laki (Putra) --}}
    <div style="display: flex; align-items: center; gap: 10px; background: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.35); border-radius: 10px; padding: 7px 14px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25); transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.borderColor='#38bdf8'; this.style.transform='translateY(-1px)';" onmouseout="this.style.borderColor='rgba(56, 189, 248, 0.35)'; this.style.transform='translateY(0)';">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #38bdf8 0%, #0284c7 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; box-shadow: 0 2px 6px rgba(56, 189, 248, 0.35);">
            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 10px; font-weight: 800; color: #38bdf8; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">
                Laki-Laki
            </span>
            <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                <span style="font-size: 17px; font-weight: 900; color: #ffffff; line-height: 1; font-family: monospace;">
                    {{ $putra }}
                </span>
                <span style="font-size: 11.5px; font-weight: 700; color: #bae6fd;">
                    {{ $putraLabel }}
                </span>
            </div>
        </div>
    </div>

    {{-- Card Perempuan (Putri) --}}
    <div style="display: flex; align-items: center; gap: 10px; background: rgba(244, 114, 182, 0.08); border: 1px solid rgba(244, 114, 182, 0.35); border-radius: 10px; padding: 7px 14px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.25); transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.borderColor='#f472b6'; this.style.transform='translateY(-1px)';" onmouseout="this.style.borderColor='rgba(244, 114, 182, 0.35)'; this.style.transform='translateY(0)';">
        <div style="width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg, #f472b6 0%, #db2777 100%); display: flex; align-items: center; justify-content: center; color: #ffffff; flex-shrink: 0; box-shadow: 0 2px 6px rgba(244, 114, 182, 0.35);">
            <svg style="width: 17px; height: 17px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div style="display: flex; flex-direction: column;">
            <span style="font-size: 10px; font-weight: 800; color: #f472b6; text-transform: uppercase; letter-spacing: 0.8px; line-height: 1;">
                Perempuan
            </span>
            <div style="display: flex; align-items: baseline; gap: 4px; margin-top: 2px;">
                <span style="font-size: 17px; font-weight: 900; color: #ffffff; line-height: 1; font-family: monospace;">
                    {{ $putri }}
                </span>
                <span style="font-size: 11.5px; font-weight: 700; color: #fbcfe8;">
                    {{ $putriLabel }}
                </span>
            </div>
        </div>
    </div>
</div>
