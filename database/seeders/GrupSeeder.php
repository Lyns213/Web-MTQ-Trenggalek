<?php

namespace Database\Seeders;

use App\Models\Grup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GrupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Grup::create([
            'nama' => 'Grup Bendungan PA',
            'tahun_id' => 1,
            'utusan_id' => 1,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Bendungan PI',
            'tahun_id' => 1,
            'utusan_id' => 1,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Dongko PA',
            'tahun_id' => 1,
            'utusan_id' => 2,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Dongko PI',
            'tahun_id' => 1,
            'utusan_id' => 2,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Durenan PA',
            'tahun_id' => 1,
            'utusan_id' => 3,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Durenan PI',
            'tahun_id' => 1,
            'utusan_id' => 3,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Gandusari PA',
            'tahun_id' => 1,
            'utusan_id' => 4,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Gandusari PI',
            'tahun_id' => 1,
            'utusan_id' => 4,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Kampak PA',
            'tahun_id' => 1,
            'utusan_id' => 5,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Kampak PI',
            'tahun_id' => 1,
            'utusan_id' => 5,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Karangan PA',
            'tahun_id' => 1,
            'utusan_id' => 6,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Karangan PI',
            'tahun_id' => 1,
            'utusan_id' => 6,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Munjungan PA',
            'tahun_id' => 1,
            'utusan_id' => 7,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Munjungan PI',
            'tahun_id' => 1,
            'utusan_id' => 7,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Panggul PA',
            'tahun_id' => 1,
            'utusan_id' => 8,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Panggul PI',
            'tahun_id' => 1,
            'utusan_id' => 8,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Pogalan PA',
            'tahun_id' => 1,
            'utusan_id' => 9,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Pogalan PI',
            'tahun_id' => 1,
            'utusan_id' => 9,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Pule PA',
            'tahun_id' => 1,
            'utusan_id' => 10,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Pule PI',
            'tahun_id' => 1,
            'utusan_id' => 10,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Suruh PA',
            'tahun_id' => 1,
            'utusan_id' => 11,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Suruh PI',
            'tahun_id' => 1,
            'utusan_id' => 11,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Trenggalek PA',
            'tahun_id' => 1,
            'utusan_id' => 12,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Trenggalek PI',
            'tahun_id' => 1,
            'utusan_id' => 12,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Tugu PA',
            'tahun_id' => 1,
            'utusan_id' => 13,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Tugu PI',
            'tahun_id' => 1,
            'utusan_id' => 13,
            'jenis_kelamin' => 'putri',
        ]);

        Grup::create([
            'nama' => 'Grup Watulimo PA',
            'tahun_id' => 1,
            'utusan_id' => 14,
            'jenis_kelamin' => 'putra',
        ]);

        Grup::create([
            'nama' => 'Grup Watulimo PI',
            'tahun_id' => 1,
            'utusan_id' => 14,
            'jenis_kelamin' => 'putri',
        ]);
    }
}
