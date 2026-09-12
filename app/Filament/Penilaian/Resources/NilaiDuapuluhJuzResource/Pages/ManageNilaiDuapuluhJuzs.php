<?php

namespace App\Filament\Penilaian\Resources\NilaiDuapuluhJuzResource\Pages;

use App\Filament\Penilaian\Resources\NilaiDuapuluhJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiDuapuluhJuzs extends ManageRecords
{
    protected static string $resource = NilaiDuapuluhJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }

    protected ?string $subheading = 'Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali';
}
