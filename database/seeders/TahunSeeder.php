<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tahun;

class TahunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tahun::create([
            'tahun' => '2024',
            'is_active' => true,
            'batas_awal' => '2024-09-09',
            'batas_akhir' => '2024-10-09',
        ]);

        // Tahun::create([
        //     'tahun' => '2026',
        //     'is_active' => false,
        //     'batas_awal' => '2026-09-09',
        //     'batas_akhir' => '2026-10-09',
        // ]);
    }
}
