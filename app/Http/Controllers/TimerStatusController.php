<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class TimerStatusController extends Controller
{
    public function index()
    {
        $slugs = array_keys(LiveScoreController::$config);
        $timers = [];

        foreach ($slugs as $slug) {
            $activeId = Cache::get('mtq_live_active_' . $slug);
            if ($activeId) {
                $cacheKey = 'mtq_timer_' . $slug . '_' . $activeId;
                $timerState = Cache::get($cacheKey);

                if (!$timerState) {
                    $cfg = LiveScoreController::$config[$slug] ?? LiveScoreController::$config['tartil'];
                    $defaultTimer = $cfg['timer'] ?? '00:05:00';
                    $parts = explode(':', $defaultTimer);
                    $totalSeconds = count($parts) === 3 ? ((int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2]) : (count($parts) === 2 ? ((int)$parts[0] * 60 + (int)$parts[1]) : 300);
                    $timerState = [
                        'total_seconds' => $totalSeconds,
                        'remaining_seconds' => $totalSeconds,
                        'is_running' => false,
                        'started_at' => null,
                    ];
                    Cache::put($cacheKey, $timerState, 86400);
                }

                if ($timerState) {
                    $remaining = (int)$timerState['remaining_seconds'];
                    if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                        $elapsed = time() - $timerState['started_at'];
                        $remaining = max(0, $remaining - $elapsed);
                        if ($remaining <= 0) {
                            $timerState['is_running'] = false;
                            $timerState['remaining_seconds'] = 0;
                            $timerState['started_at'] = null;
                            Cache::put($cacheKey, $timerState, 86400);
                        }
                    }
                    $timers[$slug] = [
                        'record_id' => (int)$activeId,
                        'remaining' => $remaining,
                        'total' => (int)$timerState['total_seconds'],
                        'is_running' => (bool)($timerState['is_running'] ?? false),
                    ];
                }
            } else {
                $timers[$slug] = null;
            }
        }

        return new \Illuminate\Http\JsonResponse([
            'timers' => $timers,
        ]);
    }
}
