<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'admin@gmail.com',
        //     'password' => Hash::make('password'),
        // ]);

        User::create([
            'name' => 'Admin MTQ',
            'email' => 'mtq@admin.com',
            'password' => Hash::make('mtqtrenggalekkab'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Kecamatan Bendungan',
            'email' => 'bendungan@mtq.com',
            'password' => Hash::make('bendungan14'),
            'role' => 'user',
            'utusan_id' => 1,
        ]);

        User::create([
            'name' => 'Kecamatan Dongko',
            'email' => 'dongko@mtq.com',
            'password' => Hash::make('dongko13'),
            'role' => 'user',
            'utusan_id' => 2,
        ]);

        User::create([
            'name' => 'Kecamatan Durenan',
            'email' => 'durenan@mtq.com',
            'password' => Hash::make('durenan12'),
            'role' => 'user',
            'utusan_id' => 3,
        ]);

        User::create([
            'name' => 'Kecamatan Gandusari',
            'email' => 'gandusari@mtq.com',
            'password' => Hash::make('gandusari11'),
            'role' => 'user',
            'utusan_id' => 4,
        ]);

        User::create([
            'name' => 'Kecamatan Kampak',
            'email' => 'kampak@mtq.com',
            'password' => Hash::make('kampak10'),
            'role' => 'user',
            'utusan_id' => 5,
        ]);

        User::create([
            'name' => 'Kecamatan Karangan',
            'email' => 'karangan@mtq.com',
            'password' => Hash::make('karangan09'),
            'role' => 'user',
            'utusan_id' => 6,
        ]);

        User::create([
            'name' => 'Kecamatan Munjungan',
            'email' => 'munjungan@mtq.com',
            'password' => Hash::make('munjungan08'),
            'role' => 'user',
            'utusan_id' => 7,
        ]);

        User::create([
            'name' => 'Kecamatan Panggul',
            'email' => 'panggul@mtq.com',
            'password' => Hash::make('panggu07'),
            'role' => 'user',
            'utusan_id' => 8,
        ]);

        User::create([
            'name' => 'Kecamatan Pogalan',
            'email' => 'pogalan@mtq.com',
            'password' => Hash::make('pogalan06'),
            'role' => 'user',
            'utusan_id' => 9,
        ]);

        User::create([
            'name' => 'Kecamatan Pule',
            'email' => 'pule@mtq.com',
            'password' => Hash::make('pule05'),
            'role' => 'user',
            'utusan_id' => 10,
        ]);

        User::create([
            'name' => 'Kecamatan Suruh',
            'email' => 'suruh@mtq.com',
            'password' => Hash::make('suruh04'),
            'role' => 'user',
            'utusan_id' => 11,
        ]);

        User::create([
            'name' => 'Kecamatan Trenggalek',
            'email' => 'trenggalek@mtq.com',
            'password' => Hash::make('trenggalek03'),
            'role' => 'user',
            'utusan_id' => 12,
        ]);

        User::create([
            'name' => 'Kecamatan Tugu',
            'email' => 'tugu@mtq.com',
            'password' => Hash::make('tugu02'),
            'role' => 'user',
            'utusan_id' => 13,
        ]);

        User::create([
            'name' => 'Kecamatan Watulimo',
            'email' => 'watulimo@mtq.com',
            'password' => Hash::make('watulimo01'),
            'role' => 'user',
            'utusan_id' => 14,
        ]);

        User::create([
            'name' => 'Tartil',
            'email' =>'tartil@penilaian.com',
            'password' => Hash::make('tartil1'),
            'role' => 'user',
            'cabang_id_satu' => 1 ,
            'cabang_id_dua' => 2 ,
        ]);

        User::create([
            'name' => 'Tilawah Anak-anak',
            'email' => 'tilawahanak@penilaian.com',
            'password' => Hash::make('anak2'),
            'role' => 'user',
            'cabang_id_satu' => 3,
            'cabang_id_dua' => 4,
        ]);

        User::create([
            'name' => 'Tilawah Remaja',
            'email' => 'tilawahremaja@penilaian.com',
            'password' => Hash::make('remaja3'),
            'role' => 'user',
            'cabang_id_satu' => 5,
            'cabang_id_dua' => 6,
        ]);

        User::create([
            'name' => 'Tilawah Dewasa',
            'email' => 'tilawahdewasa@penilaian.com',
            'password' => Hash::make('dewasa4'),
            'role' => 'user',
            'cabang_id_satu' => 7,
            'cabang_id_dua' => 8,
        ]);

        User::create([
            'name' => 'MHQ 1 Juz dan Tilawah',
            'email' => 'mhq1juzdantilawah@penilaian.com',
            'password' => Hash::make('1juz5'),
            'role' => 'user',
            'cabang_id_satu' => 9,
            'cabang_id_dua' => 10,
        ]);

        User::create([
            'name' => 'MHQ 5 Juz dan Tilawah',
            'email' => 'mhq5juzdantilawah@penilaian.com',
            'password' => Hash::make('5juz6'),
            'role' => 'user',
            'cabang_id_satu' => 11,
            'cabang_id_dua' => 12,
        ]);

        User::create([
            'name' => 'MHQ 10 Juz',
            'email' => 'mhq10juz@penilaian.com',
            'password' => Hash::make('10juz7'),
            'role' => 'user',
            'cabang_id_satu' => 13,
            'cabang_id_dua' => 14,
        ]);

        User::create([
            'name' => 'MHQ 20 Juz',
            'email' => 'mhq20juz@penilaian.com',
            'password' => Hash::make('20juz8'),
            'role' => 'user',
            'cabang_id_satu' => 15,
            'cabang_id_dua' => 16,
        ]);

        User::create([
            'name' => 'MHQ 30 Juz',
            'email' => 'mhq30juz@penilaian.com',
            'password' => Hash::make('30juz9'),
            'role' => 'user',
            'cabang_id_satu' => 17,
            'cabang_id_dua' => 18,
        ]);

        User::create([
            'name' => 'MFQ',
            'email' => 'mfq@penilaian.com',
            'password' => Hash::make('mfq10'),
            'role' => 'user',
            'cabang_id_satu' => 19,
            'cabang_id_dua' => 20,
        ]);

        User::create([
            'name' => 'MSQ',
            'email' => 'msq@penilaian.com',
            'password' => Hash::make('msq11'),
            'role' => 'user',
            'cabang_id_satu' => 21,
            'cabang_id_dua' => 22,
        ]);

        User::create([
            'name' => 'MKQ Naskah',
            'email' => 'mkqnaskah@penilaian.com',
            'password' => Hash::make('naskah12'),
            'role' => 'user',
            'cabang_id_satu' => 23,
            'cabang_id_dua' => 24,
        ]);

        User::create([
            'name' => 'MKQ Hiasan Mushaf',
            'email' => 'mkqhiasan@penilaian.com',
            'password' => Hash::make('hiasan13'),
            'role' => 'user',
            'cabang_id_satu' => 25,
            'cabang_id_dua' => 26,
        ]);

        User::create([
            'name' => 'MKQ Dekorasi',
            'email' => 'mkqdekorasi@penilaian.com',
            'password' => Hash::make('dekorasi14'),
            'role' => 'user',
            'cabang_id_satu' => 27,
            'cabang_id_dua' => 28,
        ]);

        User::create([
            'name' => 'MKQ Kontemporer',
            'email' => 'mkqkontemporer@penilaian.com',
            'password' => Hash::make('kontemporer15'),
            'role' => 'user',
            'cabang_id_satu' => 29,
            'cabang_id_dua' => 30,
        ]);

        User::create([
            'name' => 'KTIQ',
            'email' => 'mmq@penilaian.com',
            'password' => Hash::make('mmq16'),
            'role' => 'user',
            'cabang_id_satu' => 31,
            'cabang_id_dua' => 32,
        ]);
    }
}
