<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Utusan;
use Illuminate\Support\Facades\DB;

class UtusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecamatans = [
            'Bendungan',
            'Dongko',
            'Durenan',
            'Gandusari',
            'Kampak',
            'Karangan',
            'Munjungan',
            'Panggul',
            'Pogalan',
            'Pule',
            'Suruh',
            'Trenggalek',
            'Tugu',
            'Watulimo',
        ];

        foreach ($kecamatans as $kecamatan) {
            Utusan::create([
                'kecamatan' => $kecamatan,
            ]);
        }
    }
}
