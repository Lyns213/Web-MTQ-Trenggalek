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

                if ($timerState) {
                    $remaining = (int)$timerState['remaining_seconds'];
                    if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                        $elapsed = time() - $timerState['started_at'];
                        $remaining = max(0, $remaining - $elapsed);
                    }
                    $timers[$slug] = [
                        'record_id' => (int)$activeId,
                        'remaining' => $remaining,
                        'total' => (int)$timerState['total_seconds'],
                        'is_running' => (bool)($timerState['is_running'] ?? false),
                    ];
                }
            }
        }

        return response()->json([
            'timers' => $timers,
        ]);
    }
}
