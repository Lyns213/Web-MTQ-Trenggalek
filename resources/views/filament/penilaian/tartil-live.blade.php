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
            background: url("{{ asset('images/asset_mtq_bg_template.jpg') }}") no-repeat center center;
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

        .lb-tabs {
            display: flex;
            background: rgba(6, 22, 40, 0.7);
            border: 1px solid rgba(212, 175, 55, 0.3);
            border-radius: 8px;
            padding: 2px;
            gap: 2px;
        }

        .lb-tab {
            background: transparent;
            border: none;
            color: #94a3b8;
            font-family: 'Montserrat', sans-serif;
            font-size: 10px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            letter-spacing: 0.5px;
        }

        .lb-tab.active {
            background: #d4af37;
            color: #071524;
            box-shadow: 0 1px 4px rgba(0,0,0,0.3);
        }

        .lb-table-head {
            display: grid;
            grid-template-columns: 30px 1fr 78px;
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
            grid-template-columns: 30px 1fr 78px;
            align-items: center;
            padding: 6px 8px;
            background: rgba(13, 40, 67, 0.55);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.15s ease;
            position: relative;
        }

        .lb-item:hover {
            background: rgba(212, 175, 55, 0.18);
            border-color: rgba(212, 175, 55, 0.4);
            transform: translateX(2px);
        }

        .lb-item.active {
            background: linear-gradient(90deg, rgba(212, 175, 55, 0.28) 0%, rgba(13, 40, 67, 0.85) 100%);
            border: 1.5px solid #f3d069;
            box-shadow: 0 0 14px rgba(212, 175, 55, 0.35);
        }

        .lb-rank {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
            font-size: 10.5px;
            font-weight: 900;
            background: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }

        .lb-rank.gold {
            background: linear-gradient(135deg, #ffd700, #b8860b);
            color: #0c1c2e;
            box-shadow: 0 0 8px rgba(255, 215, 0, 0.5);
        }
        .lb-rank.silver {
            background: linear-gradient(135deg, #e2e8f0, #94a3b8);
            color: #0c1c2e;
        }
        .lb-rank.bronze {
            background: linear-gradient(135deg, #d97706, #92400e);
            color: #ffffff;
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
            font-size: 9.5px;
            font-weight: 600;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.2;
            margin-top: 1px;
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
            @if($curr && !empty($curr['pasfoto']))
                <img src="{{ $curr['pasfoto'] }}" id="pPhoto" class="participant-photo" alt="{{ $curr['nama'] }}">
                <div id="pPhotoPh" class="participant-photo-placeholder" style="display: none;">👤</div>
            @else
                <img src="" id="pPhoto" class="participant-photo" alt="" style="display: none;">
                <div id="pPhotoPh" class="participant-photo-placeholder">👤</div>
            @endif
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
                <span class="lb-sub">KLASEMEN NILAI</span>
                <h3 class="lb-title">PESERTA LAIN</h3>
            </div>
            <div class="lb-tabs">
                <button class="lb-tab active" id="tabRank" onclick="setLeaderboardSort('rank')">RANK</button>
                <button class="lb-tab" id="tabOrder" onclick="setLeaderboardSort('order')">NO</button>
            </div>
        </div>

        <div class="lb-table-head">
            <span>#</span>
            <span>PESERTA</span>
            <span style="text-align: right;">SKOR</span>
        </div>

        <div class="lb-list" id="lbList">
            @php
                $sortedParticipants = $participants;
                usort($sortedParticipants, function($a, $b) {
                    if (($b['total'] ?? 0) != ($a['total'] ?? 0)) {
                        return ($b['total'] ?? 0) <=> ($a['total'] ?? 0);
                    }
                    return strnatcmp($a['no_peserta'] ?? '', $b['no_peserta'] ?? '');
                });
                $sudahCount = 0;
            @endphp
            @forelse($sortedParticipants as $idx => $p)
                @php
                    $isCur = ($curr && ($p['id'] == ($curr['id'] ?? null)));
                    $rankClass = '';
                    if (($p['total'] ?? 0) > 0) {
                        $sudahCount++;
                        if ($idx === 0) $rankClass = 'gold';
                        elseif ($idx === 1) $rankClass = 'silver';
                        elseif ($idx === 2) $rankClass = 'bronze';
                    }
                @endphp
                <div class="lb-item {{ $isCur ? 'active' : '' }}" onclick="selectParticipant({{ $p['id'] }})">
                    <div class="lb-rank {{ $rankClass }}">{{ $idx + 1 }}</div>
                    <div class="lb-info">
                        <div class="lb-name" title="{{ $p['nama'] }}">{{ $p['nama'] }}</div>
                        <div class="lb-meta">No. {{ $p['no_peserta'] }} &bull; {{ $p['kecamatan'] }}</div>
                    </div>
                    <div class="lb-score-wrap">
                        @if(($p['total'] ?? 0) > 0)
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
let timerStartedAt = Date.now();
let timerInitialAtStart = timerSeconds;
let allParticipantsData = @json($participants);
let lbSortMode = 'rank';
let scrollDirection = 1; // 1 = scroll down, -1 = scroll up
let isHovered = false;
let isPaused = false;
let autoScrollTimer = null;
let lastActiveId = null;

// Audio resources
const startAudio = new Audio('{{ asset("sounds/mtqstart.mp3") }}');
const midAudio = new Audio('{{ asset("sounds/mtqmid.mp3") }}');
const endAudio = new Audio('{{ asset("sounds/mtqend.mp3") }}');

let playedMidSound = timerSeconds <= 60 && timerSeconds > 0;
let playedEndSound = timerSeconds <= 0;

function playAudio(audio) {
    try {
        audio.currentTime = 0;
        var p = audio.play();
        if (p !== undefined) {
            p.catch(function(err) { console.warn('Audio play prevented:', err); });
        }
    } catch(e) {}
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

function setLeaderboardSort(mode) {
    lbSortMode = mode;
    var tabRank = document.getElementById('tabRank');
    var tabOrder = document.getElementById('tabOrder');
    if (tabRank) tabRank.classList.toggle('active', mode === 'rank');
    if (tabOrder) tabOrder.classList.toggle('active', mode === 'order');
    renderLeaderboard(allParticipantsData);
}

function selectParticipant(id) {
    if (!id || id === currentId) return;
    currentId = id;
    window.history.pushState({}, '', '/live/' + currentSlug + '/' + id);

    // Ganti peserta: reset timer lokal ke total waktu peserta
    isTimerRunning = false;
    timerSeconds = totalTimerSeconds;
    timerInitialAtStart = totalTimerSeconds;
    playedMidSound = false;
    playedEndSound = false;
    updateTimerDisplay(totalTimerSeconds, false);

    fetchData();
}

function renderLeaderboard(participants) {
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

    if (lbSortMode === 'rank') {
        list.sort(function(a, b) {
            var diff = (b.total || 0) - (a.total || 0);
            if (diff !== 0) return diff;
            return ('' + (a.no_peserta || '')).localeCompare('' + (b.no_peserta || ''), undefined, { numeric: true });
        });
    } else {
        list.sort(function(a, b) {
            return ('' + (a.no_peserta || '')).localeCompare('' + (b.no_peserta || ''), undefined, { numeric: true });
        });
    }

    var html = '';
    var sudahCount = 0;
    list.forEach(function(p, idx) {
        var isCurrent = (p.id === currentId);
        var rankClass = '';
        var hasScore = (p.total && Number(p.total) > 0);
        if (hasScore) {
            sudahCount++;
            if (lbSortMode === 'rank') {
                if (idx === 0) rankClass = 'gold';
                else if (idx === 1) rankClass = 'silver';
                else if (idx === 2) rankClass = 'bronze';
            }
        }

        var scoreDisplay = hasScore
            ? '<span class="lb-score-val">' + Number(p.total).toFixed(2) + '</span>'
            : '<span class="lb-score-empty"></span>';

        html += '<div class="lb-item ' + (isCurrent ? 'active' : '') + '" onclick="selectParticipant(' + p.id + ')">' +
            '<div class="lb-rank ' + rankClass + '">' + (idx + 1) + '</div>' +
            '<div class="lb-info">' +
                '<div class="lb-name" title="' + (p.nama || '-') + '">' + (p.nama || '-') + '</div>' +
                '<div class="lb-meta">No. ' + (p.no_peserta || '-') + ' &bull; ' + (p.kecamatan || '-') + '</div>' +
            '</div>' +
            '<div class="lb-score-wrap">' +
                scoreDisplay +
                (isCurrent ? '<span class="lb-live-tag">&bull; TAMPIL</span>' : '') +
            '</div>' +
        '</div>';
    });

    container.innerHTML = html;

    var countEl = document.getElementById('lbTotalCount');
    if (countEl) {
        countEl.textContent = list.length + ' Peserta (' + sudahCount + ' dinilai)';
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
    if (data.participants) {
        renderLeaderboard(data.participants);
    }

    if (data.empty) {
        document.getElementById('pName').textContent = 'BELUM ADA PESERTA';
        document.getElementById('pNumber').textContent = '-';
        document.getElementById('pOrigin').textContent = '-';
        var photoEl = document.getElementById('pPhoto');
        var photoPh = document.getElementById('pPhotoPh');
        photoEl.style.display = 'none';
        photoPh.style.display = 'flex';

        if (data.cabang) {
            document.getElementById('cabangTitle').innerHTML = '<span class="cabang-gold">PENILAIAN LIVE</span> <span class="cabang-white">(CABANG ' + data.cabang + ')</span>';
        }
        if (data.fields && data.fields.length > 0) {
            renderFields(data.fields);
        }
        document.getElementById('sTotal').textContent = '0.00';
        return;
    }

    currentId = data.current.id;
    nextId = data.next ? data.next.id : null;
    prevId = data.previous ? data.previous.id : null;

    var photoEl = document.getElementById('pPhoto');
    var photoPh = document.getElementById('pPhotoPh');
    if (data.current.pasfoto) {
        photoEl.src = data.current.pasfoto;
        photoEl.style.display = 'block';
        photoPh.style.display = 'none';
    } else {
        photoEl.style.display = 'none';
        photoPh.style.display = 'flex';
    }

    document.getElementById('pName').textContent = data.current.nama || '-';
    document.getElementById('pNumber').textContent = data.current.no_peserta || '-';
    document.getElementById('pOrigin').textContent = data.current.kecamatan || data.current.tempat_lahir || '-';

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

    // Rule 5: 1 menit sebelum selesai kasih bunyi sound
    if (remaining <= 60 && remaining > 0 && !playedMidSound) {
        playedMidSound = true;
        playAudio(midAudio);
    }

    // Rule 6: ketika selesai menit 0 kasih sound teet dan STOP di 00:00 (JANGAN RESET OTOMATIS)
    if (remaining <= 0) {
        isTimerRunning = false;
        timerSeconds = 0;
        timerInitialAtStart = 0;
        updateTimerDisplay(0, false);
        if (!playedEndSound) {
            playedEndSound = true;
            playAudio(endAudio);
        }
    }
}

function fetchData() {
    var url = '/live/' + currentSlug + '/data' + (currentId ? ('/' + currentId) : '');
    fetch(url)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            updateDisplay(data);

            if (data.timer) {
                totalTimerSeconds = data.timer.total || 300;

                // JANGAN PERNAH mereset timer otomatis jika timer sedang jalan atau sudah berkurang!
                // Sinkronkan hanya jika timer lokal dalam kondisi diam di waktu penuh dan server memberi tahu bahwa timer jalan
                if (!isTimerRunning && timerSeconds === totalTimerSeconds && data.timer.is_running) {
                    isTimerRunning = true;
                    timerSeconds = data.timer.remaining;
                    timerInitialAtStart = data.timer.remaining;
                    timerStartedAt = Date.now();
                    updateTimerDisplay(timerSeconds, true);
                }
            }
        })
        .catch(function(err) { console.error('Poll error:', err); });
}

function navigateParticipant(direction) {
    var targetId = direction === 'next' ? nextId : prevId;
    if (targetId) {
        selectParticipant(targetId);
    }
}

function toggleTimer() {
    if (!currentId) return;

    if (!isTimerRunning) {
        // Jika sudah di 00:00 dan klik start lagi, baru mulai ulang dari awal
        if (timerSeconds <= 0) {
            timerSeconds = totalTimerSeconds;
            playedMidSound = false;
            playedEndSound = false;
        }
        isTimerRunning = true;
        timerStartedAt = Date.now();
        timerInitialAtStart = timerSeconds;
        updateTimerDisplay(timerSeconds, true);
        playAudio(startAudio); // Rule 4: bunyi sound saat start
        fetch('/live/' + currentSlug + '/timer/start?id=' + currentId);
    } else {
        // Pause
        timerSeconds = getRemainingSeconds();
        isTimerRunning = false;
        timerInitialAtStart = timerSeconds;
        updateTimerDisplay(timerSeconds, false);
        fetch('/live/' + currentSlug + '/timer/pause?id=' + currentId);
    }
}

function resetTimer() {
    if (!currentId) return;
    // Manual Reset hanya saat tombol Reset Timer diklik
    isTimerRunning = false;
    timerSeconds = totalTimerSeconds;
    timerInitialAtStart = totalTimerSeconds;
    playedMidSound = false;
    playedEndSound = false;
    updateTimerDisplay(totalTimerSeconds, false);
    fetch('/live/' + currentSlug + '/timer/reset?id=' + currentId);
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
setInterval(fetchData, 2500);
setInterval(function() {
    if (isTimerRunning) {
        tickTimer();
    }
}, 500);
</script>
</body>
</html>
