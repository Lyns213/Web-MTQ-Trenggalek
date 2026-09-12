@php
    $curr = $initialData['current'] ?? null;
    $timer = $initialData['timer'] ?? null;
    $tajwid = (float)($curr['tajwid'] ?? 0);
    $irama = (float)($curr['irama_dan_suara'] ?? 0);
    $fashahah = (float)($curr['fashahah'] ?? 0);
    $total = (float)($curr['total'] ?? ($tajwid + $irama + $fashahah));
    $tajwidPct = min(100, max(0, ($tajwid / 40) * 100));
    $iramaPct = min(100, max(0, ($irama / 30) * 100));
    $fashahahPct = min(100, max(0, ($fashahah / 30) * 100));
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Score Board - MTQ Kabupaten Trenggalek 2026</title>
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
        /* Exact coordinates: left: 236px, top: 212px, width: 312px, height: 437px */
        .card-participant {
            position: absolute;
            left: 236px;
            top: 212px;
            width: 312px;
            height: 437px;
            background: #ffffff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            z-index: 10;
        }

        .participant-photo-wrap {
            width: 312px;
            height: 242px;
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
            font-size: 80px;
        }

        .participant-navy-box {
            background: linear-gradient(180deg, #0d2843 0%, #061628 100%);
            border-top: 4px solid #caa44e;
            padding: 14px 18px 10px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 125px;
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
            font-size: 24px;
            line-height: 1.15;
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
            font-size: 13px;
            color: #e5b958;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .participant-white-box {
            background: #ffffff;
            padding: 0 22px;
            display: flex;
            align-items: center;
            height: 70px;
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
            font-size: 11px;
            font-weight: 800;
            color: #111111;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .origin-value {
            font-family: 'Montserrat', sans-serif;
            font-size: 21px;
            font-weight: 900;
            color: #0c233c;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }

        /* ===== SCORE CARD ===== */
        /* Exact coordinates: left: 564px, top: 212px, width: 582px, height: 437px */
        .card-score {
            position: absolute;
            left: 564px;
            top: 212px;
            width: 582px;
            height: 437px;
            background: linear-gradient(180deg, #0d2843 0%, #0a2037 40%, #061626 100%);
            border: 2px solid #527598;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            padding: 16px 24px 18px 24px;
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
            font-size: 24px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .timer-digits {
            font-family: 'Montserrat', sans-serif;
            font-size: 60px;
            font-weight: 800;
            color: #ffffff;
            margin-left: 14px;
            font-variant-numeric: tabular-nums;
            letter-spacing: 1px;
            line-height: 1;
            text-shadow: 0 0 15px rgba(255, 255, 255, 0.25);
        }

        .badge-live-top {
            background: #e53935;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 13px;
            font-weight: 800;
            padding: 5px 14px;
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
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 2px;
            margin-bottom: 6px;
        }
        .cabang-gold { color: #e5b958; }
        .cabang-white { color: #ffffff; }

        /* Score items */
        .score-field {
            display: flex;
            flex-direction: column;
            margin-bottom: 4px;
        }

        .score-field-top {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 4px;
        }

        .score-field-label {
            font-family: 'Montserrat', sans-serif;
            font-size: 18px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .score-field-num {
            font-family: 'Montserrat', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
        }

        .score-track {
            width: 100%;
            height: 16px;
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
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 6px;
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
            right: 24px;
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
            font-size: 28px;
            font-weight: 800;
            color: #e5b958;
            letter-spacing: 1.2px;
            text-transform: uppercase;
        }

        .total-text-digits {
            font-family: 'Montserrat', sans-serif;
            font-size: 64px;
            font-weight: 800;
            color: #ffffff;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
            line-height: 1;
            text-shadow: 0 0 20px rgba(255, 235, 160, 0.85), 0 0 38px rgba(212, 175, 55, 0.45);
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
                <span class="timer-digits" id="timerVal">{{ $timer['formatted'] ?? '05:00' }}</span>
            </div>
            <div class="badge-live-top">
                <span class="dot-blink"></span> LIVE
            </div>
        </div>

        <div class="cabang-subtitle" id="cabangTitle">
            <span class="cabang-gold">PENILAIAN LIVE</span> <span class="cabang-white">(CABANG {{ $curr['cabang'] ?? 'TARTIL' }})</span>
        </div>

        <!-- TAJWID -->
        <div class="score-field">
            <div class="score-field-top">
                <span class="score-field-label">TAJWID</span>
                <span class="score-field-num" id="sTajwid">{{ number_format($tajwid, 2) }}</span>
            </div>
            <div class="score-track">
                <div class="score-fill" id="bTajwid" style="width: {{ $tajwidPct }}%;"></div>
            </div>
        </div>

        <!-- IRAMA -->
        <div class="score-field">
            <div class="score-field-top">
                <span class="score-field-label">IRAMA</span>
                <span class="score-field-num" id="sIrama">{{ number_format($irama, 2) }}</span>
            </div>
            <div class="score-track">
                <div class="score-fill" id="bIrama" style="width: {{ $iramaPct }}%;"></div>
            </div>
        </div>

        <!-- FASAHAH -->
        <div class="score-field">
            <div class="score-field-top">
                <span class="score-field-label">FASAHAH</span>
                <span class="score-field-num" id="sFashahah">{{ number_format($fashahah, 2) }}</span>
            </div>
            <div class="score-track">
                <div class="score-fill" id="bFashahah" style="width: {{ $fashahahPct }}%;"></div>
            </div>
        </div>

        <!-- TOTAL SKOR -->
        <div class="total-container">
            <div class="badge-live-update">LIVE UPDATE</div>
            <span class="total-text-label">TOTAL SKOR :</span>
            <span class="total-text-digits" id="sTotal">{{ number_format($total, 2) }}</span>
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
let currentId = {{ $curr['id'] ?? 'null' }};
let nextId = {{ $initialData['next']['id'] ?? 'null' }};
let prevId = {{ $initialData['previous']['id'] ?? 'null' }};
let srvRem = {{ $timer['remaining'] ?? 300 }};
let srvRun = {{ ($timer['is_running'] ?? false) ? 'true' : 'false' }};
let lastUpd = Math.floor(Date.now() / 1000);

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

function updateDisplay(data) {
    if (data.empty) {
        document.getElementById('pName').textContent = 'BELUM ADA PESERTA';
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

    var t = parseFloat(data.current.tajwid) || 0;
    var i = parseFloat(data.current.irama_dan_suara) || 0;
    var f = parseFloat(data.current.fashahah) || 0;
    var tot = parseFloat(data.current.total) || (t + i + f);

    document.getElementById('sTajwid').textContent = t.toFixed(2);
    document.getElementById('sIrama').textContent = i.toFixed(2);
    document.getElementById('sFashahah').textContent = f.toFixed(2);
    document.getElementById('sTotal').textContent = tot.toFixed(2);

    document.getElementById('bTajwid').style.width = Math.min(100, Math.max(0, (t / 40) * 100)) + '%';
    document.getElementById('bIrama').style.width = Math.min(100, Math.max(0, (i / 30) * 100)) + '%';
    document.getElementById('bFashahah').style.width = Math.min(100, Math.max(0, (f / 30) * 100)) + '%';
}

function tickTimer() {
    var elapsed = Math.floor(Date.now() / 1000) - lastUpd;
    var remaining = Math.max(0, srvRem - elapsed);
    document.getElementById('timerVal').textContent = fmtTime(remaining);
}

function fetchData() {
    var url = '/live-tartil/data' + (currentId ? ('/' + currentId) : '');
    fetch(url)
        .then(function(res) { return res.json(); })
        .then(function(data) {
            updateDisplay(data);
            if (data.timer) {
                srvRem = data.timer.remaining;
                srvRun = data.timer.is_running;
                lastUpd = Math.floor(Date.now() / 1000);
                document.getElementById('timerVal').textContent = data.timer.formatted;
            }
        })
        .catch(function(err) { console.error('Poll error:', err); });
}

function navigateParticipant(direction) {
    var targetId = direction === 'next' ? nextId : prevId;
    if (targetId) {
        currentId = targetId;
        fetchData();
    }
}

function toggleTimer() {
    if (!currentId) return;
    var action = srvRun ? 'pause' : 'start';
    fetch('/live-tartil/timer/' + action + '?id=' + currentId)
        .then(function() { fetchData(); });
}

function resetTimer() {
    if (!currentId) return;
    fetch('/live-tartil/timer/reset?id=' + currentId)
        .then(function() { fetchData(); });
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

fetchData();
setInterval(fetchData, 2500);
setInterval(function() {
    if (srvRun) {
        tickTimer();
    }
}, 1000);
</script>
</body>
</html>
