<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiKontemporer;
use Illuminate\Http\Request;

class NilaiKontemporerController extends Controller
{
    public function index($id = null)
    {
        $tahunId = session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id);

        $records = NilaiKontemporer::with('peserta')
            ->join('pesertas', 'nilai_kontemporers.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->orderBy('pesertas.no_peserta')
            ->select('nilai_kontemporers.*')
            ->get();

        if (!$id) {
            $currentRecord = $records->first();
            
            if (!$currentRecord) {
                return view('filament.timerkontemporer', [
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
            $currentRecord = NilaiKontemporer::with('peserta')
                ->join('pesertas', 'nilai_kontemporers.peserta_id', '=', 'pesertas.id')
                ->where('nilai_kontemporers.id', $id)
                ->where('pesertas.tahun_id', $tahunId)
                ->select('nilai_kontemporers.*')
                ->first();

            if (!$currentRecord) {
                abort(404, 'Record not found');
            }
        }

        if (!$currentRecord || !$currentRecord->peserta) {
                return view('filament.timerkontemporer', [
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

        $nextRecord = NilaiKontemporer::with('peserta')
            ->join('pesertas', 'nilai_kontemporers.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '>', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'asc')
            ->select('nilai_kontemporers.*')
            ->first();

        $previousRecord = NilaiKontemporer::with('peserta')
            ->join('pesertas', 'nilai_kontemporers.peserta_id', '=', 'pesertas.id')
            ->where('pesertas.tahun_id', $tahunId)
            ->where('pesertas.no_peserta', '<', $currentNoPeserta)
            ->orderBy('pesertas.no_peserta', 'desc')
            ->select('nilai_kontemporers.*')
            ->first();

        return view('filament.timerkontemporer', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
    }
}
