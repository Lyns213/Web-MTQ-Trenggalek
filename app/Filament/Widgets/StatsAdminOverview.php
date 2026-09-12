<?php

namespace App\Filament\Widgets;

use App\Models\Peserta; // Pastikan Anda mengimpor model Peserta
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $selectedTahunId = session("selected_tahun_id");
        
        // Menghitung jumlah peserta yang sudah diverifikasi
        $verifiedCount = Peserta::where('is_verified', true)->where('tahun_id', $selectedTahunId)->count();

        // Menghitung total peserta
        $totalCount = Peserta::where('tahun_id', $selectedTahunId)->count();

        // Menghitung persentase peserta yang sudah diverifikasi
        $verificationPercentage = $totalCount > 0 ? ($verifiedCount / $totalCount) * 100 : 0;

        // Menghitung persentase kuota terpenuhi berdasarkan 560 peserta
        $quotaPercentage = ($totalCount / 560) * 100;

        // Menghitung jumlah peserta laki-laki dan perempuan
        $maleCount = Peserta::where('jenis_kelamin', 'putra')->where('tahun_id', $selectedTahunId)->count();
        $femaleCount = Peserta::where('jenis_kelamin', 'putri')->where('tahun_id', $selectedTahunId)->count();

        // Menghitung rasio jenis kelamin
        $genderRatio = $maleCount > 0 ? round($maleCount / max($femaleCount, 1), 2) : 0;

        return [
            Stat::make('Peserta Sudah Diverifikasi', "{$verifiedCount} / {$totalCount}")
                ->description(number_format($verificationPercentage, 2) . '%')
                ->descriptionIcon('heroicon-s-check-circle')
                ->color('success'),

            Stat::make('Kuota Terpenuhi', number_format($quotaPercentage, 2) . '%')
                ->description("Dari Kuota 560 Peserta")
                ->descriptionIcon('heroicon-s-chart-bar')
                ->color('primary'),

            Stat::make('Sebaran Jenis Kelamin', "{$maleCount} : {$femaleCount} ")
                ->description('Laki-laki : Perempuan')
                ->descriptionIcon('heroicon-s-user-group')
                ->color('info'),
        ];
    }
}
