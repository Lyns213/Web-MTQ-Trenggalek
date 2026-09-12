<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiTartil;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NilaiTartilLiveController extends Controller
{
    public function index($id = null)
    {
        $dataResponse = $this->getData($id);
        $initialData = $dataResponse->getData(true);

        return view('filament.penilaian.tartil-live', [
            'activeId' => $id,
            'initialData' => $initialData,
        ]);
    }

    private function getRecords()
    {
        $activeTahun = Tahun::where('is_active', true)->first();
        $tahunId = $activeTahun ? $activeTahun->id : null;

        $query = NilaiTartil::with(['peserta.utusan', 'peserta.cabang'])
            ->join('pesertas', 'nilai_tartils.peserta_id', '=', 'pesertas.id');

        if ($tahunId) {
            $query->where('pesertas.tahun_id', $tahunId);
        }

        return $query
            ->orderByRaw("CASE WHEN pesertas.no_peserta IS NOT NULL AND LENGTH(TRIM(pesertas.no_peserta)) > 0 THEN 0 ELSE 1 END, pesertas.no_peserta ASC, nilai_tartils.id ASC")
            ->select('nilai_tartils.*')
            ->get();
    }

    public function getData($id = null)
    {
        $records = $this->getRecords();

        $liveActiveId = Cache::get('mtq_live_active_tartil');
        if ($liveActiveId && !$id) {
            $id = $liveActiveId;
        }

        if (!$id) {
            $currentRecord = $records->first();
        } else {
            $currentRecord = $records->firstWhere('id', (int)$id);
            if (!$currentRecord) {
                $currentRecord = NilaiTartil::with(['peserta.utusan', 'peserta.cabang'])
                    ->where('id', $id)
                    ->first();
            }
            if (!$currentRecord) {
                $currentRecord = $records->first();
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
            return response()->json(['empty' => true, 'total' => $records->count()]);
        }

        $currentIndex = $records->search(function ($r) use ($currentRecord) {
            return $r->id === $currentRecord->id;
        });

        $nextRecord = ($currentIndex !== false && $currentIndex + 1 < $records->count()) ? $records->get($currentIndex + 1) : null;
        $previousRecord = ($currentIndex !== false && $currentIndex - 1 >= 0) ? $records->get($currentIndex - 1) : null;

        $cabangId = $currentRecord->peserta->cabang_id ?? null;
        $cabang = $currentRecord->peserta->cabang ?? ($cabangId ? Cabang::find($cabangId) : null);
        $totalTimer = $cabang && $cabang->timer ? $cabang->timer : '00:05:00';

        $parts = explode(':', $totalTimer);
        if (count($parts) === 3) {
            $totalSeconds = ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
        } elseif (count($parts) === 2) {
            $totalSeconds = ((int)$parts[0] * 60) + (int)$parts[1];
        } else {
            $totalSeconds = 300;
        }

        $cacheKey = 'mtq_timer_tartil_' . $currentRecord->id;
        $timerState = Cache::get($cacheKey);

        if (!$timerState) {
            $timerState = [
                'total_seconds' => $totalSeconds,
                'remaining_seconds' => $totalSeconds,
                'is_running' => false,
                'started_at' => null,
            ];
            Cache::put($cacheKey, $timerState, 3600);
        }

        if ($timerState['is_running'] && $timerState['started_at']) {
            $elapsed = time() - $timerState['started_at'];
            $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
            $timerState['started_at'] = time();
            Cache::put($cacheKey, $timerState, 3600);
        }

        $remaining = $timerState['remaining_seconds'];
        $m = floor($remaining / 60);
        $s = $remaining % 60;
        $timerFormatted = sprintf('%02d:%02d', $m, $s);

        $cabangNama = $cabang ? $cabang->nama_cabang : 'Tartil';
        $kecamatan = $currentRecord->peserta->utusan->kecamatan ?? $currentRecord->peserta->tempat_lahir ?? 'Trenggalek';

        $pasfotoUrl = null;
        if ($currentRecord->peserta->pasfoto) {
            $pasfotoUrl = asset('storage/' . $currentRecord->peserta->pasfoto);
        }

        return response()->json([
            'empty' => false,
            'current' => [
                'id' => $currentRecord->id,
                'nama' => $currentRecord->peserta->nama,
                'no_peserta' => $currentRecord->peserta->no_peserta ?? '-',
                'tempat_lahir' => $currentRecord->peserta->tempat_lahir,
                'kecamatan' => strtoupper($kecamatan),
                'cabang' => strtoupper($cabangNama),
                'pasfoto' => $pasfotoUrl,
                'tajwid' => (float)($currentRecord->tajwid ?? 0),
                'irama_dan_suara' => (float)($currentRecord->irama_dan_suara ?? 0),
                'fashahah' => (float)($currentRecord->fashahah ?? 0),
                'total' => (float)(($currentRecord->tajwid ?? 0) + ($currentRecord->irama_dan_suara ?? 0) + ($currentRecord->fashahah ?? 0)),
            ],
            'next' => $nextRecord ? ['id' => $nextRecord->id, 'nama' => $nextRecord->peserta->nama, 'no_peserta' => $nextRecord->peserta->no_peserta] : null,
            'previous' => $previousRecord ? ['id' => $previousRecord->id, 'nama' => $previousRecord->peserta->nama, 'no_peserta' => $previousRecord->peserta->no_peserta] : null,
            'timer' => [
                'formatted' => $timerFormatted,
                'remaining' => $remaining,
                'total' => $timerState['total_seconds'],
                'is_running' => $timerState['is_running'],
            ],
            'total_peserta' => $records->count(),
        ]);
    }

    public function controlTimer(Request $request, $action)
    {
        $id = $request->input('id');
        if (!$id) return response()->json(['error' => 'No ID'], 400);

        $cacheKey = 'mtq_timer_tartil_' . $id;
        $timerState = Cache::get($cacheKey);

        if (!$timerState) {
            return response()->json(['error' => 'Timer not initialized'], 404);
        }

        if ($action === 'start') {
            if ($timerState['remaining_seconds'] > 0) {
                $timerState['is_running'] = true;
                $timerState['started_at'] = time();
                Cache::put($cacheKey, $timerState, 3600);
            }
        } elseif ($action === 'pause') {
            if ($timerState['is_running'] && $timerState['started_at']) {
                $elapsed = time() - $timerState['started_at'];
                $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
            }
            $timerState['is_running'] = false;
            $timerState['started_at'] = null;
            Cache::put($cacheKey, $timerState, 3600);
        } elseif ($action === 'reset') {
            $timerState['is_running'] = false;
            $timerState['started_at'] = null;
            $timerState['remaining_seconds'] = $timerState['total_seconds'];
            Cache::put($cacheKey, $timerState, 3600);
        }

        return response()->json(['success' => true]);
    }
}
