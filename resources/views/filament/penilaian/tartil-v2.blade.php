<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penilaian Tartil - MTQ Trenggalek</title>
    <link rel="icon" href="{{ url(asset('logotgxmini.png')) }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; overflow: hidden; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        body { background: #f5f6fa; color: #1a1a2e; }

        .container { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 60px 1fr 50px; height: 100vh; }

        .header { grid-column: 1 / -1; background: #1b5e20; color: #fff; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; font-size: 16px; font-weight: 600; }
        .header .badge { background: rgba(255,255,255,0.2); padding: 4px 12px; border-radius: 4px; font-size: 12px; }

        .left-panel { background: #fff; border-right: 1px solid #e0e0e0; display: flex; flex-direction: column; overflow: hidden; }
        .peserta-info { padding: 20px; border-bottom: 1px solid #eee; display: flex; gap: 12px; align-items: center; }
        .foto { width: 60px; height: 60px; border-radius: 6px; object-fit: cover; background: #e8e8e8; }
        .foto-placeholder { width: 60px; height: 60px; border-radius: 6px; background: #1b5e20; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: 700; }
        .info h2 { font-size: 16px; font-weight: 700; margin-bottom: 4px; }
        .info .meta { font-size: 12px; color: #666; }

        .timer-area { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px; }
        .timer-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 8px; }
        .timer-value { font-size: 64px; font-weight: 700; font-variant-numeric: tabular-nums; color: #1b5e20; line-height: 1; margin-bottom: 16px; }
        .timer-value.warning { color: #e65100; }
        .timer-value.danger { color: #c62828; }
        .timer-bar { width: 100%; max-width: 240px; height: 4px; background: #e0e0e0; border-radius: 2px; overflow: hidden; margin-bottom: 16px; }
        .timer-bar-fill { height: 100%; background: #1b5e20; transition: width 1s linear, background 0.3s; }
        .timer-bar-fill.warning { background: #e65100; }
        .timer-bar-fill.danger { background: #c62828; }
        .timer-btns { display: flex; gap: 6px; }
        .timer-btns button { padding: 6px 16px; border: none; border-radius: 4px; font-size: 12px; font-weight: 600; cursor: pointer; color: #fff; }
        .btn-start { background: #2e7d32; }
        .btn-pause { background: #f57c00; }
        .btn-reset { background: #c62828; }

        .right-panel { background: #fff; display: flex; flex-direction: column; overflow: hidden; }
        .form-header { padding: 16px 20px; border-bottom: 1px solid #eee; font-size: 14px; font-weight: 600; }
        .form-body { flex: 1; overflow-y: auto; padding: 20px; }

        .score-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid #f0f0f0; gap: 12px; }
        .score-item:last-child { border-bottom: none; }
        .score-label { flex: 1; }
        .score-label h4 { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .score-label p { font-size: 11px; color: #888; }
        .score-input { display: flex; align-items: center; gap: 6px; }
        .score-input input { width: 80px; padding: 8px; border: 2px solid #ddd; border-radius: 4px; font-size: 16px; font-weight: 600; text-align: center; }
        .score-input input:focus { outline: none; border-color: #1b5e20; }
        .score-max { font-size: 11px; color: #999; min-width: 35px; }

        .total-section { display: flex; justify-content: space-between; align-items: center; padding: 16px 0; margin-top: 8px; border-top: 2px solid #1b5e20; }
        .total-section h4 { font-size: 14px; font-weight: 700; }
        .total-value { font-size: 28px; font-weight: 800; color: #1b5e20; }

        .btn-save { width: 100%; padding: 12px; background: #1b5e20; color: #fff; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 12px; }
        .btn-save:hover { background: #2e7d32; }

        .footer { grid-column: 1 / -1; background: #fff; border-top: 1px solid #e0e0e0; display: flex; align-items: center; justify-content: space-between; padding: 0 20px; }
        .nav-btn { display: flex; align-items: center; gap: 8px; padding: 8px 16px; border: 1px solid #ddd; border-radius: 4px; background: #fff; font-size: 12px; text-decoration: none; color: #333; cursor: pointer; }
        .nav-btn:hover { background: #f5f5f5; }
        .nav-btn.disabled { opacity: 0.4; pointer-events: none; }
        .nav-info { font-size: 12px; color: #666; }
        .nav-info strong { color: #1a1a2e; }

        .empty { display: flex; align-items: center; justify-content: center; height: 100vh; flex-direction: column; gap: 8px; color: #888; }
        .empty .icon { font-size: 40px; }
        .empty h2 { font-size: 18px; color: #333; }
    </style>
</head>
<body>
@if($emptyState ?? false)
<div class="empty">
    <div class="icon">&#128203;</div>
    <h2>Belum Ada Peserta</h2>
    <p>Tidak ada peserta terdaftar untuk cabang Tartil.</p>
</div>
@else
<div class="container">
    <div class="header">
        <span>Penilaian Tartil</span>
        <span class="badge">MTQ Trenggalek {{ date('Y') }}</span>
    </div>

    <div class="left-panel">
        <div class="peserta-info">
            @if($currentRecord->peserta->pasfoto)
            <img src="{{ asset('storage/' . $currentRecord->peserta->pasfoto) }}" class="foto" alt="Foto">
            @else
            <div class="foto-placeholder">{{ substr($currentRecord->peserta->nama, 0, 1) }}</div>
            @endif
            <div class="info">
                <h2>{{ $currentRecord->peserta->nama }}</h2>
                <div class="meta">No. {{ $currentRecord->peserta->no_peserta }} &middot; {{ $currentRecord->peserta->tempat_lahir }}</div>
            </div>
        </div>

        <div class="timer-area">
            <div class="timer-label">Waktu Tersisa</div>
            <div class="timer-value" id="timerDisplay">{{ $timer }}</div>
            <div class="timer-bar"><div class="timer-bar-fill" id="timerBar" style="width: 100%"></div></div>
            <div class="timer-btns">
                <button class="btn-start" id="startBtn">Mulai</button>
                <button class="btn-pause" id="pauseBtn">Jeda</button>
                <button class="btn-reset" id="resetBtn">Reset</button>
            </div>
        </div>
    </div>

    <div class="right-panel">
        <div class="form-header">Form Penilaian</div>
        <div class="form-body">
            <form method="POST" action="{{ route('nilai-tartil-v2.update', $currentRecord->id) }}">
                @csrf
                @method('PUT')

                <div class="score-item">
                    <div class="score-label">
                        <h4>Tajwid</h4>
                        <p>Ketepatan bacaan sesuai kaidah tajwid</p>
                    </div>
                    <div class="score-input">
                        <input type="number" name="tajwid" id="tajwid" value="{{ $currentRecord->tajwid ?? 0 }}" min="0" max="40" step="0.1" oninput="calcTotal()">
                        <span class="score-max">/ 40</span>
                    </div>
                </div>

                <div class="score-item">
                    <div class="score-label">
                        <h4>Irama & Suara</h4>
                        <p>Keindahan irama dan kualitas suara</p>
                    </div>
                    <div class="score-input">
                        <input type="number" name="irama_suara" id="irama" value="{{ $currentRecord->irama_suara ?? 0 }}" min="0" max="40" step="0.1" oninput="calcTotal()">
                        <span class="score-max">/ 40</span>
                    </div>
                </div>

                <div class="score-item">
                    <div class="score-label">
                        <h4>Fashahah</h4>
                        <p>Kelancaran dan kejelasan bacaan</p>
                    </div>
                    <div class="score-input">
                        <input type="number" name="fashahah" id="fashahah" value="{{ $currentRecord->fashahah ?? 0 }}" min="0" max="20" step="0.1" oninput="calcTotal()">
                        <span class="score-max">/ 20</span>
                    </div>
                </div>

                <div class="total-section">
                    <h4>Total Nilai</h4>
                    <div class="total-value" id="totalValue">{{ ($currentRecord->tajwid ?? 0) + ($currentRecord->irama_suara ?? 0) + ($currentRecord->fashahah ?? 0) }}</div>
                </div>

                <button type="submit" class="btn-save">Simpan Nilai</button>
            </form>
        </div>
    </div>

    <div class="footer">
        @if($previousRecord)
        <a href="{{ route('nilai-tartil-v2.index', $previousRecord->id) }}" class="nav-btn">&larr; {{ $previousRecord->peserta->nama }}</a>
        @else
        <div class="nav-btn disabled">&larr; Sebelumnya</div>
        @endif

        <div class="nav-info">Peserta <strong>{{ $currentRecord->peserta->no_peserta }}</strong> dari <strong>{{ $records->count() }}</strong></div>

        @if($nextRecord)
        <a href="{{ route('nilai-tartil-v2.index', $nextRecord->id) }}" class="nav-btn">{{ $nextRecord->peserta->nama }} &rarr;</a>
        @else
        <div class="nav-btn disabled">Selanjutnya &rarr;</div>
        @endif
    </div>
</div>

<script>
var timerSec = {{ $timerInSeconds }}, initSec = timerSec, running = false, interval;
var timerEl = document.getElementById('timerDisplay'), barEl = document.getElementById('timerBar');

var soundStart = new Audio('{{ asset("sounds/mtqstart.mp3") }}');
var soundMid = new Audio('{{ asset("sounds/mtqmid.mp3") }}');
var soundEnd = new Audio('{{ asset("sounds/mtqend.mp3") }}');

function playAudio(audio) {
    if (!audio) return;
    try {
        audio.currentTime = 0;
        var p = audio.play();
        if (p && typeof p.catch === 'function') {
            p.catch(function(e) { console.warn(e); });
        }
    } catch(e) { console.warn(e); }
}

function fmt(s) { var h=Math.floor(s/3600),m=Math.floor((s%3600)/60),sec=s%60; return [h,m,sec].map(function(v){return v<10?'0'+v:v}).join(':'); }
function render() {
    timerEl.textContent = fmt(timerSec);
    var pct = initSec > 0 ? (timerSec/initSec*100) : 0;
    barEl.style.width = pct + '%';
    timerEl.className = 'timer-value'; barEl.className = 'timer-bar-fill';
    if (pct <= 20) { timerEl.classList.add('danger'); barEl.classList.add('danger'); }
    else if (pct <= 50) { timerEl.classList.add('warning'); barEl.classList.add('warning'); }
}
function tick() {
    if (timerSec > 0) {
        timerSec--;
        render();
        if (timerSec === 60) {
            playAudio(soundMid);
        }
        if (timerSec === 0) {
            clearInterval(interval);
            running = false;
            playAudio(soundEnd);
        }
    } else {
        clearInterval(interval);
        running = false;
    }
}

document.getElementById('startBtn').onclick = function() {
    if (timerSec <= 0) {
        timerSec = initSec;
        render();
    }
    if (!running && timerSec > 0) {
        running = true;
        playAudio(soundStart);
        interval = setInterval(tick, 1000);
    }
};
document.getElementById('pauseBtn').onclick = function() { running = false; clearInterval(interval); };
document.getElementById('resetBtn').onclick = function() { running = false; clearInterval(interval); timerSec = initSec; render(); };

function calcTotal() {
    var a = parseFloat(document.getElementById('tajwid').value)||0;
    var b = parseFloat(document.getElementById('irama').value)||0;
    var c = parseFloat(document.getElementById('fashahah').value)||0;
    document.getElementById('totalValue').textContent = (a+b+c).toFixed(1);
}

render();
</script>
@endif
</body>
</html>
