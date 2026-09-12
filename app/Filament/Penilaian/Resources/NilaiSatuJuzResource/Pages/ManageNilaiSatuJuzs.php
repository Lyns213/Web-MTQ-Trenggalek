<?php

namespace App\Filament\Penilaian\Resources\NilaiSatuJuzResource\Pages;

use App\Filament\Penilaian\Resources\NilaiSatuJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiSatuJuzs extends ManageRecords
{
    protected static string $resource = NilaiSatuJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
