<?php

namespace App\Http\Controllers;

use App\Models\NilaiTartil;
use Illuminate\Support\Facades\Cache;

class TimerStatusController extends Controller
{
    public function index()
    {
        $records = NilaiTartil::all();
        $runningIds = [];
        
        foreach ($records as $record) {
            $cacheKey = 'mtq_timer_tartil_' . $record->id;
            $timerState = Cache::get($cacheKey);
            
            if ($timerState && ($timerState['is_running'] ?? false)) {
                $runningIds[] = $record->id;
            }
        }
        
        return response()->json([
            'running_ids' => $runningIds,
        ]);
    }
}
