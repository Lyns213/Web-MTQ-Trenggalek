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
    color: #ffffff !important;
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
    color: #ffffff !important;
    background: rgba(16, 185, 129, 0.18) !important;
    border: 1.5px solid rgba(16, 185, 129, 0.7) !important;
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
    color: #ffffff !important;
    background: rgba(245, 158, 11, 0.25) !important;
    border: 1.5px solid rgba(245, 158, 11, 0.8) !important;
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
    color: #ffffff !important;
    background: rgba(239, 68, 68, 0.25) !important;
    border: 1.5px solid rgba(239, 68, 68, 0.85) !important;
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

    var soundUrls = {
        start: '{{ asset("sounds/mtqstart.mp3") }}',
        mid: '{{ asset("sounds/mtqmid.mp3") }}',
        end: '{{ asset("sounds/mtqend.mp3") }}'
    };

    // 1. Preload HTML5 Audio objects
    var audioElements = {
        start: new Audio(soundUrls.start),
        mid: new Audio(soundUrls.mid),
        end: new Audio(soundUrls.end)
    };
    Object.keys(audioElements).forEach(function(k) {
        audioElements[k].preload = 'auto';
        try { audioElements[k].load(); } catch(e) {}
    });

    // 2. Pre-decode into Web Audio API buffers for 0ms latency playback
    var audioCtx = null;
    function getAudioCtx() {
        if (!audioCtx) {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (AC) audioCtx = new AC();
        }
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume().catch(function() {});
        }
        return audioCtx;
    }

    var soundBuffers = {};
    function preloadSoundBuffer(type, url) {
        fetch(url)
            .then(function(r) { return r.arrayBuffer(); })
            .then(function(buf) {
                var ctx = getAudioCtx();
                if (ctx) {
                    ctx.decodeAudioData(buf, function(decoded) {
                        soundBuffers[type] = decoded;
                    }, function() {});
                }
            })
            .catch(function() {});
    }

    preloadSoundBuffer('start', soundUrls.start);
    preloadSoundBuffer('mid', soundUrls.mid);
    preloadSoundBuffer('end', soundUrls.end);

    ['click', 'touchstart', 'keydown', 'mousedown'].forEach(function(evt) {
        document.addEventListener(evt, function() {
            getAudioCtx();
        }, { once: false, passive: true });
    });

    function playFallbackAudio(type) {
        var a = audioElements[type];
        if (a) {
            try {
                a.currentTime = 0;
                var p = a.play();
                if (p && typeof p.catch === 'function') {
                    p.catch(function(e) {
                        try {
                            var directAudio = new Audio(soundUrls[type]);
                            directAudio.play().catch(function() {});
                        } catch(err) {}
                    });
                }
            } catch(e) {}
        }
    }

    function playBeeps(count, type) {
        var ctx = getAudioCtx();
        if (ctx && soundBuffers[type]) {
            var playBuffer = function() {
                try {
                    var src = ctx.createBufferSource();
                    src.buffer = soundBuffers[type];
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

    function getCellRecordId(cell) {
        if (!cell) return null;
        var rawId = cell.getAttribute('data-record-id');
        if (rawId && !isNaN(parseInt(rawId, 10))) {
            return parseInt(rawId, 10);
        }
        var row = cell.closest('tr');
        if (row) {
            var el = row.querySelector('[data-record-id]');
            if (el && el.getAttribute('data-record-id')) {
                var val = parseInt(el.getAttribute('data-record-id'), 10);
                if (!isNaN(val)) {
                    cell.setAttribute('data-record-id', val);
                    return val;
                }
            }
            var cellKey = cell.closest('[wire\\:key]');
            if (cellKey) {
                var k = cellKey.getAttribute('wire:key') || '';
                var m = k.match(/\.record\.(\d+)\./);
                if (m && m[1]) {
                    var val2 = parseInt(m[1], 10);
                    cell.setAttribute('data-record-id', val2);
                    return val2;
                }
            }
        }
        return null;
    }

    function getPageSlug() {
        var path = (window.location.pathname || '').toLowerCase();
        if (path.indexOf('nilai-anaks') !== -1) return 'anak';
        if (path.indexOf('nilai-dekorasis') !== -1) return 'dekorasi';
        if (path.indexOf('nilai-dewasas') !== -1) return 'dewasa';
        if (path.indexOf('nilai-duapuluh-juzs') !== -1) return 'duapuluhjuz';
        if (path.indexOf('nilai-kontemporers') !== -1) return 'kontemporer';
        if (path.indexOf('nilai-lima-juzs') !== -1) return 'limajuz';
        if (path.indexOf('nilai-mfqs') !== -1) return 'mfq';
        if (path.indexOf('nilai-mmqs') !== -1) return 'mmq';
        if (path.indexOf('nilai-msqs') !== -1) return 'msq';
        if (path.indexOf('nilai-mushafs') !== -1) return 'mushaf';
        if (path.indexOf('nilai-naskahs') !== -1) return 'naskah';
        if (path.indexOf('nilai-remajas') !== -1) return 'remaja';
        if (path.indexOf('nilai-satu-juzs') !== -1) return 'satujuz';
        if (path.indexOf('nilai-sepuluh-juzs') !== -1) return 'sepuluhjuz';
        if (path.indexOf('nilai-tartils') !== -1) return 'tartil';
        if (path.indexOf('nilai-tigapuluh-juzs') !== -1) return 'tigapuluhjuz';
        return 'tartil';
    }

    function getCellSlug(cell) {
        if (cell) {
            var s = cell.getAttribute('data-slug');
            if (s) return s;
            var row = cell.closest('tr');
            if (row) {
                var el = row.querySelector('[data-slug]');
                if (el && el.getAttribute('data-slug')) {
                    var sVal = el.getAttribute('data-slug');
                    cell.setAttribute('data-slug', sVal);
                    return sVal;
                }
            }
        }
        return getPageSlug();
    }

    function updateRowAndCellPhase(cell, rem) {
        if (!cell) return;
        var row = cell.closest('tr');
        var phase = (rem <= 0) ? 'timer-phase-red' : ((rem <= 60) ? 'timer-phase-yellow' : 'timer-phase-green');

        cell.classList.remove('timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
        cell.classList.add(phase);

        if (row) {
            row.classList.remove('timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
            if (row.classList.contains('timer-active-row')) {
                row.classList.add(phase);
            }
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

    function broadcastTimerSync(action, slug, recordId, remaining, total, scores) {
        var payload = {
            slug: slug || 'tartil',
            action: action,
            recordId: recordId ? parseInt(recordId, 10) : null,
            remaining: remaining,
            total: total,
            scores: scores || null,
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

    function updateShowLiveButtons(activeRecordId) {
        document.querySelectorAll('.btn-show-live, .btn-toggle-show-live').forEach(function(b) {
            var rid = parseInt(b.getAttribute('data-record-id'), 10);
            if (rid === activeRecordId) {
                b.classList.remove('fi-color-info', 'fi-btn-color-info');
                b.classList.add('fi-color-warning', 'fi-btn-color-warning');
                b.style.setProperty('--c-400', 'var(--warning-400)');
                b.style.setProperty('--c-500', 'var(--warning-500)');
                b.style.setProperty('--c-600', 'var(--warning-600)');
            } else {
                b.classList.remove('fi-color-warning', 'fi-btn-color-warning');
                b.classList.add('fi-color-info', 'fi-btn-color-info');
                b.style.setProperty('--c-400', 'var(--info-400)');
                b.style.setProperty('--c-500', 'var(--info-500)');
                b.style.setProperty('--c-600', 'var(--info-600)');
            }
            b.setAttribute('title', 'Tampilkan Peserta');
        });
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
        recordId = parseInt(recordId, 10);
        var row = btn ? btn.closest('tr') : document.querySelector('[data-record-id="' + recordId + '"]')?.closest('tr');
        var cell = row ? row.querySelector('.timer-cell') : document.querySelector('.timer-cell[data-record-id="' + recordId + '"]');

        var isRunning = cell ? cell.getAttribute('data-is-running') === '1' : false;
        var rem = parseInt(cell ? cell.getAttribute('data-remaining') : '300', 10);
        var total = parseInt(cell ? cell.getAttribute('data-total-seconds') : '300', 10) || 300;
        if (isNaN(rem) || rem <= 0) rem = total;

        if (!isRunning) {
            // PLAY AUDIO & VISUAL INSTANTLY (0ms latency!)
            playBeeps(1, 'start');
            setBtnToPause(btn);
            showNotification('Timer dimulai', 'success');

            // Only show timer on this row, hide all others
            if (cell) {
                cell.style.display = 'inline-flex';
                cell.setAttribute('data-is-running', '1');
                cell.setAttribute('data-remaining', rem);
                var format = cell.getAttribute('data-format') || 'ms';
                updateCellText(cell, formatTime(rem, format));
                updateRowAndCellPhase(cell, rem);
            }
            document.querySelectorAll('.timer-cell').forEach(function(c) {
                if (c !== cell) {
                    c.style.display = 'none';
                    c.setAttribute('data-is-running', '0');
                }
            });

            if (row) {
                document.querySelectorAll('tr.timer-active-row').forEach(function(r) {
                    if (r !== row) r.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
                });
                row.classList.add('timer-active-row');
                updateShowLiveButtons(recordId);
            }

            if (rem >= 60) {
                playedMidMap[recordId] = false;
                playedEndMap[recordId] = false;
            } else if (rem > 0) {
                playedEndMap[recordId] = false;
            }

            document.querySelectorAll('.btn-toggle-timer').forEach(function(b) {
                if (b !== btn) setBtnToPlay(b);
            });

            broadcastTimerSync('start', slug, recordId, rem, total);
            fetch(APP_BASE + '/live/' + slug + '/timer/start?id=' + recordId + '&remaining=' + rem);
        } else {
            // PAUSE: hitung sisa waktu dari cell sebelum stop
            var pauseRem = parseInt(cell ? cell.getAttribute('data-remaining') : '0', 10);
            if (isNaN(pauseRem) || pauseRem < 0) pauseRem = 0;
            if (cell) {
                cell.setAttribute('data-is-running', '0');
                updateRowAndCellPhase(cell, pauseRem);
            }
            setBtnToPlay(btn);
            showNotification('Timer dijeda', 'warning');
            broadcastTimerSync('pause', slug, recordId, pauseRem, total);
            fetch(APP_BASE + '/live/' + slug + '/timer/pause?id=' + recordId + '&remaining=' + pauseRem);
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
        fetch(APP_BASE + '/live/' + slug + '/timer/reset?id=' + recordId + '&remaining=' + total);
    };

    // 4. SHOW LIVE (TAMPILKAN PESERTA KE LAYAR LIVE)
    window.mtqShowLive = function(slug, recordId, btn) {
        lastLocalActionAt = Date.now();
        recordId = parseInt(recordId, 10);
        var row = btn ? btn.closest('tr') : document.querySelector('[data-record-id="' + recordId + '"]')?.closest('tr');
        var cell = row ? row.querySelector('.timer-cell') : document.querySelector('.timer-cell[data-record-id="' + recordId + '"]');

        // Reset all other rows and cells
        document.querySelectorAll('tr.timer-active-row').forEach(function(r) {
            if (r !== row) r.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
        });
        document.querySelectorAll('.timer-cell').forEach(function(c) {
            if (c !== cell) {
                c.style.display = 'none';
                c.setAttribute('data-is-running', '0');
            }
        });

        // Set this row active
        if (row) row.classList.add('timer-active-row');
        updateShowLiveButtons(recordId);

        // Move & show timer ONLY on this row (STOPPED by default!)
        if (cell) {
            cell.style.display = 'inline-flex';
            cell.setAttribute('data-is-running', '0');
            var rem = parseInt(cell.getAttribute('data-remaining') || '300', 10);
            var format = cell.getAttribute('data-format') || 'ms';
            updateCellText(cell, formatTime(rem, format));
            updateRowAndCellPhase(cell, rem);
        }

        // Ensure toggle timer button is in Play state
        if (row) {
            var toggleBtn = row.querySelector('.btn-toggle-timer');
            if (toggleBtn) setBtnToPlay(toggleBtn);
        }

        showNotification('Peserta ditampilkan di live score', 'success');
        broadcastTimerSync('show_participant', slug, recordId, 0, 0);
        fetch(APP_BASE + '/live/' + slug + '/timer/show?id=' + recordId)
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.timer && cell) {
                    cell.setAttribute('data-remaining', data.timer.remaining);
                    cell.setAttribute('data-total-seconds', data.timer.total);
                    cell.setAttribute('data-is-running', '0');
                    var format = cell.getAttribute('data-format') || 'ms';
                    updateCellText(cell, formatTime(data.timer.remaining, format));
                    updateRowAndCellPhase(cell, data.timer.remaining);
                }
            })
            .catch(function() {});
    };
    window.mtqToggleShowLive = window.mtqShowLive; // backward compatibility

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
        if (window.mtqSavedScores && window.mtqSavedScores[recordId]) {
            scores = Object.assign({}, window.mtqSavedScores[recordId]);
        } else if (scoresOrTajwid && typeof scoresOrTajwid === 'object') {
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
        var scoreMap = {};
        fields.forEach(function(inp) {
            var key = inp.getAttribute('data-key');
            var val = parseFloat(inp.value) || 0;
            payload.append(key, val);
            scoreMap[key] = val;
            total += val;
        });

        // 1. Instant close modal (0ms perceived latency!)
        window.mtqCloseInputNilai();
        showNotification('Nilai berhasil disimpan', 'success');

        // Store saved scores so next modal open has latest values
        if (!window.mtqSavedScores) window.mtqSavedScores = {};
        scoreMap.total = total;
        window.mtqSavedScores[recordId] = scoreMap;

        // 2. Instant optimistic DOM update for table row
        var targetRow = document.querySelector('.btn-input-nilai[data-record-id="' + recordId + '"]')?.closest('tr')
            || document.querySelector('[data-record-id="' + recordId + '"]')?.closest('tr')
            || document.querySelector('tr.timer-active-row');

        if (targetRow) {
            // Update individual subscore cells
            Object.keys(scoreMap).forEach(function(k) {
                var kebabK = k.replace(/_/g, '-');
                var cell = targetRow.querySelector('.fi-table-cell-' + kebabK)
                    || targetRow.querySelector('[wire\\:key*="column.' + k + '"]')
                    || targetRow.querySelector('[wire\\:key*="column.' + kebabK + '"]');
                if (cell) {
                    updateCellText(cell, parseFloat(scoreMap[k]).toFixed(2));
                }
            });

            // Update total cell
            var totalCell = targetRow.querySelector('.fi-table-cell-total')
                || targetRow.querySelector('[wire\\:key*="column.total"]')
                || targetRow.querySelector('.fi-ta-col-total');
            if (totalCell) {
                updateCellText(totalCell, total.toFixed(2));
            }

            // Update inputNilai button icon & color from green plus to blue eye (Lihat/Edit)
            var inputBtn = targetRow.querySelector('.btn-input-nilai[data-record-id="' + recordId + '"]');
            if (inputBtn && total > 0) {
                inputBtn.setAttribute('title', 'Lihat / Edit Nilai');
                inputBtn.classList.remove('fi-color-success', 'fi-btn-color-success');
                inputBtn.classList.add('fi-color-info', 'fi-btn-color-info');
                var path = inputBtn.querySelector('svg path');
                if (path) {
                    path.setAttribute('d', 'M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z');
                }
            }
        }

        // 3. Broadcast to live screen (kirim scoreMap saja, jangan ubah timer)
        broadcastTimerSync('score_saved', slug, recordId, null, null, scoreMap);

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
            if (data && data.total !== undefined && targetRow) {
                var totalCell = targetRow.querySelector('.fi-table-cell-total')
                    || targetRow.querySelector('[wire\\:key*="column.total"]')
                    || targetRow.querySelector('.fi-ta-col-total');
                if (totalCell) {
                    updateCellText(totalCell, Number(data.total).toFixed(2));
                }
                if (data.fields) {
                    Object.keys(data.fields).forEach(function(k) {
                        var kebabK = k.replace(/_/g, '-');
                        var cell = targetRow.querySelector('.fi-table-cell-' + kebabK)
                            || targetRow.querySelector('[wire\\:key*="column.' + k + '"]')
                            || targetRow.querySelector('[wire\\:key*="column.' + kebabK + '"]');
                        if (cell) {
                            updateCellText(cell, Number(data.fields[k]).toFixed(2));
                        }
                    });
                }
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
        var cells = document.querySelectorAll('.timer-cell');
        cells.forEach(function(cell) {
            var isRunning = (cell.getAttribute('data-is-running') === '1');
            if (!isRunning) return;
            var rem = parseInt(cell.getAttribute('data-remaining'), 10);
            var recordId = getCellRecordId(cell);
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

    // 7. Instant Cross-Tab / Cross-Window Broadcast Listener
    try {
        if (window.BroadcastChannel) {
            var bcListen = new BroadcastChannel('mtq_timer_channel');
            bcListen.onmessage = function(e) {
                var ev = e.data;
                if (!ev) return;
                var cells = document.querySelectorAll('.timer-cell');
                cells.forEach(function(cell) {
                    var slug = getCellSlug(cell);
                    if (slug !== ev.slug) return;
                    var recordId = getCellRecordId(cell);
                    var row = cell.closest('tr');

                    if (ev.recordId && recordId && ev.recordId === recordId) {
                        cell.style.display = 'inline-flex';
                        if (row) row.classList.add('timer-active-row');

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
                    } else if (ev.recordId && recordId && ev.recordId !== recordId) {
                        cell.style.display = 'none';
                        cell.setAttribute('data-is-running', '0');
                        if (row) {
                            row.classList.remove('timer-active-row', 'timer-phase-green', 'timer-phase-yellow', 'timer-phase-red');
                            var toggleBtn = row.querySelector('.btn-toggle-timer');
                            if (toggleBtn) setBtnToPlay(toggleBtn);
                        }
                    }
                });
                if (ev.recordId) {
                    updateShowLiveButtons(ev.recordId);
                }
            };
        }
    } catch(e) {}

    function initTimerCells() {
        document.querySelectorAll('.timer-cell').forEach(function(cell) {
            var rid = getCellRecordId(cell);
            var slug = getCellSlug(cell);
            if (rid) cell.setAttribute('data-record-id', rid);
            if (slug) cell.setAttribute('data-slug', slug);
            if (!cell.getAttribute('data-format')) cell.setAttribute('data-format', 'ms');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTimerCells);
    } else {
        initTimerCells();
    }
    document.addEventListener('livewire:navigated', initTimerCells);
    document.addEventListener('livewire:initialized', function() {
        initTimerCells();
        if (window.Livewire && window.Livewire.hook) {
            window.Livewire.hook('morph.updated', function() {
                initTimerCells();
            });
        }
    });
})();
</script>