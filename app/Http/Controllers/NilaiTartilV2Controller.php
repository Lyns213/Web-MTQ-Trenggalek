<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\NilaiTartil;
use Illuminate\Http\Request;

class NilaiTartilV2Controller extends Controller
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
                return view('filament.penilaian.tartil-v2', [
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
                return view('filament.penilaian.tartil-v2', [
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

        return view('filament.penilaian.tartil-v2', compact('currentRecord', 'nextRecord', 'previousRecord', 'records', 'timer', 'timerInSeconds'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tajwid' => 'required|numeric|min:0|max:40',
            'irama_suara' => 'required|numeric|min:0|max:40',
            'fashahah' => 'required|numeric|min:0|max:20',
        ]);

        $nilai = NilaiTartil::findOrFail($id);
        $nilai->update([
            'tajwid' => $request->tajwid,
            'irama_suara' => $request->irama_suara,
            'fashahah' => $request->fashahah,
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil disimpan!');
    }
}
