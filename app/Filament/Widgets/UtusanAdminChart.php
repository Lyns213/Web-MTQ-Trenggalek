<?php

namespace App\Filament\Widgets;

use App\Models\Utusan;
use Filament\Widgets\ChartWidget;

class UtusanAdminChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Sebaran Peserta tiap Kecamatan';

    protected function getData(): array
    {
        // Mengambil data jumlah peserta per utusan/kecamatan
        $selectedTahunId = session("selected_tahun_id");
        $data = Utusan::withCount(['peserta' => function ($query) use ($selectedTahunId) {
            if ($selectedTahunId) {
                $query->where('tahun_id', $selectedTahunId);
            }
        }])
            ->get()
            ->pluck('peserta_count', 'kecamatan')
            ->toArray();

        // Memisahkan label dan nilai
        $labels = array_keys($data);
        $values = array_values($data);

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Jumlah Pendaftar',
                    'data' => $values,
                    'backgroundColor' => [
                        '#06C', // Warna-warna berbeda untuk setiap kecamatan
                        '#4CB140',
                        '#009596',
                        '#5752D1',
                        '#F4C145',
                        '#EC7A08',
                        '#7D1007',
                        '#6A6E73',
                        '#8BC1F7',
                        '#BDE2B9',
                        '#A2D9D9',
                        '#B2B0EA',
                        '#F9E0A2',
                        '#F4B678',
                        // Tambahkan lebih banyak warna sesuai kebutuhan
                    ],
                    'hoverOffset' => 4, // Memberikan efek hover pada pie chart
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom', // Letakkan legenda di atas chart
                ],
                'tooltip' => [
                    'enabled' => true, // Tampilkan tooltip saat hover
                ],
            ],
            'scales' => [
                'x' => [
                    'display' => false, // Hilangkan sumbu x
                ],
                'y' => [
                    'display' => false, // Hilangkan sumbu y
                ],
            ],
        ];
    }
}
