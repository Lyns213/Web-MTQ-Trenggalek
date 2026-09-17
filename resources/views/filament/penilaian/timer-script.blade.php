<script>
(function() {
    var APP_BASE = '{{ url("/") }}';
    var audioStart = new Audio('{{ asset("sounds/mtqstart.mp3") }}');
    var audioMid = new Audio('{{ asset("sounds/mtqmid.mp3") }}');
    var audioEnd = new Audio('{{ asset("sounds/mtqend.mp3") }}');

    function unlockAudioSystem() {
        [audioStart, audioMid, audioEnd].forEach(function(a) {
            if (!a) return;
            try {
                var p = a.play();
                if (p && typeof p.then === 'function') {
                    p.then(function() {
                        a.pause();
                        a.currentTime = 0;
                    }).catch(function() {});
                }
            } catch(e) {}
        });
    }

    ['click', 'touchstart', 'keydown', 'mousedown'].forEach(function(evt) {
        document.addEventListener(evt, unlockAudioSystem, { once: true, passive: true });
    });

    function playAudio(audio) {
        if (!audio) return;
        try {
            audio.currentTime = 0;
            var p = audio.play();
            if (p && typeof p.catch === 'function') {
                console.warn('Audio play error:', e);
            }
        } catch(e) {
            console.warn(e);
        }
    }

    function playBeeps(count, type) {
        var audio = type === 'start' ? audioStart : (type === 'mid' ? audioMid : audioEnd);
        playAudio(audio);
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

        var isRunning = cell ? cell.getAttribute('data-is-running') === '1' : false;
        var rem = parseInt(cell ? cell.getAttribute('data-remaining') : '300', 10);
        var total = parseInt(cell ? cell.getAttribute('data-total-seconds') : '300', 10) || 300;
        if (isNaN(rem) || rem <= 0) rem = total;

        if (!isRunning) {
            if (cell) {
                cell.setAttribute('data-is-running', '1');
                cell.setAttribute('data-remaining', rem);
            }
            if (row) row.classList.add('timer-active-row');
            setBtnToPause(btn);
            playBeeps(1, 'start');
            showNotification('Timer dimulai', 'success');
            broadcastTimerSync('start', slug, recordId, rem, total);
            fetch(APP_BASE + '/live/' + slug + '/timer/start?id=' + recordId);
        } else {
            if (cell) {
                cell.setAttribute('data-is-running', '0');
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

        if (cell) {
            cell.setAttribute('data-is-running', '0');
            cell.setAttribute('data-remaining', total);
            var format = cell.getAttribute('data-format') || 'ms';
            updateCellText(cell, formatTime(total, format));
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
        var row = btn ? btn.closest('tr') : null;
        var isActive = btn.getAttribute('data-is-active') === '1';

        if (isActive) {
            setBtnToShow(btn);
            if (row) row.classList.remove('timer-active-row');
            showNotification('Peserta disembunyikan dari live score', 'warning');
            broadcastTimerSync('unshow_participant', slug, null, 0, 0);
            fetch(APP_BASE + '/live/' + slug + '/timer/unshow?id=' + recordId);
        } else {
            document.querySelectorAll('.btn-toggle-show-live').forEach(function(b) {
                if (b !== btn) setBtnToShow(b);
            });
            document.querySelectorAll('tr.timer-active-row').forEach(function(r) {
                if (r !== row) r.classList.remove('timer-active-row');
            });

            setBtnToUnshow(btn);
            if (row) row.classList.add('timer-active-row');
            showNotification('Peserta ditampilkan di live score', 'success');
            broadcastTimerSync('show_participant', slug, recordId, 0, 0);
            fetch(APP_BASE + '/live/' + slug + '/timer/show?id=' + recordId);
        }
    };

    // 5. FORM MODAL SUBMIT (SIMPAN NILAI)
    document.addEventListener('click', function(e) {
        var submitModalBtn = e.target.closest('.fi-modal-submit-action') || (e.target.closest('button[type="submit"]') && e.target.closest('.fi-modal'));
        if (submitModalBtn) {
            var activeSlug = (function() {
                var cell = document.querySelector('.timer-cell');
                if (cell) return cell.getAttribute('data-slug');
                return 'tartil';
            })();
            broadcastTimerSync('score_saved', activeSlug, null, 0, 0);
        }
    }, false);

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

                if (rem === 60 && !playedMidMap[recordId]) {
                    playedMidMap[recordId] = true;
                    playBeeps(2, 'mid');
                }

                if (rem === 0) {
                    cell.setAttribute('data-is-running', '0');
                    if (!playedEndMap[recordId]) {
                        playedEndMap[recordId] = true;
                        playBeeps(3, 'end');
                    }
                }
            } else {
                cell.setAttribute('data-is-running', '0');
                var format = cell.getAttribute('data-format') || 'ms';
                updateCellText(cell, formatTime(0, format));
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

                    if (tInfo && tInfo.record_id === recordId) {
                        cell.setAttribute('data-is-running', tInfo.is_running ? '1' : '0');
                        var localRem = parseInt(cell.getAttribute('data-remaining'), 10);
                        if (isNaN(localRem) || Math.abs(localRem - tInfo.remaining) > 2) {
                            cell.setAttribute('data-remaining', tInfo.remaining);
                            var format = cell.getAttribute('data-format') || 'ms';
                            updateCellText(cell, formatTime(tInfo.remaining, format));
                        }
                        var row = cell.closest('tr');
                        if (row) {
                            var toggleBtn = row.querySelector('.btn-toggle-timer');
                            if (toggleBtn) {
                                if (tInfo.is_running) setBtnToPause(toggleBtn);
                                else setBtnToPlay(toggleBtn);
                            }
                        }
                    }
                });
            })
            .catch(function() {});
    }, 1000);
})();
</script>