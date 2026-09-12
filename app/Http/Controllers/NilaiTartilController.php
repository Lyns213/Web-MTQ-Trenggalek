<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiTartil;
use Illuminate\Http\Request;

class NilaiTartilController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiTartil::with('peserta')
            ->join('pesertas', 'nilai_tartils.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_tartils.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timertartil', [
                    'currentRecord' => null,
                    'nextRecord' => null,
                    'previousRecord' => null,
                    'records' => $records,
                    'timer' => '00:00:00',
                    'timerInSeconds' => 0,
                    'emptyState' => true,
                ]);
            }
        } else {
            $currentRecord = NilaiTartil::with('peserta')
                ->join('pesertas', 'nilai_tartils.peserta_id', '=', 'pesertas.id')
                ->where('nilai_tartils.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_tartils.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timertartil', [
                    'currentRecord' => null,
                    'nextRecord' => null,
                    'previousRecord' => null,
                    'records' => $records,
                    'timer' => '00:00:00',
                    'timerInSeconds' => 0,
                    'emptyState' => true,
                ]);
            }
            
            $currentNoPeserta = $currentRecord->peserta->no_peserta;

        $nextRecord = NilaiTartil::with('peserta')
            ->join('pesertas', 'nilai_tartils.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_tartils.*')
            ->first();

        $previousRecord = NilaiTartil::with('peserta')
            ->join('pesertas', 'nilai_tartils.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_tartils.*')
            ->first();

        $cabangId = $currentRecord->peserta->cabang_id ?? null;
        $cabang = Cabang::find($cabangId);
        $timer = $cabang ? $cabang->timer : '00:00:00';

        list($hours, $minutes, $seconds) = explode(':', $timer);
        $timerInSeconds = ($hours * 3600) + ($minutes * 60) + $seconds;

        return view('filament.timertartil', compact('currentRecord', 'nextRecord', 'previousRecord', 'records', 'timer', 'timerInSeconds'));
    }
}
