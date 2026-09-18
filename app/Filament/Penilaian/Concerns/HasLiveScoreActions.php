<?php

namespace App\Filament\Penilaian\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Cache;

class HasLiveScoreActions
{
    protected static array $activeRecordCache = [];
    protected static array $timerStateCache = [];

    public static function getActiveRecordId(string $slug): mixed
    {
        if (!array_key_exists($slug, static::$activeRecordCache)) {
            static::$activeRecordCache[$slug] = Cache::get('mtq_live_active_' . $slug);
        }
        return static::$activeRecordCache[$slug];
    }

    public static function setActiveRecordId(string $slug, mixed $id): void
    {
        static::$activeRecordCache[$slug] = $id;
        if ($id === null) {
            Cache::forget('mtq_live_active_' . $slug);
        } else {
            Cache::put('mtq_live_active_' . $slug, $id, 86400);
        }
    }

    public static function getTimerState(string $slug, mixed $recordId, mixed $record = null): array
    {
        $cacheKey = 'mtq_timer_' . $slug . '_' . $recordId;
        if (!array_key_exists($cacheKey, static::$timerStateCache)) {
            $timerState = Cache::get($cacheKey);
            $cabang = $record?->peserta?->cabang ?? $record?->grup?->peserta?->first()?->cabang;
            $timer = $cabang ? $cabang->timer : (\App\Http\Controllers\LiveScoreController::$config[$slug]['timer'] ?? '00:05:00');
            $parts = explode(':', $timer);
            if (count($parts) === 3) {
                $totalSeconds = ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
            } elseif (count($parts) === 2) {
                $totalSeconds = ((int)$parts[0] * 60) + (int)$parts[1];
            } else {
                $totalSeconds = 300;
            }

            $isScored = ($record && floatval($record->total ?? 0) > 0);

            if (!$timerState) {
                $remainingSeconds = $isScored ? 0 : $totalSeconds;
                $timerState = [
                    'total_seconds' => $totalSeconds,
                    'remaining_seconds' => $remainingSeconds,
                    'is_running' => false,
                    'started_at' => null,
                ];
                Cache::put($cacheKey, $timerState, 86400);
            } elseif ($isScored && empty($timerState['is_running']) && empty($timerState['is_reset_ready'])) {
                $timerState['remaining_seconds'] = 0;
            }
            static::$timerStateCache[$cacheKey] = $timerState;
        }

        return static::$timerStateCache[$cacheKey] ?? [
            'total_seconds' => 300,
            'remaining_seconds' => 300,
            'is_running' => false,
            'started_at' => null,
        ];
    }

    public static function setTimerState(string $slug, mixed $recordId, array $timerState): void
    {
        $cacheKey = 'mtq_timer_' . $slug . '_' . $recordId;
        static::$timerStateCache[$cacheKey] = $timerState;
        Cache::put($cacheKey, $timerState, 86400);
    }

    public static function getLiveScoreHeaderAction(string $slug, string $label = 'Buka Live Score'): Action
    {
        return Action::make('liveScore')
            ->label($label)
            ->url('/live/' . $slug)
            ->icon('heroicon-o-tv')
            ->color('info')
            ->openUrlInNewTab();
    }

