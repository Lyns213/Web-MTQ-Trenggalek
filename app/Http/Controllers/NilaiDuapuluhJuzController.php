<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiDuapuluhJuz;
use Illuminate\Http\Request;

class NilaiDuapuluhJuzController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiDuapuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_duapuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_duapuluh_juzs.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timerduapuluh_juz', [
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
            $currentRecord = NilaiDuapuluhJuz::with('peserta')
                ->join('pesertas', 'nilai_duapuluh_juzs.peserta_id', '=', 'pesertas.id')
                ->where('nilai_duapuluh_juzs.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_duapuluh_juzs.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timerduapuluhjuz', [
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

        $nextRecord = NilaiDuapuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_duapuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_duapuluh_juzs.*')
            ->first();

        $previousRecord = NilaiDuapuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_duapuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_duapuluh_juzs.*')
            ->first();

        return view('filament.timerduapuluhjuz', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
