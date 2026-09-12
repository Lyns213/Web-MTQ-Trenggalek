<?php

namespace App\Http\Controllers;

use App\Models\NilaiDekorasi;
use Illuminate\Http\Request;

class NilaiDekorasiController extends Controller
{
    public function index($id = null)
{
    // Ambil semua records dan pastikan ada data
    $records = NilaiDekorasi::orderBy('id')->get();
    $records = NilaiDekorasi::latest()->paginate(1);

    // Jika tidak ada ID yang diberikan, ambil record pertama
    if (!$id) {
        $currentRecord = $records->first();
    } else {
        $currentRecord = NilaiDekorasi::findOrFail($id);
    }

    // Ambil record berikutnya
    $nextRecord = NilaiDekorasi::where('id', '>', $currentRecord->id)
                            ->orderBy('id', 'asc')
                            ->first();

    // Ambil record sebelumnya
    $previousRecord = NilaiDekorasi::where('id', '<', $currentRecord->id)
                                ->orderBy('id', 'desc')
                                ->first();

    return view('filament.timerdekorasi', compact('currentRecord', 'nextRecord', 'previousRecord', 'records'));
}
}
