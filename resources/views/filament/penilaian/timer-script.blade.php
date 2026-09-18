<style>
@keyframes mtqSpin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
@keyframes mtqPulseGlowRed {
    0% { box-shadow: inset 0 0 0 2px #ef4444, 0 0 10px rgba(239, 68, 68, 0.35); }
    100% { box-shadow: inset 0 0 0 2px #ef4444, 0 0 22px rgba(239, 68, 68, 0.7); }
}

/* Timer Cell: ONLY display on the active row or when explicitly shown */
.timer-cell {
    display: none;
    align-items: center;
    justify-content: center;
    min-width: 62px;
    padding: 3px 10px;
    border-radius: 8px;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace !important;
    font-weight: 700 !important;
    font-size: 13.5px !important;
    box-sizing: border-box;
    letter-spacing: 0.5px;
    transition: all 0.25s ease;
}
tr.timer-active-row .timer-cell {
    display: inline-flex !important;
}

/* 1. GREEN PHASE (> 60s) */
.timer-cell.timer-phase-green,
tr.timer-active-row:not(.timer-phase-yellow):not(.timer-phase-red) .timer-cell {
    color: #10b981 !important;
    background: rgba(16, 185, 129, 0.16) !important;
    border: 1.5px solid rgba(16, 185, 129, 0.6) !important;
    box-shadow: 0 0 10px rgba(16, 185, 129, 0.35) !important;
}
tr.timer-active-row.timer-phase-green,
tr.timer-active-row:not(.timer-phase-yellow):not(.timer-phase-red) {
    box-shadow: inset 0 0 0 2px #10b981, 0 0 14px rgba(16, 185, 129, 0.35) !important;
    border-radius: 12px !important;
}
tr.timer-active-row.timer-phase-green > td,
tr.timer-active-row:not(.timer-phase-yellow):not(.timer-phase-red) > td {
    background-color: rgba(16, 185, 129, 0.12) !important;
    border-top: 2px solid #10b981 !important;
    border-bottom: 2px solid #10b981 !important;
}
tr.timer-active-row.timer-phase-green > td:first-child,
tr.timer-active-row:not(.timer-phase-yellow):not(.timer-phase-red) > td:first-child {
    border-left: 2.5px solid #10b981 !important;
    border-top-left-radius: 10px !important;
    border-bottom-left-radius: 10px !important;
}
tr.timer-active-row.timer-phase-green > td:last-child,
tr.timer-active-row:not(.timer-phase-yellow):not(.timer-phase-red) > td:last-child {
    border-right: 2.5px solid #10b981 !important;
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
}

/* 2. YELLOW PHASE (<= 60s) */
.timer-cell.timer-phase-yellow {
    color: #f59e0b !important;
    background: rgba(245, 158, 11, 0.2) !important;
    border: 1.5px solid rgba(245, 158, 11, 0.7) !important;
    box-shadow: 0 0 12px rgba(245, 158, 11, 0.4) !important;
}
tr.timer-active-row.timer-phase-yellow {
    box-shadow: inset 0 0 0 2px #f59e0b, 0 0 16px rgba(245, 158, 11, 0.4) !important;
    border-radius: 12px !important;
}
tr.timer-active-row.timer-phase-yellow > td {
    background-color: rgba(245, 158, 11, 0.14) !important;
    border-top: 2px solid #f59e0b !important;
    border-bottom: 2px solid #f59e0b !important;
}
tr.timer-active-row.timer-phase-yellow > td:first-child {
    border-left: 2.5px solid #f59e0b !important;
    border-top-left-radius: 10px !important;
    border-bottom-left-radius: 10px !important;
}
tr.timer-active-row.timer-phase-yellow > td:last-child {
    border-right: 2.5px solid #f59e0b !important;
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
}

/* 3. RED PHASE (<= 0s) */
.timer-cell.timer-phase-red {
    color: #ef4444 !important;
    background: rgba(239, 68, 68, 0.22) !important;
    border: 1.5px solid rgba(239, 68, 68, 0.8) !important;
    box-shadow: 0 0 14px rgba(239, 68, 68, 0.5) !important;
}
tr.timer-active-row.timer-phase-red {
    box-shadow: inset 0 0 0 2px #ef4444, 0 0 20px rgba(239, 68, 68, 0.5) !important;
    border-radius: 12px !important;
    animation: mtqPulseGlowRed 1s infinite alternate ease-in-out;
}
tr.timer-active-row.timer-phase-red > td {
    background-color: rgba(239, 68, 68, 0.16) !important;
    border-top: 2px solid #ef4444 !important;
    border-bottom: 2px solid #ef4444 !important;
}
tr.timer-active-row.timer-phase-red > td:first-child {
    border-left: 2.5px solid #ef4444 !important;
    border-top-left-radius: 10px !important;
    border-bottom-left-radius: 10px !important;
}
tr.timer-active-row.timer-phase-red > td:last-child {
    border-right: 2.5px solid #ef4444 !important;
    border-top-right-radius: 10px !important;
    border-bottom-right-radius: 10px !important;
}
</style>
<script>
(function() {
    var APP_BASE = '{{ url("/") }}';
    var audioUrls = {
        start: '{{ asset("sounds/mtqstart.mp3") }}',
        mid: '{{ asset("sounds/mtqmid.mp3") }}',
        end: '{{ asset("sounds/mtqend.mp3") }}'
    };

    var audioStart = new Audio(audioUrls.start);
    var audioMid = new Audio(audioUrls.mid);
    var audioEnd = new Audio(audioUrls.end);

    [audioStart, audioMid, audioEnd].forEach(function(a) {
        a.preload = 'auto';
        try { a.load(); } catch(e) {}
    });

    var audioCtx = null;
    function getAudioContext() {
        if (!audioCtx) {
            var AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
                audioCtx = new AudioContextClass();
            }
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume().catch(function() {});
        }
        return audioCtx;
    }

    function unlockAudioSystem() {
        getAudioContext();
        [audioStart, audioMid, audioEnd].forEach(function(a) {
            if (!a) return;
            try {
                a.muted = true;
                var p = a.play();
                if (p && typeof p.then === 'function') {
                    p.then(function() {
                        a.pause();
                        a.currentTime = 0;
                        a.muted = false;
                    }).catch(function() {
                        a.muted = false;
                    });
                } else {
                    a.muted = false;
                }
            } catch(e) {
                a.muted = false;
            }
        });
    }

    ['click', 'touchstart', 'keydown', 'mousedown'].forEach(function(evt) {
        document.addEventListener(evt, unlockAudioSystem, { once: false, passive: true });
    });

    function playToneBeep(count, type) {
        try {
            var ctx = getAudioContext();
            if (!ctx) return;
            var now = ctx.currentTime;
            var freq = (type === 'start') ? 880 : ((type === 'mid') ? 784 : 587);
            var duration = (type === 'start') ? 0.35 : ((type === 'mid') ? 0.28 : 0.4);
            var gap = duration + 0.12;

            for (var i = 0; i < count; i++) {
                var st = now + (i * gap);
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();

                osc.type = (type === 'end') ? 'triangle' : 'sine';
                osc.frequency.setValueAtTime(freq, st);
                if (type === 'end') {
                    osc.frequency.exponentialRampToValueAtTime(freq * 0.7, st + duration);
                }

                gain.gain.setValueAtTime(0.5, st);
                gain.gain.setValueAtTime(0.5, st + duration - 0.05);
                gain.gain.exponentialRampToValueAtTime(0.0001, st + duration);

                osc.connect(gain);
                gain.connect(ctx.destination);

                osc.start(st);
                osc.stop(st + duration);
            }
        } catch(e) {
            console.warn('Tone synth error:', e);
        }
    }

    function playBeeps(count, type) {
        unlockAudioSystem();
        var audio = (type === 'start') ? audioStart : ((type === 'mid') ? audioMid : audioEnd);
        var played = false;

        if (audio) {
            try {
                audio.currentTime = 0;
                var p = audio.play();
                if (p && typeof p.then === 'function') {
                    p.then(function() {
                        played = true;
                    }).catch(function(err) {
                        console.warn('Audio play blocked/failed, playing tone synth:', err);
                        playToneBeep(count, type);
                    });
                } else {
                    played = true;
                }
            } catch(e) {
                console.warn('Audio play exception, playing tone synth:', e);
                playToneBeep(count, type);
            }
        } else {
            playToneBeep(count, type);
        }
    }

    function updateRowAndCellPhase(cell, rem) {
        if (!cell) return;
        var row = cell.closest('tr');
        var phase = (rem <= 0) ? 'timer-phase-red' : ((rem <= 60) ? 'timer-phase-yellow' : 'timer-phase-green');

        cell.classList.remove('timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
        cell.classList.add(phase);

        if (row && row.classList.contains('timer-active-row')) {
            row.classList.remove('timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
            row.classList.add(phase);
        }
    }

    function formatTime(seconds, format) {
        var h = Math.floor(seconds / 3600);
        var m = Math.floor((seconds % 3600) / 60);
        var s = seconds % 60;
        var pad = function(n) { return n < 10 ? '0' + n : n; };
        if (format === 'hms') {
            return pad(h) + ':' + pad(m) + ':' + pad(s);
        }
        return pad(m) + ':' + pad(s);
    }

    function updateCellText(el, text) {
        if (!el) return;
        var target = el.querySelector('.fi-ta-text-item-label') || el.querySelector('span') || el;
        if (target) target.textContent = text;
    }

    function broadcastTimerSync(action, slug, recordId, remaining, total) {
        var payload = {
            slug: slug || 'tartil',
            action: action,
            recordId: recordId ? parseInt(recordId, 10) : null,
            remaining: remaining,
            total: total,
            isRunning: (action === 'start'),
            timestamp: Date.now()
        };
        try {
            if (window.BroadcastChannel) {
                var bc = new BroadcastChannel('mtq_timer_channel');
                bc.postMessage(payload);
            }
        } catch(e) {}
        try {
            localStorage.setItem('mtq_timer_sync_event', JSON.stringify(payload));
        } catch(e) {}
    }

    // Instant Toast Notification (100% Reliable, 0ms latency)
    function showNotification(title, type) {
        try {
            var container = document.getElementById('mtq-custom-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'mtq-custom-toast-container';
                container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none; max-width: 360px; width: calc(100vw - 40px);';
                document.body.appendChild(container);
            }

            var toast = document.createElement('div');
            toast.className = 'mtq-toast-card';
            toast.style.cssText = 'pointer-events: auto; cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 12px; background: #ffffff; color: #0f172a; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.18), 0 8px 10px -6px rgba(0,0,0,0.1); border: 1.5px solid transparent; font-family: inherit; font-size: 13.5px; font-weight: 700; opacity: 0; transform: translateY(-12px) scale(0.96); transition: all 0.16s cubic-bezier(0.16, 1, 0.3, 1);';

            var iconSvg = '';
            if (type === 'success') {
                toast.style.borderColor = '#10b981';
                iconSvg = '<svg style="width: 22px; height: 22px; color: #10b981; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
            } else if (type === 'warning') {
                toast.style.borderColor = '#f59e0b';
                iconSvg = '<svg style="width: 22px; height: 22px; color: #f59e0b; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>';
            } else if (type === 'danger') {
                toast.style.borderColor = '#ef4444';
                iconSvg = '<svg style="width: 22px; height: 22px; color: #ef4444; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>';
            } else {
                toast.style.borderColor = '#0284c7';
                iconSvg = '<svg style="width: 22px; height: 22px; color: #0284c7; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" /></svg>';
            }

            var closeSvg = '<svg style="width: 15px; height: 15px; color: #94a3b8; margin-left: auto; flex-shrink: 0;" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>';

            toast.innerHTML = iconSvg + '<span style="flex: 1;">' + title + '</span>' + closeSvg;

            var dismissed = false;
            function dismissToast() {
                if (dismissed) return;
                dismissed = true;
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px) scale(0.95)';
                setTimeout(function() { if (toast.parentNode) toast.remove(); }, 160);
            }

            toast.onclick = dismissToast;
            container.appendChild(toast);

            requestAnimationFrame(function() {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0) scale(1)';
            });

            setTimeout(dismissToast, 1800);
        } catch(e) {}
    }

    // Button state helpers
    function setBtnToPause(btn) {
        if (!btn) return;
        var path = btn.querySelector('svg path');
        if (path) path.setAttribute('d', 'M15.75 5.25v13.5m-7.5-13.5v13.5');
        btn.classList.remove('fi-color-success', 'fi-btn-color-success');
        btn.classList.add('fi-color-warning', 'fi-btn-color-warning');
        btn.style.setProperty('--c-400', 'var(--warning-400)');
        btn.style.setProperty('--c-500', 'var(--warning-500)');
        btn.style.setProperty('--c-600', 'var(--warning-600)');
        btn.setAttribute('title', 'Jeda Waktu');
    }

    function setBtnToPlay(btn) {
        if (!btn) return;
        var path = btn.querySelector('svg path');
        if (path) path.setAttribute('d', 'M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z');
        btn.classList.remove('fi-color-warning', 'fi-btn-color-warning');
        btn.classList.add('fi-color-success', 'fi-btn-color-success');
        btn.style.setProperty('--c-400', 'var(--success-400)');
        btn.style.setProperty('--c-500', 'var(--success-500)');
        btn.style.setProperty('--c-600', 'var(--success-600)');
        btn.setAttribute('title', 'Mulai Waktu');
    }

    function setBtnToShow(btn) {
        if (!btn) return;
        var path = btn.querySelector('svg path');
        if (path) path.setAttribute('d', 'M6 20.25h12m-7.5-3v3m3-3v3m-10.125-3h17.25c.621 0 1.125-.504 1.125-1.125V4.875c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125Z');
        btn.classList.remove('fi-color-gray', 'fi-btn-color-gray');
        btn.classList.add('fi-color-info', 'fi-btn-color-info');
        btn.style.setProperty('--c-400', 'var(--info-400)');
        btn.style.setProperty('--c-500', 'var(--info-500)');
        btn.style.setProperty('--c-600', 'var(--info-600)');
        btn.setAttribute('data-is-active', '0');
        btn.setAttribute('title', 'Tampilkan Peserta');
    }

    function setBtnToUnshow(btn) {
        if (!btn) return;
        var path = btn.querySelector('svg path');
        if (path) path.setAttribute('d', 'M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88');
        btn.classList.remove('fi-color-info', 'fi-btn-color-info');
        btn.classList.add('fi-color-gray', 'fi-btn-color-gray');
        btn.style.setProperty('--c-400', 'var(--gray-400)');
        btn.style.setProperty('--c-500', 'var(--gray-500)');
        btn.style.setProperty('--c-600', 'var(--gray-600)');
        btn.setAttribute('data-is-active', '1');
        btn.setAttribute('title', 'Sembunyikan Peserta');
    }

    var lastLocalActionAt = 0;
    var playedMidMap = {};
    var playedEndMap = {};

    // 1. Click anywhere on notification to dismiss instantly
    document.addEventListener('click', function(e) {
        var notif = e.target.closest('.fi-no-notification');
        if (notif) {
            notif.style.transition = 'opacity 0.15s ease, transform 0.15s ease';
            notif.style.opacity = '0';
            notif.style.transform = 'scale(0.95) translateY(-8px)';
            setTimeout(function() { notif.remove(); }, 150);
        }
    }, true);

    // 2. TOGGLE TIMER (MULAI / JEDA WAKTU)
    window.mtqToggleTimer = function(slug, recordId, btn) {
        lastLocalActionAt = Date.now();
        var row = btn ? btn.closest('tr') : null;
        var cell = row ? row.querySelector('.timer-cell') : document.querySelector('.timer-cell[data-record-id="' + recordId + '"]');

        // Only show timer on this row, hide all others
        if (cell) {
            cell.style.display = 'inline-flex';
        }
        document.querySelectorAll('.timer-cell').forEach(function(c) {
            if (c !== cell) c.style.display = 'none';
        });

        // Ensure this row is marked active and others are deactivated
        if (row) {
            document.querySelectorAll('tr.timer-active-row').forEach(function(r) {
                if (r !== row) r.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
            });
            row.classList.add('timer-active-row');

            var showBtn = row.querySelector('.btn-toggle-show-live');
            if (showBtn) {
                document.querySelectorAll('.btn-toggle-show-live').forEach(function(b) {
                    if (b !== showBtn) setBtnToShow(b);
                });
                setBtnToUnshow(showBtn);
            }
        }

        var isRunning = cell ? cell.getAttribute('data-is-running') === '1' : false;
        var rem = parseInt(cell ? cell.getAttribute('data-remaining') : '300', 10);
        var total = parseInt(cell ? cell.getAttribute('data-total-seconds') : '300', 10) || 300;
        if (isNaN(rem) || rem <= 0) rem = total;

        if (cell) {
            updateRowAndCellPhase(cell, rem);
        }

        if (!isRunning) {
            if (rem > 60) {
                playedMidMap[recordId] = false;
                playedEndMap[recordId] = false;
            } else if (rem > 0) {
                playedEndMap[recordId] = false;
            }
            if (cell) {
                cell.setAttribute('data-is-running', '1');
                cell.setAttribute('data-remaining', rem);
                updateCellText(cell, formatTime(rem, cell.getAttribute('data-format') || 'ms'));
                updateRowAndCellPhase(cell, rem);
            }
            // Reset other toggle buttons
            document.querySelectorAll('.btn-toggle-timer').forEach(function(b) {
                if (b !== btn) setBtnToPlay(b);
            });
            setBtnToPause(btn);
            playBeeps(1, 'start');
            showNotification('Timer dimulai', 'success');
            broadcastTimerSync('start', slug, recordId, rem, total);
            fetch(APP_BASE + '/live/' + slug + '/timer/start?id=' + recordId);
        } else {
            if (cell) {
                cell.setAttribute('data-is-running', '0');
                updateRowAndCellPhase(cell, rem);
            }
            setBtnToPlay(btn);
            showNotification('Timer dijeda', 'warning');
            broadcastTimerSync('pause', slug, recordId, rem, total);
            fetch(APP_BASE + '/live/' + slug + '/timer/pause?id=' + recordId);
        }
    };

    // 3. RESET TIMER (RESET WAKTU)
    window.mtqResetTimer = function(slug, recordId, btn) {
        lastLocalActionAt = Date.now();
        var row = btn ? btn.closest('tr') : null;
        var cell = row ? row.querySelector('.timer-cell') : document.querySelector('.timer-cell[data-record-id="' + recordId + '"]');
        var total = parseInt(btn.getAttribute('data-total-seconds') || (cell ? cell.getAttribute('data-total-seconds') : '300'), 10) || 300;

        playedMidMap[recordId] = false;
        playedEndMap[recordId] = false;

        if (cell) {
            cell.setAttribute('data-is-running', '0');
            cell.setAttribute('data-remaining', total);
            var format = cell.getAttribute('data-format') || 'ms';
            updateCellText(cell, formatTime(total, format));
            updateRowAndCellPhase(cell, total);
        }

        if (row) {
            var toggleBtn = row.querySelector('.btn-toggle-timer');
            if (toggleBtn) setBtnToPlay(toggleBtn);
        }

        showNotification('Timer direset', 'danger');
        broadcastTimerSync('reset', slug, recordId, total, total);
        fetch(APP_BASE + '/live/' + slug + '/timer/reset?id=' + recordId);
    };

    // 4. TOGGLE SHOW LIVE (TAMPILKAN / SEMBUNYIKAN PESERTA)
    window.mtqToggleShowLive = function(slug, recordId, btn) {
        lastLocalActionAt = Date.now();
        var row = btn ? btn.closest('tr') : null;
        var isActive = btn.getAttribute('data-is-active') === '1';
        var cell = row ? row.querySelector('.timer-cell') : document.querySelector('.timer-cell[data-record-id="' + recordId + '"]');

        if (isActive) {
            setBtnToShow(btn);
            if (row) {
                row.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
                var toggleBtn = row.querySelector('.btn-toggle-timer');
                if (toggleBtn) setBtnToPlay(toggleBtn);
            }
            if (cell) {
                cell.style.display = 'none';
                cell.setAttribute('data-is-running', '0');
            }
            showNotification('Peserta disembunyikan dari live score', 'warning');
            broadcastTimerSync('unshow_participant', slug, null, 0, 0);
            fetch(APP_BASE + '/live/' + slug + '/timer/unshow?id=' + recordId);
        } else {
            // Reset other buttons, rows, and cells
            document.querySelectorAll('.btn-toggle-show-live').forEach(function(b) {
                if (b !== btn) setBtnToShow(b);
            });
            document.querySelectorAll('tr.timer-active-row').forEach(function(r) {
                if (r !== row) r.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
            });
            document.querySelectorAll('.timer-cell').forEach(function(c) {
                if (c !== cell) {
                    c.style.display = 'none';
                    c.setAttribute('data-is-running', '0');
                }
            });

            setBtnToUnshow(btn);
            if (row) row.classList.add('timer-active-row');

            // Move & show timer ONLY on this row
            if (cell) {
                cell.style.display = 'inline-flex';
                var rem = parseInt(cell.getAttribute('data-remaining') || '300', 10);
                var format = cell.getAttribute('data-format') || 'ms';
                updateCellText(cell, formatTime(rem, format));
                updateRowAndCellPhase(cell, rem);
            }

            showNotification('Peserta ditampilkan di live score', 'success');
            broadcastTimerSync('show_participant', slug, recordId, 0, 0);
            fetch(APP_BASE + '/live/' + slug + '/timer/show?id=' + recordId)
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    if (data && data.timer && cell) {
                        cell.setAttribute('data-remaining', data.timer.remaining);
                        cell.setAttribute('data-total-seconds', data.timer.total);
                        cell.setAttribute('data-is-running', data.timer.is_running ? '1' : '0');
                        var format = cell.getAttribute('data-format') || 'ms';
                        updateCellText(cell, formatTime(data.timer.remaining, format));
                        updateRowAndCellPhase(cell, data.timer.remaining);
                        var toggleBtn = row ? row.querySelector('.btn-toggle-timer') : null;
                        if (toggleBtn) {
                            if (data.timer.is_running) setBtnToPause(toggleBtn);
                            else setBtnToPlay(toggleBtn);
                        }
                    }
                })
                .catch(function() {});
        }
    };

    // =========================================================================
    // 5. ULTRA-FAST INSTANT INPUT NILAI MODAL FOR ALL CABANG (0ms OPEN, 0ms BATAL, FAST SAVE)
    // =========================================================================
    var CABANG_FIELDS = {
        tartil: [
            { key: 'tajwid', label: 'Tajwid', max: 40 },
            { key: 'irama_dan_suara', label: 'Irama dan suara', max: 30 },
            { key: 'fashahah', label: 'Fashahah', max: 30 }
        ],
        anak: [
            { key: 'tajwid', label: 'Tajwid', max: 30 },
            { key: 'lagu', label: 'Lagu', max: 25 },
            { key: 'fashahah', label: 'Fashahah', max: 25 },
            { key: 'suara', label: 'Suara', max: 20 }
        ],
        remaja: [
            { key: 'tajwid', label: 'Tajwid', max: 30 },
            { key: 'lagu', label: 'Lagu', max: 25 },
            { key: 'fashahah', label: 'Fashahah', max: 25 },
            { key: 'suara', label: 'Suara', max: 20 }
        ],
        dewasa: [
            { key: 'tajwid', label: 'Tajwid', max: 30 },
            { key: 'lagu', label: 'Lagu', max: 25 },
            { key: 'fashahah', label: 'Fashahah', max: 25 },
            { key: 'suara', label: 'Suara', max: 20 }
        ],
        satujuz: [
            { key: 'til_tajwid', label: 'Tilawah - Tajwid', max: 30 },
            { key: 'til_lagu', label: 'Tilawah - Lagu', max: 25 },
            { key: 'til_suara', label: 'Tilawah - Suara', max: 15 },
            { key: 'til_fashahah', label: 'Tilawah - Fashahah', max: 30 },
            { key: 'tah_tahfizh', label: 'Tahfizh - Tahfizh', max: 50 },
            { key: 'tah_tajwid', label: 'Tahfizh - Tajwid', max: 25 },
            { key: 'tah_fashahah', label: 'Tahfizh - Fashahah', max: 25 }
        ],
        limajuz: [
            { key: 'til_tajwid', label: 'Tilawah - Tajwid', max: 30 },
            { key: 'til_lagu', label: 'Tilawah - Lagu', max: 25 },
            { key: 'til_suara', label: 'Tilawah - Suara', max: 15 },
            { key: 'til_fashahah', label: 'Tilawah - Fashahah', max: 30 },
            { key: 'tah_tahfizh', label: 'Tahfizh - Tahfizh', max: 50 },
            { key: 'tah_tajwid', label: 'Tahfizh - Tajwid', max: 25 },
            { key: 'tah_fashahah', label: 'Tahfizh - Fashahah', max: 25 }
        ],
        sepuluhjuz: [
            { key: 'tahfizh', label: 'Tahfizh', max: 50 },
            { key: 'tajwid', label: 'Tajwid', max: 25 },
            { key: 'fashahah', label: 'Fashahah', max: 25 }
        ],
        duapuluhjuz: [
            { key: 'tahfizh', label: 'Tahfizh', max: 50 },
            { key: 'tajwid', label: 'Tajwid', max: 25 },
            { key: 'fashahah', label: 'Fashahah', max: 25 }
        ],
        tigapuluhjuz: [
            { key: 'tahfizh', label: 'Tahfizh', max: 50 },
            { key: 'tajwid', label: 'Tajwid', max: 25 },
            { key: 'fashahah', label: 'Fashahah', max: 25 }
        ],
        mfq: [
            { key: 'total', label: 'Total Nilai', max: 100 }
        ],
        msq: [
            { key: 'terjemahan_dan_materi', label: 'Terjemah & Materi', max: 40 },
            { key: 'penghayatan_dan_retorika', label: 'Penghayatan & Retorika', max: 30 },
            { key: 'tilawah', label: 'Tilawah', max: 30 }
        ],
        mmq: [
            { key: 'bobot_materi', label: 'Bobot Materi', max: 40 },
            { key: 'kaidah_dan_gaya_bahasa', label: 'Kaidah & Gaya Bahasa', max: 25 },
            { key: 'logika_dan_organisasi_pesan', label: 'Logika & Organisasi', max: 20 },
            { key: 'presentasi', label: 'Presentasi', max: 15 }
        ],
        naskah: [
            { key: 'kebenaran_kaidah_khat_wajib', label: 'Kaidah Khat Wajib', max: 35 },
            { key: 'keindahan_khat_wajib', label: 'Keindahan Khat Wajib', max: 25 },
            { key: 'kebenaran_kaidah_khat_pilihan', label: 'Kaidah Khat Pilihan', max: 25 },
            { key: 'keindahan_khat_pilihan', label: 'Keindahan Khat Pilihan', max: 15 }
        ],
        mushaf: [
            { key: 'kebenaran_kaidah_khat', label: 'Kebenaran Kaidah Khat', max: 45 },
            { key: 'keindahan_khat', label: 'Keindahan Khat', max: 35 },
            { key: 'keindahan_hiasan_dan_lukisan', label: 'Keindahan Hiasan & Lukisan', max: 20 }
        ],
        dekorasi: [
            { key: 'kebenaran_kaidah_khath', label: 'Kebenaran Kaidah Khat', max: 45 },
            { key: 'keindahan_khath', label: 'Keindahan Khat', max: 35 },
            { key: 'keindahan_hiasan_dan_lukisan', label: 'Keindahan Hiasan & Lukisan', max: 20 }
        ],
        kontemporer: [
            { key: 'unsur_kaligrafi', label: 'Unsur Kaligrafi', max: 40 },
            { key: 'unsur_seni_rupa', label: 'Unsur Seni Rupa', max: 35 },
            { key: 'sentuhan_akhir', label: 'Sentuhan Akhir', max: 25 }
        ]
    };

    var currentEditRecord = {
        slug: 'tartil',
        id: null,
        nama: ''
    };

    function buildModalHtml() {
        if (document.getElementById('mtq-instant-score-modal')) return;

        var modal = document.createElement('div');
        modal.id = 'mtq-instant-score-modal';
        modal.style.cssText = 'display: none; position: fixed; inset: 0; z-index: 99999; align-items: center; justify-content: center; padding: 16px; font-family: inherit;';
        modal.innerHTML = [
            '<div id="mtq-modal-backdrop" style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.55); backdrop-filter: blur(4px);"></div>',
            '<div style="position: relative; z-index: 10; width: 95%; max-width: 680px; background: #ffffff; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; border: 1px solid #e2e8f0;">',
                '<!-- Header -->',
                '<div style="display: flex; align-items: flex-start; justify-content: space-between; padding: 20px 24px 16px; border-bottom: 1px solid #f1f5f9;">',
                    '<div>',
                        '<h3 style="margin: 0; font-size: 18px; font-weight: 700; color: #0f172a;">Input Nilai</h3>',
                        '<p style="margin: 4px 0 0; font-size: 12.5px; color: #64748b;">Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali</p>',
                    '</div>',
                    '<button type="button" onclick="window.mtqCloseInputNilai()" style="border: none; background: transparent; cursor: pointer; color: #94a3b8; padding: 4px; border-radius: 8px; margin-top: -2px; margin-right: -6px;">',
                        '<svg style="width: 20px; height: 20px;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>',
                    '</button>',
                '</div>',
                '<!-- Body -->',
                '<form id="mtq-score-form" onsubmit="window.mtqSubmitInputNilai(event)" style="padding: 20px 24px; display: flex; flex-direction: column; gap: 14px;">',
                    '<div>',
                        '<label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 6px;">Nama</label>',
                        '<input type="text" id="mtq-field-nama" readonly style="width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; background: #f8fafc; font-size: 13.5px; font-weight: 600; color: #64748b; outline: none;" />',
                    '</div>',
                    '<!-- Dynamic Fields Container -->',
                    '<div id="mtq-modal-fields-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 14px; max-height: 58vh; overflow-y: auto; padding-right: 4px;"></div>',
                    '<div id="mtq-modal-total-wrapper">',
                        '<label style="display: block; font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 6px;">Total</label>',
                        '<input type="text" id="mtq-field-total" readonly style="width: 100%; box-sizing: border-box; padding: 10px 12px; border: 2px solid #10b981; border-radius: 8px; background: #ecfdf5; font-size: 18px; font-weight: 800; color: #065f46; text-align: center; outline: none;" value="0.00" />',
                    '</div>',
                    '<!-- Footer Buttons -->',
                    '<div style="display: flex; align-items: center; gap: 10px; margin-top: 6px;">',
                        '<button type="submit" id="mtq-btn-save-score" style="padding: 9px 24px; background: #10b981; color: #ffffff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);">',
                            '<span id="mtq-btn-save-spinner" style="display: none; width: 15px; height: 15px; border: 2px solid #ffffff; border-top-color: transparent; border-radius: 50%; animation: mtqSpin 0.6s linear infinite;"></span>',
                            '<span>Simpan</span>',
                        '</button>',
                        '<button type="button" onclick="window.mtqCloseInputNilai()" style="padding: 9px 20px; background: #ffffff; color: #334155; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer;">',
                            'Batal',
                        '</button>',
                    '</div>',
                '</form>',
            '</div>',
        ].join('');

        document.body.appendChild(modal);

        var backdrop = document.getElementById('mtq-modal-backdrop');
        if (backdrop) {
            backdrop.onclick = function() { window.mtqCloseInputNilai(); };
        }
    }

    window.mtqOpenInputNilai = function(slug, recordId, nama, scoresOrTajwid, irama, fashahah, totalVal) {
        buildModalHtml();

        slug = (slug || 'tartil').toLowerCase();
        currentEditRecord.slug = slug;
        currentEditRecord.id = recordId;
        currentEditRecord.nama = nama || '';

        var scores = {};
        if (scoresOrTajwid && typeof scoresOrTajwid === 'object') {
            scores = scoresOrTajwid;
        } else {
            scores = {
                tajwid: parseFloat(scoresOrTajwid) || 0,
                irama_dan_suara: parseFloat(irama) || 0,
                fashahah: parseFloat(fashahah) || 0,
                total: parseFloat(totalVal) || 0
            };
        }

        document.getElementById('mtq-field-nama').value = currentEditRecord.nama;

        var fields = CABANG_FIELDS[slug] || [
            { key: 'total', label: 'Total Nilai', max: 100 }
        ];

        var container = document.getElementById('mtq-modal-fields-container');
        if (container) {
            container.innerHTML = '';
            fields.forEach(function(f) {
                var val = scores[f.key] !== undefined && scores[f.key] !== null ? scores[f.key] : '';
                if (val === 0 || val === '0') val = '';

                var rowDiv = document.createElement('div');
                rowDiv.innerHTML = [
                    '<label style="display: block; font-size: 13px; font-weight: 600; color: #334155; margin-bottom: 5px;">' + f.label + '</label>',
                    '<input type="number" step="0.01" min="0" max="' + f.max + '" data-key="' + f.key + '" data-max="' + f.max + '" class="mtq-dynamic-field" oninput="window.mtqRecalcScore()" value="' + val + '" style="width: 100%; box-sizing: border-box; padding: 9px 12px; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 14px; font-weight: 600; color: #0f172a; outline: none; transition: border-color 0.15s;" />',
                    '<div style="font-size: 11.5px; color: #64748b; margin-top: 3px;"><strong>Petunjuk :</strong> Input nilai maksimal ' + f.max + '</div>'
                ].join('');
                container.appendChild(rowDiv);
            });
        }

        var totalWrapper = document.getElementById('mtq-modal-total-wrapper');
        if (totalWrapper) {
            totalWrapper.style.display = (slug === 'mfq') ? 'none' : 'block';
        }

        var currentTotal = parseFloat(scores.total) || 0;
        document.getElementById('mtq-field-total').value = (currentTotal > 0) ? currentTotal.toFixed(2) : '0.00';

        var modal = document.getElementById('mtq-instant-score-modal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(function() {
                var firstInput = container ? container.querySelector('input') : null;
                if (firstInput) firstInput.focus();
            }, 50);
        }
    };

    window.mtqCloseInputNilai = function() {
        var modal = document.getElementById('mtq-instant-score-modal');
        if (modal) {
            modal.style.display = 'none';
        }
    };

    window.mtqRecalcScore = function() {
        var fields = document.querySelectorAll('.mtq-dynamic-field');
        var total = 0;
        fields.forEach(function(inp) {
            var max = parseFloat(inp.getAttribute('data-max')) || 100;
            var val = parseFloat(inp.value) || 0;
            if (val > max) {
                val = max;
                inp.value = max;
            }
            total += val;
        });

        var totEl = document.getElementById('mtq-field-total');
        if (totEl) {
            totEl.value = total.toFixed(2);
        }
    };

    window.mtqSubmitInputNilai = function(e) {
        if (e && typeof e.preventDefault === 'function') e.preventDefault();

        var recordId = currentEditRecord.id;
        var slug = currentEditRecord.slug;
        var payload = new URLSearchParams();
        payload.append('id', recordId);

        var fields = document.querySelectorAll('.mtq-dynamic-field');
        var total = 0;
        fields.forEach(function(inp) {
            var key = inp.getAttribute('data-key');
            var val = parseFloat(inp.value) || 0;
            payload.append(key, val);
            total += val;
        });

        // 1. Instant close modal (0ms perceived latency!)
        window.mtqCloseInputNilai();
        showNotification('Nilai berhasil disimpan', 'success');

        // 2. Instant optimistic DOM update for total in table
        var row = document.querySelector('tr.timer-active-row') || document.querySelector('[data-record-id="' + recordId + '"]')?.closest('tr');
        if (row) {
            var cells = row.querySelectorAll('td');
            cells.forEach(function(cell) {
                if (cell.classList.contains('fi-ta-col-total')) {
                    updateCellText(cell, total.toFixed(2));
                }
            });
        }

        // 3. Broadcast to live screen
        broadcastTimerSync('score_saved', slug, recordId, 0, 0);

        // 4. Background save
        fetch(APP_BASE + '/simpan-nilai/' + slug, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: payload.toString()
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data && data.total !== undefined && row) {
                var cells = row.querySelectorAll('td');
                cells.forEach(function(cell) {
                    if (cell.classList.contains('fi-ta-col-total')) {
                        updateCellText(cell, Number(data.total).toFixed(2));
                    }
                });
            }
        })
        .catch(function(err) {
            showNotification('Gagal menyimpan nilai ke server', 'danger');
        });
    };

    // Close modal on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.mtqCloseInputNilai();
        }
    });

    // 6. Real-time Countdown: Ticks down every 1000ms
    setInterval(function() {
        var cells = document.querySelectorAll('.timer-cell[data-is-running="1"]');
        cells.forEach(function(cell) {
            var rem = parseInt(cell.getAttribute('data-remaining'), 10);
            var recordId = cell.getAttribute('data-record-id');
            if (isNaN(rem)) return;

            if (rem > 0) {
                rem = rem - 1;
                cell.setAttribute('data-remaining', rem);

                var format = cell.getAttribute('data-format') || 'ms';
                updateCellText(cell, formatTime(rem, format));
                updateRowAndCellPhase(cell, rem);

                if (rem > 60) {
                    playedMidMap[recordId] = false;
                    playedEndMap[recordId] = false;
                } else if (rem <= 60 && rem > 0) {
                    playedEndMap[recordId] = false;
                    if (!playedMidMap[recordId]) {
                        playedMidMap[recordId] = true;
                        playBeeps(2, 'mid');
                    }
                }

                if (rem <= 0) {
                    cell.setAttribute('data-is-running', '0');
                    updateRowAndCellPhase(cell, 0);
                    var row = cell.closest('tr');
                    if (row) {
                        var toggleBtn = row.querySelector('.btn-toggle-timer');
                        if (toggleBtn) setBtnToPlay(toggleBtn);
                    }
                    if (!playedEndMap[recordId]) {
                        playedEndMap[recordId] = true;
                        playBeeps(3, 'end');
                    }
                }
            } else {
                cell.setAttribute('data-is-running', '0');
                var format = cell.getAttribute('data-format') || 'ms';
                updateCellText(cell, formatTime(0, format));
                updateRowAndCellPhase(cell, 0);
                var row = cell.closest('tr');
                if (row) {
                    var toggleBtn = row.querySelector('.btn-toggle-timer');
                    if (toggleBtn) setBtnToPlay(toggleBtn);
                }
                if (!playedEndMap[recordId]) {
                    playedEndMap[recordId] = true;
                    playBeeps(3, 'end');
                }
            }
        });
    }, 1000);

    // 7. Background Sync: Poll timer status from server every 1000ms
    setInterval(function() {
        if (Date.now() - lastLocalActionAt < 4000) {
            return;
        }

        fetch(APP_BASE + '/mtq-timer-status')
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (!data || !data.timers) return;
                var cells = document.querySelectorAll('.timer-cell');
                cells.forEach(function(cell) {
                    var slug = cell.getAttribute('data-slug') || 'tartil';
                    var recordId = parseInt(cell.getAttribute('data-record-id'), 10);
                    var tInfo = data.timers[slug];
                    var row = cell.closest('tr');

                    if (tInfo && tInfo.record_id && tInfo.record_id === recordId) {
                        cell.style.display = 'inline-flex';
                        cell.setAttribute('data-is-running', tInfo.is_running ? '1' : '0');
                        var localRem = parseInt(cell.getAttribute('data-remaining'), 10);
                        if (isNaN(localRem) || Math.abs(localRem - tInfo.remaining) > 2) {
                            cell.setAttribute('data-remaining', tInfo.remaining);
                            var format = cell.getAttribute('data-format') || 'ms';
                            updateCellText(cell, formatTime(tInfo.remaining, format));
                            localRem = tInfo.remaining;
                        }
                        updateRowAndCellPhase(cell, localRem);
                        if (row) {
                            row.classList.add('timer-active-row');
                            var toggleBtn = row.querySelector('.btn-toggle-timer');
                            if (toggleBtn) {
                                if (tInfo.is_running) setBtnToPause(toggleBtn);
                                else setBtnToPlay(toggleBtn);
                            }
                            var showBtn = row.querySelector('.btn-toggle-show-live');
                            if (showBtn) setBtnToUnshow(showBtn);
                        }
                    } else {
                        cell.style.display = 'none';
                        cell.setAttribute('data-is-running', '0');
                        if (row) {
                            row.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
                            var showBtn = row.querySelector('.btn-toggle-show-live');
                            if (showBtn) setBtnToShow(showBtn);
                            var toggleBtn = row.querySelector('.btn-toggle-timer');
                            if (toggleBtn) setBtnToPlay(toggleBtn);
                        }
                    }
                });
            })
            .catch(function() {});
    }, 1000);

    // 8. Instant Cross-Tab / Cross-Window Broadcast Listener
    try {
        if (window.BroadcastChannel) {
            var bcListen = new BroadcastChannel('mtq_timer_channel');
            bcListen.onmessage = function(e) {
                var ev = e.data;
                if (!ev) return;
                var cells = document.querySelectorAll('.timer-cell');
                cells.forEach(function(cell) {
                    var slug = cell.getAttribute('data-slug') || 'tartil';
                    if (slug !== ev.slug) return;
                    var recordId = parseInt(cell.getAttribute('data-record-id'), 10);
                    var row = cell.closest('tr');

                    if (ev.recordId && ev.recordId === recordId) {
                        cell.style.display = 'inline-flex';
                        if (row) row.classList.add('timer-active-row');
                        var showBtn = row ? row.querySelector('.btn-toggle-show-live') : null;
                        if (showBtn) setBtnToUnshow(showBtn);

                        if (ev.action === 'start') {
                            cell.setAttribute('data-is-running', '1');
                            if (ev.remaining !== undefined) cell.setAttribute('data-remaining', ev.remaining);
                            var format = cell.getAttribute('data-format') || 'ms';
                            var curRem = parseInt(cell.getAttribute('data-remaining'), 10);
                            updateCellText(cell, formatTime(curRem, format));
                            updateRowAndCellPhase(cell, curRem);
                            var toggleBtn = row ? row.querySelector('.btn-toggle-timer') : null;
                            if (toggleBtn) setBtnToPause(toggleBtn);
                        } else if (ev.action === 'pause') {
                            cell.setAttribute('data-is-running', '0');
                            if (ev.remaining !== undefined) cell.setAttribute('data-remaining', ev.remaining);
                            var format = cell.getAttribute('data-format') || 'ms';
                            var curRem = parseInt(cell.getAttribute('data-remaining'), 10);
                            updateCellText(cell, formatTime(curRem, format));
                            updateRowAndCellPhase(cell, curRem);
                            var toggleBtn = row ? row.querySelector('.btn-toggle-timer') : null;
                            if (toggleBtn) setBtnToPlay(toggleBtn);
                        } else if (ev.action === 'reset') {
                            cell.setAttribute('data-is-running', '0');
                            var total = ev.total || parseInt(cell.getAttribute('data-total-seconds') || '300', 10);
                            cell.setAttribute('data-remaining', total);
                            var format = cell.getAttribute('data-format') || 'ms';
                            updateCellText(cell, formatTime(total, format));
                            updateRowAndCellPhase(cell, total);
                            var toggleBtn = row ? row.querySelector('.btn-toggle-timer') : null;
                            if (toggleBtn) setBtnToPlay(toggleBtn);
                        }
                    } else if (ev.recordId && ev.recordId !== recordId) {
                        cell.style.display = 'none';
                        cell.setAttribute('data-is-running', '0');
                        if (row) {
                            row.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
                            var showBtn = row.querySelector('.btn-toggle-show-live');
                            if (showBtn) setBtnToShow(showBtn);
                            var toggleBtn = row.querySelector('.btn-toggle-timer');
                            if (toggleBtn) setBtnToPlay(toggleBtn);
                        }
                    } else if (ev.action === 'unshow_participant') {
                        cell.style.display = 'none';
                        cell.setAttribute('data-is-running', '0');
                        if (row) {
                            row.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
                            var showBtn = row.querySelector('.btn-toggle-show-live');
                            if (showBtn) setBtnToShow(showBtn);
                            var toggleBtn = row.querySelector('.btn-toggle-timer');
                            if (toggleBtn) setBtnToPlay(toggleBtn);
                        }
                    }
                });
            };
        }
    } catch(e) {}
})();
</script>