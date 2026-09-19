@php
    $slug = strtolower($slug ?? 'tartil');
    $cfg = $cfg ?? (\App\Http\Controllers\LiveScoreController::$config[$slug] ?? \App\Http\Controllers\LiveScoreController::$config['tartil']);
    $curr = $initialData['current'] ?? null;
    $timer = $initialData['timer'] ?? null;
    $total = (float)($curr['total'] ?? 0);
    $cabangLabel = $curr['cabang'] ?? ($initialData['cabang'] ?? $cfg['label']);
    $fields = !empty($curr['fields']) ? $curr['fields'] : ($initialData['fields'] ?? []);
    if (empty($fields) && !empty($cfg['fields'])) {
        foreach ($cfg['fields'] as $f) {
            $fields[] = [
                'key' => $f['key'],
                'label' => $f['label'],
                'value' => 0.0,
                'max' => $f['max'] ?? 100,
                'pct' => 0.0,
            ];
        }
    }
    $participants = $initialData['participants'] ?? [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Score Board - MTQ Kabupaten Trenggalek 2026 - Cabang {{ $cabangLabel }}</title>
    <link rel="icon" href="{{ asset('images/logotgxmini.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            background: #000814;
            font-family: 'Montserrat', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* 16:9 Broadcast Stage Canvas (1376 x 768) */
        .stage-canvas {
            width: 1376px;
            height: 768px;
            position: relative;
            background: url("{{ asset('images/asset_mtq_bg_template.jpg') }}?v={{ filemtime(public_path('images/asset_mtq_bg_template.jpg')) }}") no-repeat center center;
            background-size: 1376px 768px;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 0 50px rgba(0,0,0,0.8);
            transform-origin: center center;
        }

        /* ===== PARTICIPANT CARD ===== */
        .card-participant {
            position: absolute;
            left: 60px;
            top: 206px;
            width: 290px;
            height: 446px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .participant-photo-wrap {
            width: 290px;
            height: 236px;
            background: #0d1e30;
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .participant-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center 15%;
            display: block;
            transition: opacity 0.25s ease;
        }

        .participant-photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0a1f35, #143557);
            color: #d4af37;
            font-size: 76px;
            transition: opacity 0.25s ease;
        }

        .participant-navy-box {
            background: linear-gradient(180deg, #0d2843 0%, #061628 100%);
            border-top: 4px solid #caa44e;
            padding: 12px 16px 8px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 134px;
            flex-shrink: 0;
            position: relative;
        }

        .participant-navy-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, #ffe082, #caa44e, #9e7a2b);
        }

        .participant-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800;
            font-size: 20px;
            line-height: 1.18;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            transition: opacity 0.2s ease;
        }

        .participant-sep {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(214, 175, 87, 0.5), transparent);
            margin: 5px 0 4px;
        }

        .participant-number {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 12px;
            color: #e5b958;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            transition: opacity 0.2s ease;
        }

        .participant-white-box {
            background: #ffffff;
            padding: 0 18px;
            display: flex;
            align-items: center;
            height: 76px;
            flex-shrink: 0;
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .participant-origin-text {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.25;
            text-align: left;
            align-items: flex-start;
            transition: opacity 0.2s ease;
        }

        .origin-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 10.5px;
            font-weight: 800;
            color: #111111;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .origin-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 19px;
            font-weight: 900;
            color: #0c233c;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ===== SCORE CARD ===== */
        .card-score {
            position: absolute;
            left: 366px;
            top: 206px;
            width: 534px;
            height: 446px;
            background: linear-gradient(180deg, #0d2843 0%, #0a2037 40%, #061626 100%);
            border: 2px solid #527598;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            padding: 14px 22px 16px 22px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 10;
        }

        /* Top golden highlight line */
        .card-score::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #ffe082, transparent);
        }

        .score-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .timer-group {
            display: flex;
            align-items: baseline;
        }

        .timer-text-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .timer-digits {
            font-family: 'Montserrat', sans-serif;
            font-size: 52px;
            font-weight: 800;
            color: #ffe082;
            margin-left: 12px;
            font-variant-numeric: tabular-nums;
            letter-spacing: 1px;
            line-height: 1;
            text-shadow: 0 0 18px rgba(255, 224, 130, 0.65), 0 0 35px rgba(212, 175, 55, 0.4);
            transition: color 0.25s ease, text-shadow 0.25s ease;
        }

        .timer-digits.timer-yellow {
            color: #ffd600 !important;
            text-shadow: 0 0 18px rgba(255, 214, 0, 0.8), 0 0 35px rgba(255, 193, 7, 0.45) !important;
            animation: none !important;
        }

        .timer-digits.timer-green {
            color: #00e676 !important;
            text-shadow: 0 0 18px rgba(0, 230, 118, 0.75), 0 0 35px rgba(0, 230, 118, 0.45) !important;
            animation: none !important;
        }

        .timer-digits.timer-red {
            color: #ff334b !important;
            text-shadow: 0 0 20px rgba(255, 51, 75, 0.85), 0 0 38px rgba(255, 51, 75, 0.5) !important;
            animation: pulse-timer-red 1s infinite alternate ease-in-out !important;
        }

        @keyframes pulse-timer-red {
            from { transform: scale(1); }
            to { transform: scale(1.03); }
        }

        .badge-live-top {
            background: #e53935;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            letter-spacing: 1px;
            box-shadow: 0 2px 10px rgba(229, 57, 53, 0.6);
        }

        .dot-blink {
            width: 8px;
            height: 8px;
            background: #ffffff;
            border-radius: 50%;
            animation: pulse-dot 1.2s infinite ease-in-out;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.3; transform: scale(0.65); }
        }

        .cabang-subtitle {
            font-family: 'Montserrat', sans-serif;
            font-size: 13.5px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
            margin-bottom: 4px;
        }
        .cabang-gold { color: #e5b958; }
        .cabang-white { color: #ffffff; }

        /* Score items */
        .score-field {
            display: flex;
            flex-direction: column;
            margin-bottom: 3px;
        }

        .score-field-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 3px;
        }

        .score-field-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .score-field-num {
            font-family: 'Montserrat', sans-serif;
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
        }

        .score-track {
            width: 100%;
            height: 13px;
            background: #040e19;
            border: 2px solid #b8923e;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.5);
        }

        .score-fill {
            height: 100%;
            background: linear-gradient(90deg, #008cb5 0%, #00d4aa 50%, #00f0c8 100%);
            border-radius: 20px;
            box-shadow: 0 0 12px rgba(0, 212, 170, 0.7);
            transition: width 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* TOTAL SKOR BOX */
        .total-container {
            position: relative;
            background: linear-gradient(180deg, #0b2238 0%, #051423 100%);
            border: 2px solid #c9a34b;
            border-radius: 12px;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 4px;
            height: 62px;
            box-shadow: 0 0 18px rgba(201, 163, 75, 0.25), inset 0 1px 1px rgba(255, 255, 255, 0.15);
        }

        .total-container::before {
            content: '';
            position: absolute;
            top: -1px;
            left: 20%;
            right: 20%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #ffe082, transparent);
        }

        .badge-live-update {
            position: absolute;
            top: -10px;
            right: 20px;
            background: #e53935;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            padding: 2px 10px;
            border-radius: 4px;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(229, 57, 53, 0.5);
        }

        .total-text-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 24px;
            font-weight: 800;
            color: #e5b958;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .total-text-digits {
            font-family: 'Montserrat', sans-serif;
            font-size: 54px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
            line-height: 1;
            text-shadow: 0 0 20px rgba(255, 235, 160, 0.85), 0 0 38px rgba(212, 175, 55, 0.45);
        }

        /* ===== LEADERBOARD / PESERTA LAIN CARD ===== */
        .card-leaderboard {
            position: absolute;
            left: 916px;
            top: 206px;
            width: 400px;
            height: 446px;
            background: linear-gradient(180deg, #0d2843 0%, #0a2037 40%, #061626 100%);
            border: 2px solid #527598;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            padding: 14px 16px;
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .card-leaderboard::before {
            content: '';
            position: absolute;
            top: 0;
            left: 15%;
            right: 15%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #ffe082, transparent);
        }

        .lb-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.25);
        }

        .lb-title-group {
            display: flex;
            flex-direction: column;
        }

        .lb-sub {
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #d4af37;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .lb-title {
            font-family: 'Montserrat', sans-serif;
            font-size: 16px;
            font-weight: 900;
            color: #ffffff;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .lb-badge-total {
            background: rgba(6, 22, 40, 0.85);
            border: 1px solid rgba(212, 175, 55, 0.35);
            border-radius: 8px;
            padding: 4px 10px;
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #e5b958;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .lb-table-head {
            display: grid;
            grid-template-columns: 46px 1fr 78px;
            align-items: center;
            padding: 6px 10px 4px;
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            color: #64748b;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .lb-list {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding-right: 4px;
            margin-top: 2px;
            scrollbar-width: thin;
            scrollbar-color: #caa44e rgba(13, 40, 67, 0.4);
        }

        .lb-list::-webkit-scrollbar {
            width: 4px;
        }
        .lb-list::-webkit-scrollbar-track {
            background: rgba(13, 40, 67, 0.4);
            border-radius: 4px;
        }
        .lb-list::-webkit-scrollbar-thumb {
            background: #caa44e;
            border-radius: 4px;
        }

        .lb-item {
            display: grid;
            grid-template-columns: 46px 1fr 78px;
            align-items: center;
            padding: 5px 8px;
            background: rgba(13, 40, 67, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            cursor: default;
            user-select: none;
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
            position: relative;
        }

        .lb-item.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.28) 0%, rgba(13, 40, 67, 0.85) 100%);
            border: 1.5px solid #f3d069;
            box-shadow: 0 0 14px rgba(212, 175, 55, 0.35);
        }

        .lb-no-badge {
            min-width: 40px;
            height: 24px;
            padding: 0 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 11px;
            font-weight: 900;
            background: rgba(212, 175, 55, 0.16);
            border: 1px solid rgba(212, 175, 55, 0.45);
            color: #ffe082;
            letter-spacing: 0.5px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.25);
        }

        .lb-item.active .lb-no-badge {
            background: #d4af37;
            color: #071524;
            border-color: #ffe082;
            box-shadow: 0 0 10px rgba(212, 175, 55, 0.6);
        }

        .lb-info {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 0 8px;
        }

        .lb-name {
            font-family: 'Montserrat', sans-serif;
            font-size: 12px;
            font-weight: 800;
            color: #ffffff;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
        }

        .lb-item.active .lb-name {
            color: #ffe082;
        }

        .lb-meta {
            font-family: 'Montserrat', sans-serif;
            font-size: 9px;
            font-weight: 600;
            color: #94a3b8;
            line-height: 1.3;
            margin-top: 1px;
            display: flex;
            align-items: center;
            gap: 3px;
            flex-wrap: wrap;
        }

        .lb-score-wrap {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .lb-score-val {
            font-family: 'Montserrat', sans-serif;
            font-size: 15px;
            font-weight: 900;
            color: #f3d069;
            font-variant-numeric: tabular-nums;
            line-height: 1;
        }

        .lb-score-empty {
            display: inline-block;
            min-height: 15px;
            width: 10px;
        }

        .lb-scores-tag {
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-left: 5px;
            vertical-align: middle;
        }

        .lb-chip {
            font-family: 'Montserrat', sans-serif;
            font-size: 8px;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 3px;
            line-height: 1.1;
            white-space: nowrap;
        }

        .lb-chip.done {
            background: rgba(56, 189, 248, 0.15);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #38bdf8;
        }

        .lb-chip.empty {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #64748b;
        }

        .lb-live-tag {
            font-family: 'Montserrat', sans-serif;
            font-size: 8px;
            font-weight: 800;
            color: #ff4d4d;
            letter-spacing: 0.5px;
            line-height: 1;
            margin-top: 2px;
            text-transform: uppercase;
            animation: pulse-dot 1.2s infinite ease-in-out;
        }

        .lb-footer {
            padding-top: 7px;
            margin-top: 4px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'Montserrat', sans-serif;
            font-size: 9.5px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Operator Toolbar (visible on hover) */
        .op-bar {
            position: absolute;
            bottom: 6px;
            left: 10px;
            display: flex;
            gap: 6px;
            z-index: 100;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .stage-canvas:hover .op-bar {
            opacity: 1;
        }

        .op-btn {
            background: rgba(10, 25, 45, 0.85);
            color: #d4af37;
            border: 1px solid #d4af37;
            padding: 5px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            backdrop-filter: blur(4px);
        }
        .op-btn:hover {
            background: #d4af37;
            color: #071524;
        }
    </style>
</head>
<body>

<div class="stage-canvas" id="stageCanvas">
    <!-- Participant Card -->
    <div class="card-participant">
        <div class="participant-photo-wrap">
            <img src="{{ $curr['pasfoto'] ?? '' }}" id="pPhoto" class="participant-photo" alt="{{ $curr['nama'] ?? '' }}" style="{{ !empty($curr['pasfoto']) ? 'display: block;' : 'display: none;' }}" onerror="this.style.display='none'; document.getElementById('pPhotoPh').style.display='flex';">
            <div id="pPhotoPh" class="participant-photo-placeholder" style="{{ !empty($curr['pasfoto']) ? 'display: none;' : 'display: flex;' }}">👤</div>
        </div>
        <div class="participant-navy-box">
            <div class="participant-name" id="pName">{{ $curr['nama'] ?? 'BELUM ADA PESERTA' }}</div>
            <div class="participant-sep"></div>
            <div class="participant-number">NOMOR PESERTA : <span id="pNumber">{{ $curr['no_peserta'] ?? '-' }}</span></div>
        </div>
        <div class="participant-white-box">
            <div class="participant-origin-text">
                <span class="origin-label">ASAL / KECAMATAN :</span>
                <span class="origin-value" id="pOrigin">{{ $curr['kecamatan'] ?? ($curr['tempat_lahir'] ?? '-') }}</span>
            </div>
        </div>
    </div>

    <!-- Score Card -->
    <div class="card-score">
        <div class="score-header-row">
            <div class="timer-group">
                <span class="timer-text-label">WAKTU TERSISA:</span>
                <span class="timer-digits timer-yellow" id="timerVal">{{ $timer['formatted'] ?? '05:00' }}</span>
            </div>
            <div class="badge-live-top">
                <span class="dot-blink"></span> LIVE
            </div>
        </div>

        <div class="cabang-subtitle" id="cabangTitle">
            <span class="cabang-gold">PENILAIAN LIVE</span> <span class="cabang-white">(CABANG {{ $cabangLabel }})</span>
        </div>

        <div class="score-fields-container" id="scoreFieldsContainer" style="display: flex; flex-direction: column; justify-content: space-around; flex: 1; margin: 4px 0; gap: 4px;">
            @foreach($fields as $idx => $f)
            <div class="score-field">
                <div class="score-field-top">
                    <span class="score-field-label">{{ $f['label'] }}</span>
                    <span class="score-field-num" id="sVal_{{ $idx }}">{{ number_format($f['value'], 2) }}</span>
                </div>
                <div class="score-track">
                    <div class="score-fill" id="sBar_{{ $idx }}" style="width: {{ $f['pct'] }}%;"></div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- TOTAL SKOR -->
        <div class="total-container">
            <div class="badge-live-update">LIVE UPDATE</div>
            <span class="total-text-label">TOTAL SKOR :</span>
            <span class="total-text-digits" id="sTotal">{{ number_format($total, 2) }}</span>
        </div>
    </div>

    <!-- Leaderboard / Peserta Lain Card -->
    <div class="card-leaderboard">
        <div class="lb-header">
            <div class="lb-title-group">
                <span class="lb-sub">DAFTAR PESERTA</span>
                <h3 class="lb-title">PESERTA LAIN</h3>
            </div>
            <div class="lb-badge-total">
                <span id="lbTotalCountBadge">{{ count($participants) }} PESERTA</span>
            </div>
        </div>

        <div class="lb-table-head">
            <span>NO</span>
            <span>PESERTA</span>
            <span style="text-align: right;">SKOR</span>
        </div>

        <div class="lb-list" id="lbList">
            @php
                $sortedParticipants = $participants;
                usort($sortedParticipants, function($a, $b) {
                    return strnatcasecmp($a['no_peserta'] ?? '', $b['no_peserta'] ?? '');
                });
                $sudahCount = 0;
            @endphp
            @forelse($sortedParticipants as $idx => $p)
                @php
                    $isCur = ($curr && ($p['id'] == ($curr['id'] ?? null)));
                    $hasScore = (($p['total'] ?? 0) > 0);
                    if ($hasScore) {
                        $sudahCount++;
                    }

                    $fieldsHtml = '';
                    if ($hasScore && !empty($p['fields'])) {
                        $fieldsHtml .= '<span class="lb-scores-tag">';
                        foreach ($p['fields'] as $f) {
                            $fullL = ucwords(strtolower($f['label']));
                            $fVal = (float)($f['value'] ?? 0);
                            if ($fVal > 0) {
                                $fieldsHtml .= '<span class="lb-chip done">' . $fullL . ': ' . $fVal . '</span>';
                            } else {
                                $fieldsHtml .= '<span class="lb-chip empty">' . $fullL . ': -</span>';
                            }
                        }
                        $fieldsHtml .= '</span>';
                    }
                @endphp
                <div class="lb-item {{ $isCur ? 'active' : '' }}" data-id="{{ $p['id'] }}">
                    <div class="lb-no-badge">{{ $p['no_peserta'] ?? '-' }}</div>
                    <div class="lb-info">
                        <div class="lb-name" title="{{ $p['nama'] }}">{{ $p['nama'] }}</div>
                        <div class="lb-meta"><span>{{ $p['kecamatan'] }}</span>{!! $fieldsHtml !!}</div>
                    </div>
                    <div class="lb-score-wrap">
                        @if($hasScore)
                            <span class="lb-score-val">{{ number_format($p['total'], 2) }}</span>
                        @else
                            <span class="lb-score-empty"></span>
                        @endif
                        @if($isCur)
                            <span class="lb-live-tag">&bull; TAMPIL</span>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; color: #64748b; padding: 30px 10px; font-size: 11px; font-weight: 700;">
                    BELUM ADA PESERTA TERDAFTAR
                </div>
            @endforelse
        </div>

        <div class="lb-footer">
            <span>MTQ TRENGGALEK 2026</span>
            <span id="lbTotalCount">{{ count($participants) }} Peserta ({{ $sudahCount }} dinilai)</span>
        </div>
    </div>

    <!-- Operator Toolbar (visible on hover) -->
    <div class="op-bar">
        <button class="op-btn" id="btnPrev" onclick="navigateParticipant('prev')">&larr; Prev (&larr;)</button>
        <button class="op-btn" id="btnToggleTimer" onclick="toggleTimer()">Mulai / Jeda (Spasi)</button>
        <button class="op-btn" id="btnResetTimer" onclick="resetTimer()">Reset Timer (R)</button>
        <button class="op-btn" id="btnNext" onclick="navigateParticipant('next')">Next (&rarr;) &rarr;</button>
        <button class="op-btn" onclick="toggleFullscreen()">Fullscreen (F)</button>
    </div>
</div>

<script>
let currentSlug = '{{ $slug ?? "tartil" }}';
let currentId = {{ $curr['id'] ?? 'null' }};
let nextId = {{ $initialData['next']['id'] ?? 'null' }};
let prevId = {{ $initialData['previous']['id'] ?? 'null' }};
let totalTimerSeconds = {{ $timer['total'] ?? 300 }};
let timerSeconds = {{ $timer['remaining'] ?? 300 }};
let isTimerRunning = {{ ($timer['is_running'] ?? false) ? 'true' : 'false' }};
let lastLocalActionAt = 0;
let timerStartedAt = Date.now();
let timerInitialAtStart = timerSeconds;
let allParticipantsData = @json($participants);
let scrollDirection = 1; // 1 = scroll down, -1 = scroll up
let isHovered = false;
let isPaused = false;
let autoScrollTimer = null;
let lastActiveId = null;

// Audio System with original MTQ MP3 files (0ms Instant Preload)
let playedMidSound = timerSeconds <= 60 && timerSeconds > 0;
let playedEndSound = timerSeconds <= 0;

var liveSoundUrls = {
    start: '{{ asset("sounds/mtqstart.mp3") }}',
    mid: '{{ asset("sounds/mtqmid.mp3") }}',
    end: '{{ asset("sounds/mtqend.mp3") }}'
};

var liveAudioElements = {
    start: new Audio(liveSoundUrls.start),
    mid: new Audio(liveSoundUrls.mid),
    end: new Audio(liveSoundUrls.end)
};
Object.keys(liveAudioElements).forEach(function(k) {
    liveAudioElements[k].preload = 'auto';
    try { liveAudioElements[k].load(); } catch(e) {}
});

var liveAudioCtx = null;
function getLiveAudioCtx() {
    if (!liveAudioCtx) {
        var AC = window.AudioContext || window.webkitAudioContext;
        if (AC) liveAudioCtx = new AC();
    }
    if (liveAudioCtx && liveAudioCtx.state === 'suspended') {
        liveAudioCtx.resume().catch(function() {});
    }
    return liveAudioCtx;
}

var liveSoundBuffers = {};
function preloadLiveSoundBuffer(type, url) {
    fetch(url)
        .then(function(r) { return r.arrayBuffer(); })
        .then(function(buf) {
            var ctx = getLiveAudioCtx();
            if (ctx) {
                ctx.decodeAudioData(buf, function(decoded) {
                    liveSoundBuffers[type] = decoded;
                }, function() {});
            }
        })
        .catch(function() {});
}
preloadLiveSoundBuffer('start', liveSoundUrls.start);
preloadLiveSoundBuffer('mid', liveSoundUrls.mid);
preloadLiveSoundBuffer('end', liveSoundUrls.end);

['click', 'touchstart', 'keydown', 'mousedown'].forEach(function(evt) {
    document.addEventListener(evt, function() {
        getLiveAudioCtx();
    }, { once: false, passive: true });
});

function playFallbackAudio(type) {
    var a = liveAudioElements[type];
    if (a) {
        try {
            a.currentTime = 0;
            var p = a.play();
            if (p && typeof p.catch === 'function') {
                p.catch(function(e) {
                    try {
                        var directAudio = new Audio(liveSoundUrls[type]);
                        directAudio.play().catch(function() {});
                    } catch(err) {}
                });
            }
        } catch(e) {}
    }
}

function playBeeps(count, type) {
    var ctx = getLiveAudioCtx();
    if (ctx && liveSoundBuffers[type]) {
        var playBuffer = function() {
            try {
                var src = ctx.createBufferSource();
                src.buffer = liveSoundBuffers[type];
                src.connect(ctx.destination);
                src.start(0);
            } catch(e) {
                playFallbackAudio(type);
            }
        };

        if (ctx.state === 'running') {
            playBuffer();
            return;
        } else if (typeof ctx.resume === 'function') {
            ctx.resume().then(playBuffer).catch(function() {
                playFallbackAudio(type);
            });
            return;
        }
    }

    playFallbackAudio(type);
}

function showLiveToast(msg) {
    // Disabled: Notifikasi hanya untuk panel dewan hakim, jangan tampil di layar live
}

function getRemainingSeconds() {
    if (!isTimerRunning) return timerSeconds;
    var elapsed = Math.floor((Date.now() - timerStartedAt) / 1000);
    return Math.max(0, timerInitialAtStart - elapsed);
}

function updateTimerDisplay(remaining, isRunning) {
    var timerEl = document.getElementById('timerVal');
    if (timerEl) {
        timerEl.textContent = fmtTime(remaining);
        timerEl.classList.remove('timer-yellow', 'timer-green', 'timer-red');
        if (remaining <= 0) {
            timerEl.classList.add('timer-red');
        } else if (remaining <= 60) {
            timerEl.classList.add('timer-yellow');
        } else if (isRunning) {
            timerEl.classList.add('timer-green');
        } else {
            timerEl.classList.add('timer-yellow');
        }
    }
    var toggleBtn = document.getElementById('btnToggleTimer');
    if (toggleBtn) {
        toggleBtn.textContent = isRunning ? 'Jeda (Spasi)' : 'Mulai (Spasi)';
    }
}

let pauseResumeTimeout = null;
let fetchSequence = 0;
let pollTimer = null;

function handleDirectSync(ev) {
    if (!ev || ev.slug !== currentSlug) return;

    if (ev.action === 'show_participant') {
        if (ev.recordId) {
            switchToParticipant(Number(ev.recordId));
        }
        return;
    }

    if (ev.action === 'start') {
        // Abaikan jika bukan peserta yang sedang live
        if (ev.recordId && currentId && Number(ev.recordId) !== Number(currentId)) return;
        lastLocalActionAt = Date.now();
        playBeeps(1, 'start');
        var targetRem = (ev.remaining !== undefined && ev.remaining !== null && Number(ev.remaining) > 0)
            ? Number(ev.remaining)
            : totalTimerSeconds;
        isTimerRunning = true;
        timerSeconds = targetRem;
        timerInitialAtStart = timerSeconds;
        // Pakai timestamp dari dewan untuk eliminasi jeda network broadcast
        timerStartedAt = (ev.timestamp && ev.timestamp > 0) ? ev.timestamp : Date.now();
        if (timerSeconds > 60) playedMidSound = false;
        if (timerSeconds > 0) playedEndSound = false;
        updateTimerDisplay(getRemainingSeconds(), true);
    } else if (ev.action === 'pause') {
        if (ev.recordId && currentId && Number(ev.recordId) !== Number(currentId)) return;
        lastLocalActionAt = Date.now();
        // Hitung dulu sebelum stop — getRemainingSeconds() butuh isTimerRunning=true
        var calcRem = isTimerRunning
            ? Math.max(0, timerInitialAtStart - Math.floor((Date.now() - timerStartedAt) / 1000))
            : timerSeconds;
        // Validasi — kalau hasil lokal tidak masuk akal, fallback ke ev.remaining dari dewan
        if (calcRem <= 0 && ev.remaining !== undefined && Number(ev.remaining) > 0) {
            calcRem = Number(ev.remaining);
        }
        isTimerRunning = false;
        timerSeconds = calcRem;
        timerInitialAtStart = calcRem;
        updateTimerDisplay(calcRem, false);
    } else if (ev.action === 'reset') {
        if (ev.recordId && currentId && Number(ev.recordId) !== Number(currentId)) return;
        lastLocalActionAt = Date.now();
        isTimerRunning = false;
        timerSeconds = Number(ev.total || totalTimerSeconds);
        timerInitialAtStart = timerSeconds;
        playedMidSound = false;
        playedEndSound = false;
        updateTimerDisplay(timerSeconds, false);
    } else if (ev.action === 'score_saved') {
        if (ev.recordId && ev.scores) {
            // Update allParticipantsData total & fields
            var targetP = (allParticipantsData || []).find(function(p) { return Number(p.id) === Number(ev.recordId); });
            if (targetP) {
                var newTotal = 0;
                Object.keys(ev.scores).forEach(function(k) {
                    if (k !== 'total') newTotal += Number(ev.scores[k] || 0);
                });
                if (ev.scores.total !== undefined) newTotal = Number(ev.scores.total);
                targetP.total = newTotal;
                if (targetP.fields) {
                    targetP.fields.forEach(function(f) {
                        if (ev.scores[f.key] !== undefined) {
                            f.value = Number(ev.scores[f.key] || 0);
                            f.pct = Math.min(100, Math.max(0, (f.value / (f.max || 100)) * 100));
                        }
                    });
                }
            }
            // Update display jika peserta ini sedang live
            if (currentId && Number(ev.recordId) === Number(currentId)) {
                var sTotalEl = document.getElementById('sTotal');
                if (sTotalEl) sTotalEl.textContent = (targetP ? Number(targetP.total) : 0).toFixed(2);
                if (targetP && targetP.fields) renderFields(targetP.fields);
            }
            // Update leaderboard score tampilan
            renderLeaderboard(null, false);
        }
        // Fetch fresh data dengan delay kecil agar DB sudah tersimpan
        setTimeout(function() { fetchData(currentId); }, 800);
    }
}

try {
    if (window.BroadcastChannel) {
        var bc = new BroadcastChannel('mtq_timer_channel');
        bc.onmessage = function(e) { handleDirectSync(e.data); };
    }
} catch(e) {}

window.addEventListener('storage', function(e) {
    if (e.key === 'mtq_timer_sync_event' && e.newValue) {
        try { handleDirectSync(JSON.parse(e.newValue)); } catch(err) {}
    }
});

// pollFastTimerStatus dihapus — digantikan fetchData polling 3 detik
// fetchData sudah handle semua state sync dari server

function restartPollTimer() {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = setInterval(function() {
        if (!currentId) return;
        // Kirim tanpa ID agar server return peserta aktif dari cache — untuk deteksi switch peserta
        var pollUrl = getAppBasePath() + '/live/' + currentSlug + '/data';
        fetch(pollUrl)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data || data.empty || !data.current) return;
                var serverId = Number(data.current.id);
                if (serverId && serverId !== Number(currentId)) {
                    // Peserta aktif di server beda — switch tanpa beep
                    switchToParticipant(serverId);
                } else {
                    // Sama — update display & timer state
                    fetchData(currentId);
                }
            })
            .catch(function() {});
    }, 3000);
}

