<?php

namespace App\Filament\Resources\NilaiSepuluhJuzResource\Pages;

use App\Filament\Resources\NilaiSepuluhJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiSepuluhJuzs extends ManageRecords
{
    protected static string $resource = NilaiSepuluhJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
