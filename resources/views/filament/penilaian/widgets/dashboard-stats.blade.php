<x-filament-widgets::widget>
@if($stats)
<div style="font-family: 'Montserrat', sans-serif; margin-bottom: 24px;">
    {{-- Header Banner Card --}}
    <div style="background: linear-gradient(135deg, #0d233a 0%, #061525 100%); border: 1.5px solid #c9a34b; border-radius: 16px; padding: 22px 26px; margin-bottom: 20px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25), 0 0 20px rgba(201, 163, 75, 0.1); display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px; position: relative; overflow: hidden;">
        {{-- Gold highlight --}}
        <div style="position: absolute; top: 0; left: 10%; right: 10%; height: 2px; background: linear-gradient(90deg, transparent, #ffe082, transparent);"></div>

        <div style="display: flex; align-items: center; gap: 16px;">
            <div style="width: 52px; height: 52px; border-radius: 12px; background: rgba(212, 175, 55, 0.15); border: 1.5px solid #d4af37; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);">
                <svg style="width: 26px; height: 26px; color: #ffe082;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 11px; font-weight: 800; letter-spacing: 1.5px; color: #e5b958; text-transform: uppercase;">
                        DEWAN HAKIM &bull; PORTAL PENILAIAN
                    </span>
                    <span style="background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.4); color: #34d399; font-size: 10px; font-weight: 800; padding: 2px 8px; border-radius: 4px; letter-spacing: 1px;">
                        TAHUN {{ $stats['tahun'] ?? 2026 }}
                    </span>
                </div>
                <h2 style="font-size: 22px; font-weight: 900; color: #ffffff; text-transform: uppercase; letter-spacing: 0.8px; margin: 4px 0 0 0;">
                    CABANG {{ $cabangLabel }}
                </h2>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
            @if(!empty($cabangSlug))
            <a href="/live/{{ $cabangSlug }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: rgba(229, 57, 53, 0.15); border: 1.5px solid #e53935; color: #ffffff; padding: 10px 18px; border-radius: 10px; font-size: 12.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; transition: all 0.2s;" onmouseover="this.style.background='#e53935'; this.style.boxShadow='0 0 16px rgba(229, 57, 53, 0.6)'" onmouseout="this.style.background='rgba(229, 57, 53, 0.15)'; this.style.boxShadow='none'">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #ff4d4d; box-shadow: 0 0 8px #ff4d4d;"></span>
                Layar Live Score
            </a>
            @endif
        </div>
    </div>

    {{-- Stats Cards Grid (3 Columns) --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
        {{-- Card 1: Total Peserta --}}
        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 22px 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: transform 0.2s, border-color 0.2s; position: relative;" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#d4af37';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                <span style="font-size: 12px; font-weight: 800; color: #64748b; letter-spacing: 1px; text-transform: uppercase;">
                    TOTAL PESERTA
                </span>
                <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #0d2238 0%, #173d63 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(13, 34, 56, 0.25);">
                    <svg style="width: 20px; height: 20px; color: #ffe082;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 38px; font-weight: 900; color: #0c233c; line-height: 1; letter-spacing: -0.5px;">
                {{ $stats['total'] }}
            </div>
            <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 10px; font-size: 12px; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                <span>Sudah Dinilai: <strong style="color: #059669;">{{ $stats['sudah_dinilai'] ?? 0 }}</strong></span>
                <span>Belum: <strong style="color: #ea580c;">{{ $stats['belum_dinilai'] ?? 0 }}</strong></span>
            </div>
        </div>

        {{-- Card 2: Peserta Putra --}}
        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 22px 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#10b981';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                <span style="font-size: 12px; font-weight: 800; color: #64748b; letter-spacing: 1px; text-transform: uppercase;">
                    PESERTA PUTRA
                </span>
                <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #059669 0%, #10b981 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(5, 150, 105, 0.25);">
                    <svg style="width: 20px; height: 20px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 38px; font-weight: 900; color: #065f46; line-height: 1; letter-spacing: -0.5px;">
                {{ $stats['laki'] }}
            </div>
            <div style="margin-top: 10px; font-size: 12px; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                @php $putraPct = $stats['total'] > 0 ? round(($stats['laki'] / $stats['total']) * 100, 1) : 0; @endphp
                <span>Proporsi: <strong style="color: #059669;">{{ $putraPct }}%</strong> dari total</span>
            </div>
        </div>

        {{-- Card 3: Peserta Putri --}}
        <div style="background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 22px 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.04); transition: transform 0.2s, border-color 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.borderColor='#e11d48';" onmouseout="this.style.transform='translateY(0)'; this.style.borderColor='#e2e8f0';">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px;">
                <span style="font-size: 12px; font-weight: 800; color: #64748b; letter-spacing: 1px; text-transform: uppercase;">
                    PESERTA PUTRI
                </span>
                <div style="width: 42px; height: 42px; border-radius: 10px; background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(225, 29, 72, 0.25);">
                    <svg style="width: 20px; height: 20px; color: #ffffff;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
            <div style="font-size: 38px; font-weight: 900; color: #9f1239; line-height: 1; letter-spacing: -0.5px;">
                {{ $stats['perempuan'] }}
            </div>
            <div style="margin-top: 10px; font-size: 12px; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 8px;">
                @php $putriPct = $stats['total'] > 0 ? round(($stats['perempuan'] / $stats['total']) * 100, 1) : 0; @endphp
                <span>Proporsi: <strong style="color: #e11d48;">{{ $putriPct }}%</strong> dari total</span>
            </div>
        </div>
    </div>

    {{-- Ratio Bar --}}
    @if($stats['total'] > 0)
    <div style="margin-top: 16px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 14px; padding: 16px 20px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
            <span style="font-size: 12px; font-weight: 800; color: #0c233c; letter-spacing: 0.8px; text-transform: uppercase;">
                DISTRIBUSI GENDER PESERTA
            </span>
            <span style="font-size: 12px; font-weight: 700; color: #64748b;">
                {{ $stats['total'] }} Peserta Terdaftar
            </span>
        </div>
        <div style="display: flex; height: 12px; border-radius: 8px; overflow: hidden; background: #e2e8f0;">
            <div style="width: {{ $putraPct }}%; background: linear-gradient(90deg, #059669, #10b981); transition: width 0.8s ease;" title="Putra: {{ $putraPct }}%"></div>
            <div style="width: {{ $putriPct }}%; background: linear-gradient(90deg, #e11d48, #f43f5e); transition: width 0.8s ease;" title="Putri: {{ $putriPct }}%"></div>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 8px;">
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #059669;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981;"></span> Putra: {{ $stats['laki'] }} ({{ $putraPct }}%)
            </span>
            <span style="display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 700; color: #e11d48;">
                <span style="width: 8px; height: 8px; border-radius: 50%; background: #f43f5e;"></span> Putri: {{ $stats['perempuan'] }} ({{ $putriPct }}%)
            </span>
        </div>
    </div>
    @endif
</div>
@else
<div style="background: #ffffff; border: 1.5px solid #f59e0b; border-radius: 12px; padding: 20px; text-align: center; color: #92400e; font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 700;">
    Belum ada data penilaian aktif untuk cabang ini.
</div>
@endif
</x-filament-widgets::widget>