    public static function getTimerTableColumn(string $slug): TextColumn
    {
        return TextColumn::make('timer_live')
            ->label('Timer')
            ->html()
            ->alignCenter()
            ->extraAttributes(fn ($record) => [
                'data-slug' => $slug,
                'data-record-id' => $record->id,
            ])
            ->extraCellAttributes(fn ($record) => [
                'data-slug' => $slug,
                'data-record-id' => $record->id,
            ])
            ->getStateUsing(function ($record) use ($slug) {
                $isActive = (static::getActiveRecordId($slug) == $record->id);
                $timerState = static::getTimerState($slug, $record->id, $record);
                $total = (int)($timerState['total_seconds'] ?? 300);
                $isScored = ($record && floatval($record->total ?? 0) > 0);
                if ($isScored && empty($timerState['is_running']) && empty($timerState['is_reset_ready'])) {
                    $remaining = 0;
                } else {
                    $remaining = (int)($timerState['remaining_seconds'] ?? $total);
                }

                if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                    $elapsed = time() - $timerState['started_at'];
                    $remaining = max(0, $remaining - $elapsed);
                }

                $m = floor($remaining / 60);
                $s = $remaining % 60;
                $formatted = sprintf('%02d:%02d', $m, $s);
                $isRunning = (!empty($timerState['is_running'])) ? '1' : '0';
                $display = $isActive ? 'inline-flex' : 'none';
                $phase = ($remaining <= 0) ? 'timer-phase-red' : (($remaining <= 60) ? 'timer-phase-yellow' : 'timer-phase-green');

                return '<span class="timer-cell ' . $phase . '" '
                    . 'data-slug="' . htmlspecialchars($slug, ENT_QUOTES) . '" '
                    . 'data-record-id="' . (int)$record->id . '" '
                    . 'data-total-seconds="' . $total . '" '
                    . 'data-remaining="' . $remaining . '" '
                    . 'data-is-running="' . $isRunning . '" '
                    . 'data-format="ms" '
                    . 'style="display: ' . $display . ';">'
                    . $formatted
                    . '</span>';
            });
    }

    public static array $cabangFields = [
        'tartil' => [
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 40],
            ['key' => 'irama_dan_suara', 'label' => 'Irama dan suara', 'max' => 30],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 30],
        ],
        'anak' => [
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 30],
            ['key' => 'lagu', 'label' => 'Lagu', 'max' => 25],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 25],
            ['key' => 'suara', 'label' => 'Suara', 'max' => 20],
        ],
        'remaja' => [
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 30],
            ['key' => 'lagu', 'label' => 'Lagu', 'max' => 25],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 25],
            ['key' => 'suara', 'label' => 'Suara', 'max' => 20],
        ],
        'dewasa' => [
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 30],
            ['key' => 'lagu', 'label' => 'Lagu', 'max' => 25],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 25],
            ['key' => 'suara', 'label' => 'Suara', 'max' => 20],
        ],
        'satujuz' => [
            ['key' => 'til_tajwid', 'label' => 'Tilawah - Tajwid', 'max' => 30],
            ['key' => 'til_lagu', 'label' => 'Tilawah - Lagu', 'max' => 25],
            ['key' => 'til_suara', 'label' => 'Tilawah - Suara', 'max' => 15],
            ['key' => 'til_fashahah', 'label' => 'Tilawah - Fashahah', 'max' => 30],
            ['key' => 'tah_tahfizh', 'label' => 'Tahfizh - Tahfizh', 'max' => 50],
            ['key' => 'tah_tajwid', 'label' => 'Tahfizh - Tajwid', 'max' => 25],
            ['key' => 'tah_fashahah', 'label' => 'Tahfizh - Fashahah', 'max' => 25],
        ],
        'limajuz' => [
            ['key' => 'til_tajwid', 'label' => 'Tilawah - Tajwid', 'max' => 30],
            ['key' => 'til_lagu', 'label' => 'Tilawah - Lagu', 'max' => 25],
            ['key' => 'til_suara', 'label' => 'Tilawah - Suara', 'max' => 15],
            ['key' => 'til_fashahah', 'label' => 'Tilawah - Fashahah', 'max' => 30],
            ['key' => 'tah_tahfizh', 'label' => 'Tahfizh - Tahfizh', 'max' => 50],
            ['key' => 'tah_tajwid', 'label' => 'Tahfizh - Tajwid', 'max' => 25],
            ['key' => 'tah_fashahah', 'label' => 'Tahfizh - Fashahah', 'max' => 25],
        ],
        'sepuluhjuz' => [
            ['key' => 'tahfizh', 'label' => 'Tahfizh', 'max' => 50],
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 25],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 25],
        ],
        'duapuluhjuz' => [
            ['key' => 'tahfizh', 'label' => 'Tahfizh', 'max' => 50],
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 25],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 25],
        ],
        'tigapuluhjuz' => [
            ['key' => 'tahfizh', 'label' => 'Tahfizh', 'max' => 50],
            ['key' => 'tajwid', 'label' => 'Tajwid', 'max' => 25],
            ['key' => 'fashahah', 'label' => 'Fashahah', 'max' => 25],
        ],
        'mfq' => [
            ['key' => 'total', 'label' => 'Total Nilai', 'max' => 100],
        ],
        'msq' => [
            ['key' => 'terjemahan_dan_materi', 'label' => 'Terjemah & Materi', 'max' => 40],
            ['key' => 'penghayatan_dan_retorika', 'label' => 'Penghayatan & Retorika', 'max' => 30],
            ['key' => 'tilawah', 'label' => 'Tilawah', 'max' => 30],
        ],
        'mmq' => [
            ['key' => 'bobot_materi', 'label' => 'Bobot Materi', 'max' => 40],
            ['key' => 'kaidah_dan_gaya_bahasa', 'label' => 'Kaidah & Gaya Bahasa', 'max' => 25],
            ['key' => 'logika_dan_organisasi_pesan', 'label' => 'Logika & Organisasi', 'max' => 20],
            ['key' => 'presentasi', 'label' => 'Presentasi', 'max' => 15],
        ],
        'naskah' => [
            ['key' => 'kebenaran_kaidah_khat_wajib', 'label' => 'Kaidah Khat Wajib', 'max' => 35],
            ['key' => 'keindahan_khat_wajib', 'label' => 'Keindahan Khat Wajib', 'max' => 25],
            ['key' => 'kebenaran_kaidah_khat_pilihan', 'label' => 'Kaidah Khat Pilihan', 'max' => 25],
            ['key' => 'keindahan_khat_pilihan', 'label' => 'Keindahan Khat Pilihan', 'max' => 15],
        ],
        'mushaf' => [
            ['key' => 'kebenaran_kaidah_khat', 'label' => 'Kebenaran Kaidah Khat', 'max' => 45],
            ['key' => 'keindahan_khat', 'label' => 'Keindahan Khat', 'max' => 35],
            ['key' => 'keindahan_hiasan_dan_lukisan', 'label' => 'Keindahan Hiasan & Lukisan', 'max' => 20],
        ],
        'dekorasi' => [
            ['key' => 'kebenaran_kaidah_khath', 'label' => 'Kebenaran Kaidah Khat', 'max' => 45],
            ['key' => 'keindahan_khath', 'label' => 'Keindahan Khat', 'max' => 35],
            ['key' => 'keindahan_hiasan_dan_lukisan', 'label' => 'Keindahan Hiasan & Lukisan', 'max' => 20],
        ],
        'kontemporer' => [
            ['key' => 'unsur_kaligrafi', 'label' => 'Unsur Kaligrafi', 'max' => 40],
            ['key' => 'unsur_seni_rupa', 'label' => 'Unsur Seni Rupa', 'max' => 35],
            ['key' => 'sentuhan_akhir', 'label' => 'Sentuhan Akhir', 'max' => 25],
        ],
    ];

    public static function getInputNilaiTableAction(string $slug): Action
    {
        return Action::make('inputNilai')
            ->label('')
            ->tooltip(fn ($record) => ($record->total == 0 || $record->total == null) ? 'Input Nilai' : 'Lihat / Edit Nilai')
            ->icon(fn ($record) => ($record->total == 0 || $record->total == null) ? 'heroicon-o-plus' : 'heroicon-o-eye')
            ->color(fn ($record) => ($record->total == 0 || $record->total == null) ? 'success' : 'info')
            ->extraAttributes(fn ($record) => [
                'class' => 'btn-input-nilai',
                'data-record-id' => $record->id,
                'data-slug' => $slug,
            ])
            ->alpineClickHandler(function ($record) use ($slug) {
                $name = $record->peserta?->nama ?? $record->grup?->nama ?? '';
                $scores = [];
                $fields = static::$cabangFields[$slug] ?? [];
                foreach ($fields as $f) {
                    $scores[$f['key']] = floatval($record->{$f['key']} ?? 0);
                }
                $scores['total'] = floatval($record->total ?? 0);
                return "window.mtqOpenInputNilai('{$slug}', {$record->id}, " . json_encode($name) . ", " . json_encode($scores) . ")";
            });
    }

    public static function getCabangStatsHeader(string $modelClass): \Closure
    {
        return function () use ($modelClass) {
            $tahunId = session('selected_tahun_id', '0');
            $html = Cache::remember('stats_hdr_' . md5($modelClass . '_' . $tahunId), 120, function () use ($modelClass) {
                return view('filament.penilaian.components.cabang-stats-header', ['modelClass' => $modelClass])->render();
            });
            return new \Illuminate\Support\HtmlString($html);
        };
    }

    public static function getLiveScoreTableActions(string $slug): array
    {
        return [
            static::getInputNilaiTableAction($slug),

            Action::make('showLive')
                ->label('')
                ->tooltip('Tampilkan Peserta')
                ->icon('heroicon-o-tv')
                ->color('info')
                ->extraAttributes(fn ($record) => [
                    'class' => 'btn-show-live btn-toggle-show-live',
                    'data-record-id' => $record->id,
                    'data-slug' => $slug,
                ])
                ->alpineClickHandler(fn ($record) => "window.mtqShowLive('{$slug}', {$record->id}, \$el)"),

            Action::make('toggleTimer')
                ->label('')
                ->tooltip(fn ($record) => (!empty(static::getTimerState($slug, $record->id, $record)['is_running'])) ? 'Jeda Waktu' : 'Mulai Waktu')
                ->icon(fn ($record) => (!empty(static::getTimerState($slug, $record->id, $record)['is_running'])) ? 'heroicon-o-pause' : 'heroicon-o-play')
                ->color(fn ($record) => (!empty(static::getTimerState($slug, $record->id, $record)['is_running'])) ? 'warning' : 'success')
                ->extraAttributes(fn ($record) => [
                    'class' => 'btn-toggle-timer',
                    'data-action-timer' => (!empty(static::getTimerState($slug, $record->id, $record)['is_running'])) ? 'pause' : 'start',
                    'data-record-id' => $record->id,
                    'data-slug' => $slug,
                ])
                ->alpineClickHandler(fn ($record) => "window.mtqToggleTimer('{$slug}', {$record->id}, \$el)"),

            Action::make('resetTimer')
                ->label('')
                ->tooltip('Reset Waktu')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->extraAttributes(fn ($record) => [
                    'class' => 'btn-reset-timer',
                    'data-record-id' => $record->id,
                    'data-slug' => $slug,
                    'data-total-seconds' => static::getTimerState($slug, $record->id, $record)['total_seconds'] ?? 300,
                ])
                ->alpineClickHandler(fn ($record) => "window.mtqResetTimer('{$slug}', {$record->id}, \$el)"),
        ];
    }

    public static function getRecordClasses(string $slug): \Closure
    {
        return function ($record) use ($slug) {
            if (static::getActiveRecordId($slug) != $record->id) {
                return '';
            }
            $timerState = static::getTimerState($slug, $record->id, $record);
            $total = (int)($timerState['total_seconds'] ?? 300);
            $isScored = ($record && floatval($record->total ?? 0) > 0);
            if ($isScored && empty($timerState['is_running']) && empty($timerState['is_reset_ready'])) {
                $remaining = 0;
            } else {
                $remaining = (int)($timerState['remaining_seconds'] ?? $total);
            }
            if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                $elapsed = time() - $timerState['started_at'];
                $remaining = max(0, $remaining - $elapsed);
            }
            $phase = ($remaining <= 0) ? 'timer-phase-red' : (($remaining <= 60) ? 'timer-phase-yellow' : 'timer-phase-green');
            return 'timer-active-row ' . $phase;
        };
    }
}
