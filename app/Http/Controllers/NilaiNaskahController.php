<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiNaskah;
use Illuminate\Http\Request;

class NilaiNaskahController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiNaskah::with('peserta')
            ->join('pesertas', 'nilai_naskahs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_naskahs.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timernaskah', [
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
            $currentRecord = NilaiNaskah::with('peserta')
                ->join('pesertas', 'nilai_naskahs.peserta_id', '=', 'pesertas.id')
                ->where('nilai_naskahs.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_naskahs.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timernaskah', [
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

        $nextRecord = NilaiNaskah::with('peserta')
            ->join('pesertas', 'nilai_naskahs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_naskahs.*')
            ->first();

        $previousRecord = NilaiNaskah::with('peserta')
            ->join('pesertas', 'nilai_naskahs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_naskahs.*')
            ->first();

        return view('filament.timernaskah', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
