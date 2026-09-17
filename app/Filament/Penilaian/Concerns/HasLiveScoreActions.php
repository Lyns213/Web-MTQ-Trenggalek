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
        $activeId = static::getActiveRecordId($slug);
        if ($activeId != $recordId) {
            return [
                'total_seconds' => 300,
                'remaining_seconds' => 300,
                'is_running' => false,
                'started_at' => null,
            ];
        }

        $cacheKey = 'mtq_timer_' . $slug . '_' . $recordId;
        if (!array_key_exists($cacheKey, static::$timerStateCache)) {
            $timerState = Cache::get($cacheKey);
            if (!$timerState && $record) {
                $cabang = $record->peserta?->cabang ?? $record->grup?->peserta?->first()?->cabang;
                $timer = $cabang ? $cabang->timer : '00:05:00';
                $parts = explode(':', $timer);
                if (count($parts) === 3) {
                    $totalSeconds = ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
                } elseif (count($parts) === 2) {
                    $totalSeconds = ((int)$parts[0] * 60) + (int)$parts[1];
                } else {
                    $totalSeconds = 300;
                }
                $timerState = [
                    'total_seconds' => $totalSeconds,
                    'remaining_seconds' => $totalSeconds,
                    'is_running' => false,
                    'started_at' => null,
                ];
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
            ->getStateUsing(function ($record) use ($slug) {
                if (static::getActiveRecordId($slug) != $record->id) {
                    return '';
                }
                $timerState = static::getTimerState($slug, $record->id, $record);

                $remaining = $timerState['remaining_seconds'] ?? 300;
                if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                    $elapsed = time() - $timerState['started_at'];
                    $remaining = max(0, $remaining - $elapsed);
                }

                $m = floor($remaining / 60);
                $s = $remaining % 60;
                return sprintf('%02d:%02d', $m, $s);
            })
            ->badge()
            ->color(function ($record) use ($slug) {
                if (static::getActiveRecordId($slug) != $record->id) return null;
                $timerState = static::getTimerState($slug, $record->id, $record);
                return (!empty($timerState['is_running'])) ? 'success' : 'gray';
            })
            ->extraAttributes(function ($record) use ($slug) {
                if (static::getActiveRecordId($slug) != $record->id) {
                    return [];
                }
                $timerState = static::getTimerState($slug, $record->id, $record);
                $remaining = $timerState['remaining_seconds'] ?? 300;
                if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                    $elapsed = time() - $timerState['started_at'];
                    $remaining = max(0, $remaining - $elapsed);
                }
                return [
                    'data-remaining' => $remaining,
                    'data-total-seconds' => $timerState['total_seconds'] ?? 300,
                    'data-record-id' => $record->id,
                    'data-is-running' => (!empty($timerState['is_running'])) ? 1 : 0,
                    'data-format' => 'ms',
                    'data-slug' => $slug,
                    'class' => 'timer-cell timer-badge-running',
                ];
            });
    }

    public static function getLiveScoreTableActions(string $slug): array
    {
        return [
            Action::make('toggleShowLive')
                ->label('')
                ->tooltip(fn ($record) => (static::getActiveRecordId($slug) == $record->id) ? 'Sembunyikan Peserta' : 'Tampilkan Peserta')
                ->icon(fn ($record) => (static::getActiveRecordId($slug) == $record->id) ? 'heroicon-o-eye-slash' : 'heroicon-o-tv')
                ->color(fn ($record) => (static::getActiveRecordId($slug) == $record->id) ? 'gray' : 'info')
                ->extraAttributes(fn ($record) => [
                    'class' => 'btn-toggle-show-live',
                    'data-is-active' => (static::getActiveRecordId($slug) == $record->id) ? '1' : '0',
                    'data-record-id' => $record->id,
                    'data-slug' => $slug,
                ])
                ->alpineClickHandler(fn ($record) => "window.mtqToggleShowLive('{$slug}', {$record->id}, \$el)"),

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
        return fn ($record) => (!empty(static::getTimerState($slug, $record->id, $record)['is_running'])) ? 'timer-active-row' : '';
    }
}
