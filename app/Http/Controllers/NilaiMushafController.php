<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiMushaf;
use Illuminate\Http\Request;

class NilaiMushafController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiMushaf::with('peserta')
            ->join('pesertas', 'nilai_mushafs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_mushafs.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timermushaf', [
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
            $currentRecord = NilaiMushaf::with('peserta')
                ->join('pesertas', 'nilai_mushafs.peserta_id', '=', 'pesertas.id')
                ->where('nilai_mushafs.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_mushafs.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timermushaf', [
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

        $nextRecord = NilaiMushaf::with('peserta')
            ->join('pesertas', 'nilai_mushafs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_mushafs.*')
            ->first();

        $previousRecord = NilaiMushaf::with('peserta')
            ->join('pesertas', 'nilai_mushafs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_mushafs.*')
            ->first();

        return view('filament.timermushaf', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
