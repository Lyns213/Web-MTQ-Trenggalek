<?php

namespace App\Filament\Kecamatan\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Peserta;

class StatsKecamatanOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $selectedTahunId = session("selected_tahun_id");
        
        // Mendapatkan user yang login saat ini
        $user = Auth::user();

        // Menghitung jumlah peserta yang dibuat oleh user yang login saat ini
        $jumlahPeserta = Peserta::where('utusan_id', $user->utusan_id)->where('tahun_id', $selectedTahunId)->count();

        // Menghitung pemenuhan kuota peserta
        $kuotaMaksimal = 40; // Kuota maksimal per kecamatan
        $persentaseKuota = ($jumlahPeserta / $kuotaMaksimal) * 100;

        // Menghitung sebaran gender peserta
        $pesertaLakiLaki = Peserta::where('utusan_id', $user->utusan_id)->where('jenis_kelamin', 'putra')->where('tahun_id', $selectedTahunId)->count();
        $pesertaPerempuan = Peserta::where('utusan_id', $user->utusan_id)->where('jenis_kelamin', 'putri')->where('tahun_id', $selectedTahunId)->count();
        $sebaranGender = $pesertaLakiLaki . ' : ' . $pesertaPerempuan;

        return [
            Stat::make('Jumlah Peserta', $jumlahPeserta)
                ->description('Pendaftar dari '. Auth::user()->name)
                ->descriptionIcon('heroicon-c-map-pin')
                ->color('warning'),

            Stat::make('Pemenuhan Kuota Peserta', round($persentaseKuota, 2) . '%')
                ->description('Dari kuota maksimal 40 Peserta')
                ->color('success')
                ->descriptionIcon('heroicon-s-percent-badge'),

            Stat::make('Sebaran Gender Peserta', $sebaranGender)
                ->description('Laki-laki : Perempuan')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('info'),
        ];
    }
}