function highlightActiveLeaderboard(id) {
    var list = document.getElementById('lbList');
    if (!list) return;

    var items = list.querySelectorAll('.lb-item');
    var activeEl = null;

    items.forEach(function(item) {
        var itemId = parseInt(item.getAttribute('data-id'), 10);
        var isCur = (itemId === id);
        item.classList.toggle('active', isCur);

        var liveTag = item.querySelector('.lb-live-tag');
        if (isCur) {
            activeEl = item;
            if (!liveTag) {
                var scoreWrap = item.querySelector('.lb-score-wrap');
                if (scoreWrap) {
                    var tag = document.createElement('span');
                    tag.className = 'lb-live-tag';
                    tag.innerHTML = '&bull; TAMPIL';
                    scoreWrap.appendChild(tag);
                }
            }
        } else if (liveTag) {
            liveTag.remove();
        }
    });

    if (activeEl) {
        lastActiveId = id;
        activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function getAppBasePath() {
    return '{{ url('/') }}';
}

function resolveStorageUrl(url) {
    if (!url || typeof url !== 'string' || url.trim() === '') return '';
    if (url.indexOf('/storage/') !== -1) {
        var filename = url.split('/storage/')[1];
        return getAppBasePath() + '/storage/' + filename;
    }
    return url;
}

function updateParticipantPhoto(url, name) {
    var photoWrap = document.querySelector('.participant-photo-wrap');
    if (!photoWrap) return;

    var photoEl = document.getElementById('pPhoto');
    var photoPh = document.getElementById('pPhotoPh');

    if (!url || typeof url !== 'string' || url.trim() === '') {
        if (photoEl) {
            photoEl.style.display = 'none';
            photoEl.removeAttribute('src');
        }
        if (photoPh) {
            photoPh.style.display = 'flex';
            photoPh.style.opacity = '1';
        }
        return;
    }

    var cleanUrl = resolveStorageUrl(url);

    if (!photoEl) {
        photoEl = document.createElement('img');
        photoEl.id = 'pPhoto';
        photoEl.className = 'participant-photo';
        photoWrap.insertBefore(photoEl, photoPh);
    }

    photoEl.alt = name || 'Foto Peserta';

    photoEl.onerror = function() {
        this.style.display = 'none';
        if (photoPh) {
            photoPh.style.display = 'flex';
            photoPh.style.opacity = '1';
        }
    };

    photoEl.onload = function() {
        this.style.display = 'block';
        this.style.opacity = '1';
        if (photoPh) photoPh.style.display = 'none';
    };

    // Langsung perbarui src seketika
    photoEl.src = cleanUrl;
    photoEl.style.display = 'block';
    photoEl.style.opacity = '1';
    if (photoPh) photoPh.style.display = 'none';
}

// Preload semua foto peserta ke memory cache browser agar klik ganti peserta 0ms instan
if (Array.isArray(allParticipantsData)) {
    allParticipantsData.forEach(function(p) {
        if (p.pasfoto) {
            var preImg = new Image();
            preImg.src = resolveStorageUrl(p.pasfoto);
        }
    });
}

function formatFullLabel(label) {
    if (!label) return '';
    var words = label.toLowerCase().split(' ');
    for (var i = 0; i < words.length; i++) {
        if (words[i] === '&') continue;
        words[i] = words[i].charAt(0).toUpperCase() + words[i].slice(1);
    }
    return words.join(' ');
}

function buildFieldChipsHtml(fields, hasScore) {
    if (!hasScore || !fields || fields.length === 0) return '';
    var html = '<span class="lb-scores-tag">';
    fields.forEach(function(f) {
        var fullL = formatFullLabel(f.label);
        var val = Number(f.value || 0);
        if (val > 0) {
            html += '<span class="lb-chip done">' + fullL + ': ' + val + '</span>';
        } else {
            html += '<span class="lb-chip empty">' + fullL + ': -</span>';
        }
    });
    html += '</span>';
    return html;
}

function renderLeaderboard(participants, forceRebuild) {
    if (participants) {
        allParticipantsData = participants;
    }
    var container = document.getElementById('lbList');
    if (!container) return;
    if (!allParticipantsData || allParticipantsData.length === 0) {
        container.innerHTML = '<div style="text-align: center; color: #64748b; padding: 30px 10px; font-size: 11px; font-weight: 700;">BELUM ADA PESERTA TERDAFTAR</div>';
        return;
    }

    var list = allParticipantsData.slice();
    list.sort(function(a, b) {
        return ('' + (a.no_peserta || '')).localeCompare('' + (b.no_peserta || ''), undefined, { numeric: true });
    });

    var existingItems = container.querySelectorAll('.lb-item');
    var isSameStructure = (!forceRebuild && existingItems.length === list.length);
    if (isSameStructure) {
        for (var k = 0; k < list.length; k++) {
            if (parseInt(existingItems[k].getAttribute('data-id'), 10) !== list[k].id) {
                isSameStructure = false;
                break;
            }
        }
    }

    var sudahCount = 0;

    if (isSameStructure) {
        // Smooth in-place update
        list.forEach(function(p, idx) {
            var item = existingItems[idx];
            var isCurrent = (p.id === currentId);
            var hasScore = (p.total && Number(p.total) > 0);
            if (hasScore) sudahCount++;

            item.classList.toggle('active', isCurrent);

            // Update no_peserta badge (bukan ranking)
            var noBadgeEl = item.querySelector('.lb-no-badge');
            if (noBadgeEl) {
                noBadgeEl.textContent = (p.no_peserta || '-');
            }

            // Update meta with kecamatan + field scores
            var metaEl = item.querySelector('.lb-meta');
            if (metaEl) {
                var chipsHtml = buildFieldChipsHtml(p.fields, hasScore);
                var baseMeta = (p.kecamatan || '-');
                metaEl.innerHTML = '<span>' + baseMeta + '</span>' + chipsHtml;
            }

            // Update score
            var scoreWrap = item.querySelector('.lb-score-wrap');
            if (scoreWrap) {
                var scoreValEl = scoreWrap.querySelector('.lb-score-val');
                var scoreEmptyEl = scoreWrap.querySelector('.lb-score-empty');
                var liveTag = scoreWrap.querySelector('.lb-live-tag');

                if (hasScore) {
                    var fmt = Number(p.total).toFixed(2);
                    if (scoreValEl) {
                        if (scoreValEl.textContent !== fmt) scoreValEl.textContent = fmt;
                    } else if (scoreEmptyEl) {
                        scoreEmptyEl.outerHTML = '<span class="lb-score-val">' + fmt + '</span>';
                    }
                } else if (scoreValEl) {
                    scoreValEl.outerHTML = '<span class="lb-score-empty"></span>';
                }

                if (isCurrent && !liveTag) {
                    var tag = document.createElement('span');
                    tag.className = 'lb-live-tag';
                    tag.innerHTML = '&bull; TAMPIL';
                    scoreWrap.appendChild(tag);
                } else if (!isCurrent && liveTag) {
                    liveTag.remove();
                }
            }
        });
    } else {
        // Rebuild DOM
        var html = '';
        list.forEach(function(p, idx) {
            var isCurrent = (p.id === currentId);
            var hasScore = (p.total && Number(p.total) > 0);
            if (hasScore) sudahCount++;

            var chipsHtml = buildFieldChipsHtml(p.fields, hasScore);
            var baseMeta = (p.kecamatan || '-');
            var scoreDisplay = hasScore
                ? '<span class="lb-score-val">' + Number(p.total).toFixed(2) + '</span>'
                : '<span class="lb-score-empty"></span>';

            html += '<div class="lb-item ' + (isCurrent ? 'active' : '') + '" data-id="' + p.id + '">' +
                '<div class="lb-no-badge">' + (p.no_peserta || '-') + '</div>' +
                '<div class="lb-info">' +
                    '<div class="lb-name" title="' + (p.nama || '-') + '">' + (p.nama || '-') + '</div>' +
                    '<div class="lb-meta"><span>' + baseMeta + '</span>' + chipsHtml + '</div>' +
                '</div>' +
                '<div class="lb-score-wrap">' +
                    scoreDisplay +
                    (isCurrent ? '<span class="lb-live-tag">&bull; TAMPIL</span>' : '') +
                '</div>' +
            '</div>';
        });

        container.innerHTML = html;
    }

    var countEl = document.getElementById('lbTotalCount');
    if (countEl) {
        countEl.textContent = list.length + ' Peserta (' + sudahCount + ' dinilai)';
    }
    var countBadgeEl = document.getElementById('lbTotalCountBadge');
    if (countBadgeEl) {
        countBadgeEl.textContent = list.length + ' PESERTA';
    }

    var activeEl = container.querySelector('.lb-item.active');
    if (activeEl && lastActiveId !== currentId) {
        lastActiveId = currentId;
        activeEl.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}

function initAutoScroll() {
    var list = document.getElementById('lbList');
    if (!list) return;

    list.addEventListener('mouseenter', function() { isHovered = true; });
    list.addEventListener('mouseleave', function() { isHovered = false; });
    list.addEventListener('touchstart', function() { isHovered = true; }, { passive: true });
    list.addEventListener('touchend', function() {
        setTimeout(function() { isHovered = false; }, 2000);
    }, { passive: true });

    if (autoScrollTimer) clearInterval(autoScrollTimer);

    autoScrollTimer = setInterval(function() {
        if (isHovered || isPaused) return;
        var maxScroll = list.scrollHeight - list.clientHeight;
        if (maxScroll <= 8) return; // Muat semua, tidak perlu scroll

        if (scrollDirection === 1) {
            list.scrollTop += 0.8;
            if (list.scrollTop >= maxScroll - 1) {
                scrollDirection = -1;
                isPaused = true;
                setTimeout(function() { isPaused = false; }, 2500); // jeda di bawah
            }
        } else {
            list.scrollTop -= 0.8;
            if (list.scrollTop <= 1) {
                scrollDirection = 1;
                isPaused = true;
                setTimeout(function() { isPaused = false; }, 2500); // jeda di atas
            }
        }
    }, 35);
}

function fitStage() {
    var stage = document.getElementById('stageCanvas');
    var w = window.innerWidth;
    var h = window.innerHeight;
    var scale = Math.min(w / 1376, h / 768);
    stage.style.transform = 'scale(' + scale + ')';
}
window.addEventListener('resize', fitStage);
fitStage();

function fmtTime(s) {
    var m = Math.floor(s / 60);
    var sec = s % 60;
    return (m < 10 ? '0' + m : m) + ':' + (sec < 10 ? '0' + sec : sec);
}

function renderFields(fields) {
    var container = document.getElementById('scoreFieldsContainer');
    if (!container || !fields || fields.length === 0) return;

    var existingRows = container.querySelectorAll('.score-field');
    if (existingRows.length === fields.length) {
        fields.forEach(function(f, idx) {
            var valEl = document.getElementById('sVal_' + idx);
            var barEl = document.getElementById('sBar_' + idx);
            if (valEl) valEl.textContent = Number(f.value).toFixed(2);
            if (barEl) barEl.style.width = f.pct + '%';
        });
        return;
    }

    var html = '';
    fields.forEach(function(f, idx) {
        html += '<div class="score-field">' +
            '<div class="score-field-top">' +
                '<span class="score-field-label">' + f.label + '</span>' +
                '<span class="score-field-num" id="sVal_' + idx + '">' + Number(f.value).toFixed(2) + '</span>' +
            '</div>' +
            '<div class="score-track">' +
                '<div class="score-fill" id="sBar_' + idx + '" style="width: ' + f.pct + '%;"></div>' +
            '</div>' +
        '</div>';
    });
    container.innerHTML = html;
}

function updateDisplay(data) {
    if (!data || data.empty) return;

    // Update currentId terlebih dahulu agar sinkron dengan leaderboard
    currentId = Number(data.current.id);
    nextId = data.next ? Number(data.next.id) : null;
    prevId = data.previous ? Number(data.previous.id) : null;

    if (data.participants) {
        renderLeaderboard(data.participants);
    } else {
        highlightActiveLeaderboard(currentId);
    }

    updateParticipantPhoto(data.current.pasfoto, data.current.nama);

    var pNameEl = document.getElementById('pName');
    var pNumberEl = document.getElementById('pNumber');
    var pOriginEl = document.getElementById('pOrigin');

    if (pNameEl) {
        pNameEl.textContent = data.current.nama || '-';
        pNameEl.style.opacity = '1';
    }
    if (pNumberEl) pNumberEl.textContent = data.current.no_peserta || '-';
    if (pOriginEl) {
        pOriginEl.textContent = data.current.kecamatan || data.current.tempat_lahir || '-';
        pOriginEl.style.opacity = '1';
    }

    if (data.current.cabang) {
        document.getElementById('cabangTitle').innerHTML = '<span class="cabang-gold">PENILAIAN LIVE</span> <span class="cabang-white">(CABANG ' + data.current.cabang + ')</span>';
    }

    if (data.current.fields && data.current.fields.length > 0) {
        renderFields(data.current.fields);
    }

    document.getElementById('sTotal').textContent = Number(data.current.total || 0).toFixed(2);
}

function tickTimer() {
    if (!isTimerRunning) return;
    var remaining = getRemainingSeconds();
    timerSeconds = remaining;
    updateTimerDisplay(remaining, true);

    // Suara mid: tepat saat crossing batas 60 detik (dari atas ke bawah)
    if (remaining > 60) {
        playedMidSound = false;
        playedEndSound = false;
    } else if (remaining <= 60 && remaining > 0) {
        playedEndSound = false;
        if (!playedMidSound) {
            playedMidSound = true;
            playBeeps(2, 'mid');
        }
    }

    // Suara end: saat 00:00
    if (remaining <= 0) {
        isTimerRunning = false;
        timerSeconds = 0;
        timerInitialAtStart = 0;
        updateTimerDisplay(0, false);
        if (!playedEndSound) {
            playedEndSound = true;
            playBeeps(3, 'end');
        }
    }
}

let isFetchingData = false;

function fetchData(forcedId, setActive, fromUserAction) {
    var targetId = (forcedId !== undefined && forcedId !== null) ? Number(forcedId) : currentId;
    if (isFetchingData && forcedId === undefined) return;
    isFetchingData = true;

    var reqSeq = ++fetchSequence;
    var url = getAppBasePath() + '/live/' + currentSlug + '/data' + (targetId ? ('/' + targetId + (setActive ? '?set_active=1' : '')) : '');

    fetch(url)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            isFetchingData = false;
            if (!data || data.empty) return;
            if (reqSeq !== fetchSequence) return;

            // Jika server kembalikan peserta berbeda dari yang diminta, abaikan (stale)
            if (targetId && data.current && Number(data.current.id) !== Number(targetId)) return;

            var isNewParticipant = (data.current && currentId !== null && Number(data.current.id) !== currentId);

            updateDisplay(data);

            if (!data.timer) return;

            totalTimerSeconds = Number(data.timer.total) || 300;
            var serverRemaining = Number(data.timer.remaining);
            var serverIsRunning = Boolean(data.timer.is_running);
            var serverStartedAtMs = data.timer.started_at_ms || null;

            if (isNewParticipant) {
                // Peserta beda dari server — update semua state, NO beep dari poll
                currentId = Number(data.current.id);
                isTimerRunning = serverIsRunning;
                timerSeconds = serverRemaining;
                timerInitialAtStart = serverRemaining;
                // Pakai started_at_ms server untuk sync presisi cross-device
                timerStartedAt = serverStartedAtMs || Date.now();
                playedMidSound = (serverRemaining <= 60 && serverRemaining > 0);
                playedEndSound = (serverRemaining <= 0);
                updateTimerDisplay(serverIsRunning ? getRemainingSeconds() : timerSeconds, isTimerRunning);
                return;
            }

            if (serverIsRunning) {
                if (!isTimerRunning) {
                    // Server running, lokal stop — sync state TANPA beep (beep hanya dari broadcast)
                    isTimerRunning = true;
                    timerSeconds = serverRemaining;
                    timerInitialAtStart = serverRemaining;
                    // Pakai started_at_ms server agar presisi sama dengan dewan
                    timerStartedAt = serverStartedAtMs || Date.now();
                    if (timerSeconds > 60) playedMidSound = false;
                    if (timerSeconds > 0) playedEndSound = false;
                    updateTimerDisplay(getRemainingSeconds(), true);
                    // Beep HANYA jika ini dipanggil dari user action (bukan poll)
                    if (fromUserAction) {
                        playBeeps(1, 'start');
                    }
                } else {
                    // Keduanya running — koreksi drift >3 detik, pakai started_at_ms server
                    var localRem = getRemainingSeconds();
                    if (Math.abs(localRem - serverRemaining) > 3) {
                        timerSeconds = serverRemaining;
                        timerInitialAtStart = serverRemaining;
                        timerStartedAt = serverStartedAtMs || Date.now();
                    }
                }
            } else {
                // Server stop — sync lokal ke stop
                if (isTimerRunning) {
                    isTimerRunning = false;
                    timerSeconds = serverRemaining;
                    timerInitialAtStart = serverRemaining;
                    updateTimerDisplay(timerSeconds, false);
                } else if (Math.abs(timerSeconds - serverRemaining) > 2) {
                    // Lokal sudah stop, tapi nilai beda (misal setelah reset) — update display
                    timerSeconds = serverRemaining;
                    timerInitialAtStart = serverRemaining;
                    updateTimerDisplay(timerSeconds, false);
                }
            }
        })
        .catch(function(err) {
            isFetchingData = false;
            console.error('Poll error:', err);
        });
}

function navigateParticipant(direction) {
    var targetId = direction === 'next' ? nextId : prevId;
    if (!targetId || targetId === currentId) return;
    switchToParticipant(targetId);
}

function switchToParticipant(id) {
    if (!id) return;
    id = Number(id);

    // 1. ALWAYS STOP TIMER ON SWITCH! Never auto-play!
    isTimerRunning = false;
    playedMidSound = false;
    playedEndSound = false;

    isPaused = true;
    if (pauseResumeTimeout) clearTimeout(pauseResumeTimeout);
    pauseResumeTimeout = setTimeout(function() {
        isPaused = false;
    }, 8000);

    currentId = id;
    window.history.pushState({}, '', getAppBasePath() + '/live/' + currentSlug + '/' + currentId);

    var currentIndex = (allParticipantsData || []).findIndex(function(p) { return Number(p.id) === currentId; });
    if (currentIndex !== -1) {
        prevId = currentIndex > 0 ? allParticipantsData[currentIndex - 1].id : null;
        nextId = currentIndex < allParticipantsData.length - 1 ? allParticipantsData[currentIndex + 1].id : null;
    }

    highlightActiveLeaderboard(currentId);

    var pData = (allParticipantsData || []).find(function(p) { return Number(p.id) === currentId; });
    if (pData) {
        var pNameEl = document.getElementById('pName');
        var pNumberEl = document.getElementById('pNumber');
        var pOriginEl = document.getElementById('pOrigin');
        var sTotalEl = document.getElementById('sTotal');
        if (pNameEl) pNameEl.textContent = pData.nama || '-';
        if (pNumberEl) pNumberEl.textContent = pData.no_peserta || '-';
        if (pOriginEl) pOriginEl.textContent = pData.kecamatan || '-';
        if (sTotalEl) sTotalEl.textContent = Number(pData.total || 0).toFixed(2);
        updateParticipantPhoto(pData.pasfoto, pData.nama);
        if (pData.fields && pData.fields.length > 0) renderFields(pData.fields);

        // Status awal: jika sudah dinilai langsung 0, jika belum durasi penuh
        var isScored = Number(pData.total || 0) > 0;
        timerSeconds = isScored ? 0 : totalTimerSeconds;
        timerInitialAtStart = timerSeconds;
        updateTimerDisplay(timerSeconds, false);
    }

    lastLocalActionAt = Date.now();
    fetchData(currentId, true, true);
}
function toggleTimer() {
    if (!currentId) return;
    lastLocalActionAt = Date.now();

    if (!isTimerRunning) {
        playBeeps(1, 'start'); // 1 bel saat mulai langsung seketika (0ms)

        // Jika sudah di 00:00 dan klik start lagi, baru mulai ulang dari awal
        if (timerSeconds <= 0) {
            timerSeconds = totalTimerSeconds;
            playedMidSound = false;
            playedEndSound = false;
        } else if (timerSeconds > 60) {
            playedMidSound = false;
        }
        isTimerRunning = true;
        timerStartedAt = Date.now();
        timerInitialAtStart = timerSeconds;
        updateTimerDisplay(timerSeconds, true);
        tickTimer();
        fetch(getAppBasePath() + '/live/' + currentSlug + '/timer/start?id=' + currentId + '&remaining=' + timerSeconds);
        showLiveToast('Timer Dimulai');
    } else {
        // Pause
        timerSeconds = getRemainingSeconds();
        isTimerRunning = false;
        timerInitialAtStart = timerSeconds;
        updateTimerDisplay(timerSeconds, false);
        fetch(getAppBasePath() + '/live/' + currentSlug + '/timer/pause?id=' + currentId + '&remaining=' + timerSeconds);
        showLiveToast('Timer Dijeda');
    }
}

function resetTimer() {
    if (!currentId) return;
    lastLocalActionAt = Date.now();
    // Manual Reset hanya saat tombol Reset Timer diklik
    isTimerRunning = false;
    timerSeconds = totalTimerSeconds;
    timerInitialAtStart = totalTimerSeconds;
    playedMidSound = false;
    playedEndSound = false;
    updateTimerDisplay(totalTimerSeconds, false);
    fetch(getAppBasePath() + '/live/' + currentSlug + '/timer/reset?id=' + currentId + '&remaining=' + totalTimerSeconds);
    showLiveToast('Timer Direset');
}

function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(function(e) {});
    } else {
        document.exitFullscreen().catch(function(e) {});
    }
}

// Keyboard shortcuts for broadcast operator
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowRight') {
        navigateParticipant('next');
    } else if (e.key === 'ArrowLeft') {
        navigateParticipant('prev');
    } else if (e.key === ' ') {
        e.preventDefault();
        toggleTimer();
    } else if (e.key === 'r' || e.key === 'R') {
        resetTimer();
    } else if (e.key === 'f' || e.key === 'F') {
        toggleFullscreen();
    }
});

// Initial timer styling
updateTimerDisplay(timerSeconds, isTimerRunning);

fetchData();
initAutoScroll();
restartPollTimer();
setInterval(function() {
    if (isTimerRunning) {
        tickTimer();
    }
}, 250);
</script>
</body>
</html>
