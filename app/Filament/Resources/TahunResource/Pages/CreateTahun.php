<?php

namespace App\Filament\Resources\TahunResource\Pages;

use App\Models\Grup;
use App\Models\Peserta;
use Filament\Actions;
use App\Filament\Resources\TahunResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTahun extends CreateRecord
{
    protected static string $resource = TahunResource::class;

    protected static ?string $title = 'Tambah Tahun Baru';

    protected function afterCreate(): void
    {
        $tahunId = $this->record->id;

        Peserta::whereYear('created_at', $this->record->tahun)
            ->update(['tahun_id' => $tahunId]);

        $groupData = [
            ['nama' => 'Grup Bendungan', 'utusan_id' => 1],
            ['nama' => 'Grup Dongko', 'utusan_id' => 2],
            ['nama' => 'Grup Durenan', 'utusan_id' => 3],
            ['nama' => 'Grup Gandusari', 'utusan_id' => 4],
            ['nama' => 'Grup Kampak', 'utusan_id' => 5],
            ['nama' => 'Grup Karangan', 'utusan_id' => 6],
            ['nama' => 'Grup Munjungan', 'utusan_id' => 7],
            ['nama' => 'Grup Panggul', 'utusan_id' => 8],
            ['nama' => 'Grup Pogalan', 'utusan_id' => 9],
            ['nama' => 'Grup Pule', 'utusan_id' => 10],
            ['nama' => 'Grup Suruh', 'utusan_id' => 11],
            ['nama' => 'Grup Trenggalek', 'utusan_id' => 12],
            ['nama' => 'Grup Tugu', 'utusan_id' => 13],
            ['nama' => 'Grup Watulimo', 'utusan_id' => 14],
        ];

        // Loop untuk membuat grup dengan jenis kelamin
        foreach ($groupData as $group) {
            Grup::create([
                'nama' => $group['nama'] . ' PA', // Grup untuk putra
                'tahun_id' => $tahunId,
                'utusan_id' => $group['utusan_id'],
                'jenis_kelamin' => 'putra',
            ]);

            Grup::create([
                'nama' => $group['nama'] . ' PI', // Grup untuk putri
                'tahun_id' => $tahunId,
                'utusan_id' => $group['utusan_id'],
                'jenis_kelamin' => 'putri',
            ]);
        }

        // Ambil semua grup yang baru saja dibuat
        $grups = Grup::where('tahun_id', $this->record->id)->get();

        // Loop melalui setiap grup untuk memperbarui peserta yang sesuai
        foreach ($grups as $grup) {
            Peserta::where('utusan_id', $grup->utusan_id)
                ->where('tahun_id', $grup->tahun_id)
                ->where('jenis_kelamin', $grup->jenis_kelamin)
                ->where(function ($query) {
                    $query->where('cabang_id', 19)
                        ->orWhere('cabang_id', 20);
                })
                ->update(['grup_id' => $grup->id]);
        }
    }
}
