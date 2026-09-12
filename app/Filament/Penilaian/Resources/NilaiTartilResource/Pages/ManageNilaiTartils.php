<?php

namespace App\Filament\Penilaian\Resources\NilaiTartilResource\Pages;

use App\Filament\Penilaian\Resources\NilaiTartilResource;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiTartils extends ManageRecords
{
    protected static string $resource = NilaiTartilResource::class;

    protected ?string $subheading = 'Kelola penilaian peserta cabang Tartil. Input nilai dengan tepat karena hanya bisa dilakukan satu kali.';

    public function getHeadHtml(): string
    {
        return parent::getHeadHtml() . '<link rel="stylesheet" href="' . asset('css/penilaian.css') . '">';
    }
}
