<?php

namespace App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource\Pages;

use App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiSepuluhJuzs extends ManageRecords
{
    protected static string $resource = NilaiSepuluhJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    protected ?string $subheading = 'Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali';
}
