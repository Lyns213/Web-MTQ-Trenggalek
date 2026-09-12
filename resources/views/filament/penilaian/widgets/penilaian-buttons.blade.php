<x-filament-widgets::widget>
<div style="font-family: 'Montserrat', sans-serif;">
    {{-- Header --}}
    <div style="margin-bottom: 18px; padding-bottom: 14px; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
        <div>
            <div style="font-size: 11px; font-weight: 800; letter-spacing: 1.5px; color: #c8a34d; text-transform: uppercase;">
                MENU PENILAIAN
            </div>
            <h2 style="font-size: 18px; font-weight: 900; color: #0c233c; margin: 3px 0 0 0; text-transform: uppercase; letter-spacing: 0.5px;">
                RUANG PENILAIAN DEWAN HAKIM
            </h2>
        </div>
        <div style="font-size: 12px; font-weight: 600; color: #64748b;">
            Pilih cabang untuk menginput nilai peserta
        </div>
    </div>

    {{-- Cards Grid --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 14px;">
        @foreach($accessibleResources as $r)
        <a href="{{ $r['url'] }}" style="display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; background: #ffffff; border: 1.5px solid #e2e8f0; border-radius: 12px; text-decoration: none; color: #0c233c; transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 8px rgba(0,0,0,0.03); position: relative; overflow: hidden;" onmouseover="this.style.borderColor='#d4af37'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(212,175,55,0.18)'" onmouseout="this.style.borderColor='#e2e8f0'; this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(0,0,0,0.03)'">
            {{-- Left Accent Bar --}}
            <div style="position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: linear-gradient(180deg, #d4af37, #0c233c);"></div>

            <div style="display: flex; align-items: center; gap: 14px; padding-left: 6px;">
                <div style="width: 44px; height: 44px; border-radius: 10px; background: linear-gradient(135deg, #0d2238 0%, #15385c 100%); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 3px 8px rgba(13,34,56,0.25);">
                    <svg style="width: 22px; height: 22px; color: #ffe082;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div>
                    <div style="font-size: 15px; font-weight: 800; color: #0c233c; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ $r['label'] }}
                    </div>
                    <div style="font-size: 11px; font-weight: 600; color: #64748b; margin-top: 2px;">
                        Input Nilai & Rekapitulasi
                    </div>
                </div>
            </div>

            <div style="width: 32px; height: 32px; border-radius: 50%; background: #f8fafc; border: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                <svg style="width: 16px; height: 16px; color: #0c233c;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @endforeach
    </div>
</div>
</x-filament-widgets::widget>
