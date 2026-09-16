<?php

namespace App\Filament\Penilaian\Concerns;

use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Cache;

class HasLiveScoreActions
{
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
                if (Cache::get('mtq_live_active_' . $slug) != $record->id) {
                    return '';
                }
                $cacheKey = 'mtq_timer_' . $slug . '_' . $record->id;
                $timerState = Cache::get($cacheKey);

                if (!$timerState) {
                    return '';
                }

                $remaining = $timerState['remaining_seconds'];
                if ($timerState['is_running'] && $timerState['started_at']) {
                    $elapsed = time() - $timerState['started_at'];
                    $remaining = max(0, $remaining - $elapsed);
                }

                $m = floor($remaining / 60);
                $s = $remaining % 60;
                return sprintf('%02d:%02d', $m, $s);
            })
            ->badge()
            ->color(function ($record) use ($slug) {
                if (Cache::get('mtq_live_active_' . $slug) != $record->id) return null;
                $cacheKey = 'mtq_timer_' . $slug . '_' . $record->id;
                $timerState = Cache::get($cacheKey);
                if (!$timerState) return 'gray';
                return $timerState['is_running'] ? 'success' : 'gray';
            })
            ->extraAttributes(function ($record) use ($slug) {
                if (Cache::get('mtq_live_active_' . $slug) != $record->id) {
                    return [];
                }
                $cacheKey = 'mtq_timer_' . $slug . '_' . $record->id;
                $timerState = Cache::get($cacheKey);
                if (!$timerState || !$timerState['is_running']) {
                    return [];
                }
                return ['class' => 'timer-badge-running'];
            });
    }

    public static function getLiveScoreTableActions(string $slug): array
    {
        return [
            Action::make('showLive')
                ->label('')
                ->tooltip('Tampilkan Peserta')
                ->icon('heroicon-o-tv')
                ->color('info')
                ->action(function ($record) use ($slug) {
                    Cache::put('mtq_live_active_' . $slug, $record->id, 86400);
                    Notification::make()
                        ->title('Peserta ditampilkan di live score')
                        ->success()
                        ->send();
                })
                ->visible(fn ($record) => Cache::get('mtq_live_active_' . $slug) != $record->id),

            Action::make('unshowLive')
                ->label('')
                ->tooltip('Sembunyikan Peserta')
                ->icon('heroicon-o-eye-slash')
                ->color('gray')
                ->action(function ($record) use ($slug) {
                    Cache::forget('mtq_live_active_' . $slug);
                    Notification::make()
                        ->title('Peserta disembunyikan dari live score')
                        ->success()
                        ->send();
                })
                ->visible(fn ($record) => Cache::get('mtq_live_active_' . $slug) == $record->id),

            Action::make('toggleTimer')
                ->label('')
                ->tooltip(fn ($record) => (Cache::get('mtq_timer_' . $slug . '_' . $record->id)['is_running'] ?? false) ? 'Jeda Waktu' : 'Mulai Waktu')
                ->icon(fn ($record) => (Cache::get('mtq_timer_' . $slug . '_' . $record->id)['is_running'] ?? false) ? 'heroicon-o-pause' : 'heroicon-o-play')
                ->color(fn ($record) => (Cache::get('mtq_timer_' . $slug . '_' . $record->id)['is_running'] ?? false) ? 'warning' : 'success')
                ->action(function ($record) use ($slug) {
                    $cacheKey = 'mtq_timer_' . $slug . '_' . $record->id;
                    $timerState = Cache::get($cacheKey);

                    if (!$timerState) {
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

                    if ($timerState['is_running']) {
                        if ($timerState['started_at']) {
                            $elapsed = time() - $timerState['started_at'];
                            $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
                        }
                        $timerState['is_running'] = false;
                        $timerState['started_at'] = null;
                        Notification::make()->title('Timer dijeda')->warning()->send();
                    } else {
                        if ($timerState['remaining_seconds'] > 0) {
                            $timerState['is_running'] = true;
                            $timerState['started_at'] = time();
                            Cache::put('mtq_live_active_' . $slug, $record->id, 86400);
                            Notification::make()->title('Timer dimulai')->success()->send();
                        }
                    }

                    Cache::put($cacheKey, $timerState, 86400);
                }),

            Action::make('resetTimer')
                ->label('')
                ->tooltip('Reset Waktu')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset Waktu')
                ->modalDescription('Apakah Anda yakin ingin mereset waktu?')
                ->modalSubmitActionLabel('Reset Waktu')
                ->action(function ($record) use ($slug) {
                    $cacheKey = 'mtq_timer_' . $slug . '_' . $record->id;
                    $timerState = Cache::get($cacheKey);

                    if ($timerState) {
                        $timerState['is_running'] = false;
                        $timerState['started_at'] = null;
                        $timerState['remaining_seconds'] = $timerState['total_seconds'];
                        Cache::put($cacheKey, $timerState, 86400);
                        Notification::make()->title('Timer direset')->danger()->send();
                    }
                }),
        ];
    }

    public static function getRecordClasses(string $slug): \Closure
    {
        return fn ($record) => (Cache::get('mtq_timer_' . $slug . '_' . $record->id)['is_running'] ?? false) ? 'timer-active-row' : '';
    }
}
