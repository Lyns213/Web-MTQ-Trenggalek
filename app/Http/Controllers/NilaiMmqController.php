<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiMmq;
use Illuminate\Http\Request;

class NilaiMmqController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiMmq::with('peserta')
            ->join('pesertas', 'nilai_mmqs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_mmqs.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timermmq', [
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
            $currentRecord = NilaiMmq::with('peserta')
                ->join('pesertas', 'nilai_mmqs.peserta_id', '=', 'pesertas.id')
                ->where('nilai_mmqs.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_mmqs.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timermmq', [
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

        $nextRecord = NilaiMmq::with('peserta')
            ->join('pesertas', 'nilai_mmqs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_mmqs.*')
            ->first();

        $previousRecord = NilaiMmq::with('peserta')
            ->join('pesertas', 'nilai_mmqs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_mmqs.*')
            ->first();

        return view('filament.timermmq', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
