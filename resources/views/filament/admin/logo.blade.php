@if(Auth::check())
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/logotgx.png') }}" alt="Logo" style="height: 38px; width: auto; object-fit: contain;">
        <div class="flex flex-col text-left">
            <span class="font-bold text-sm leading-tight text-gray-900 dark:text-white">Admin MTQ Trenggalek</span>
            <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">Panel Administrator</span>
        </div>
    </div>
@else
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; width: 100%; margin: 0 auto;">
        <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 14px;">
            <img src="{{ asset('images/logo_trenggalek.png') }}" alt="Trenggalek" style="height: 68px !important; width: auto; object-fit: contain; filter: drop-shadow(0 4px 12px rgba(0,0,0,0.6));">
        </div>
        <h2 style="font-family: 'Montserrat', sans-serif; font-size: 13px; font-weight: 800; letter-spacing: 2px; color: #e5b958; text-transform: uppercase; margin: 0; text-shadow: 0 2px 6px rgba(0,0,0,0.4);">
            MUSABAQAH TILAWATIL QUR'AN
        </h2>
        <h1 style="font-family: 'Montserrat', sans-serif; font-size: 19px; font-weight: 900; letter-spacing: 1.2px; color: #ffffff; text-transform: uppercase; margin: 4px 0 8px 0; text-shadow: 0 2px 8px rgba(0,0,0,0.5);">
            KABUPATEN TRENGGALEK 2026
        </h1>
        <div style="display: inline-flex; align-items: center; gap: 7px; padding: 5px 16px; border-radius: 20px; background: rgba(229, 185, 88, 0.12); border: 1px solid rgba(229, 185, 88, 0.35); margin-bottom: 8px;">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px #10b981;"></span>
            <span style="font-family: 'Montserrat', sans-serif; font-size: 11px; font-weight: 800; color: #ffe082; letter-spacing: 1.5px; text-transform: uppercase;">
                PORTAL RESMI ADMINISTRATOR
            </span>
        </div>
    </div>
@endif
