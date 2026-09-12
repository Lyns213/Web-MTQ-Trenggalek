<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiTigapuluhJuz;
use Illuminate\Http\Request;

class NilaiTigapuluhJuzController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiTigapuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_tigapuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_tigapuluh_juzs.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timertigapuluh_juz', [
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
            $currentRecord = NilaiTigapuluhJuz::with('peserta')
                ->join('pesertas', 'nilai_tigapuluh_juzs.peserta_id', '=', 'pesertas.id')
                ->where('nilai_tigapuluh_juzs.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_tigapuluh_juzs.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timertigapuluhjuz', [
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

        $nextRecord = NilaiTigapuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_tigapuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_tigapuluh_juzs.*')
            ->first();

        $previousRecord = NilaiTigapuluhJuz::with('peserta')
            ->join('pesertas', 'nilai_tigapuluh_juzs.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_tigapuluh_juzs.*')
            ->first();

        return view('filament.timertigapuluhjuz', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
