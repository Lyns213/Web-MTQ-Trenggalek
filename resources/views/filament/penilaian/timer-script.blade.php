<script>
(function() {
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
                p.catch(function(e) {
                    console.warn('Audio play error:', e);
                });
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
        var target = el.querySelector('.fi-ta-text-item-label') || el.querySelector('span') || el;
        if (target) target.textContent = text;
    }

    function broadcastTimerSync(action, slug, recordId, remaining, total) {
        var payload = {
            slug: slug || 'tartil',
            action: action,
            recordId: parseInt(recordId, 10),
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

    var playedMidMap = {};
    var playedEndMap = {};

    // 1. Play sound and broadcast sync on click without breaking Livewire actions/notifications
    document.addEventListener('click', function(e) {
        var toggleBtn = e.target.closest('.btn-toggle-timer');
        if (toggleBtn) {
            var row = toggleBtn.closest('tr');
            var cell = row ? row.querySelector('.timer-cell') : null;
            var recordId = toggleBtn.getAttribute('data-record-id') || (cell ? cell.getAttribute('data-record-id') : null);
            var slug = toggleBtn.getAttribute('data-slug') || (cell ? cell.getAttribute('data-slug') : 'tartil');

            if (cell) {
                var isRunning = cell.getAttribute('data-is-running') === '1';
                var rem = parseInt(cell.getAttribute('data-remaining'), 10);
                var total = parseInt(cell.getAttribute('data-total-seconds'), 10) || 300;

                if (!isRunning) {
                    playBeeps(1, 'start');
                    broadcastTimerSync('start', slug, recordId, rem, total);
                    cell.setAttribute('data-is-running', '1');
                    fetch(getAppBasePath() + '/live/' + slug + '/timer/start?id=' + recordId);
                } else {
                    broadcastTimerSync('pause', slug, recordId, rem, total);
                    cell.setAttribute('data-is-running', '0');
                    fetch(getAppBasePath() + '/live/' + slug + '/timer/pause?id=' + recordId);
                }
            }
        }
    }, false);

    // 2. Real-time Countdown: Ticks down every 1000ms
    setInterval(function() {
        var cells = document.querySelectorAll('.timer-cell[data-is-running="1"]');
        cells.forEach(function(cell) {
            var rem = parseInt(cell.getAttribute('data-remaining'), 10);
            var recordId = cell.getAttribute('data-record-id');
            if (isNaN(rem)) return;

            if (rem > 0) {
                rem = rem - 1;
                cell.setAttribute('data-remaining', rem);

                var format = cell.getAttribute('data-format') || 'hms';
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
                var format = cell.getAttribute('data-format') || 'hms';
                updateCellText(cell, formatTime(0, format));
            }
        });
    }, 1000);

    function getAppBasePath() {
        var match = window.location.pathname.match(/^(.*?)\/penilaian/i);
        return (match && match[1]) ? match[1] : '';
    }

    // 3. Background Sync: Poll timer status from server every 1000ms
    setInterval(function() {
        fetch(getAppBasePath() + '/mtq-timer-status')
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
                            var format = cell.getAttribute('data-format') || 'hms';
                            updateCellText(cell, formatTime(tInfo.remaining, format));
                        }
                    }
                });
            })
            .catch(function() {});
    }, 1000);
})();
</script>
