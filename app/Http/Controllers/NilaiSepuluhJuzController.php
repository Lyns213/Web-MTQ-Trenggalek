<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiSepuluhJuz;
use Illuminate\Http\Request;

class NilaiSepuluhJuzController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiSepuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_sepuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_sepuluh_juzs.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timersepuluh_juz', [
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
            $currentRecord = NilaiSepuluhJuz::with('peserta')
                ->join('pesertas', 'nilai_sepuluh_juzs.peserta_id', '=', 'pesertas.id')
                ->where('nilai_sepuluh_juzs.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_sepuluh_juzs.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timersepuluhjuz', [
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

        $nextRecord = NilaiSepuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_sepuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_sepuluh_juzs.*')
            ->first();

        $previousRecord = NilaiSepuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_sepuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_sepuluh_juzs.*')
            ->first();

        return view('filament.timersepuluhjuz', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
