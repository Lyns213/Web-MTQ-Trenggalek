<?php

namespace App\Filament\Penilaian\Resources\NilaiTigapuluhJuzResource\Pages;

use App\Filament\Penilaian\Resources\NilaiTigapuluhJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiTigapuluhJuzs extends ManageRecords
{
    protected static string $resource = NilaiTigapuluhJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
    protected ?string $subheading = 'Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali';
}
