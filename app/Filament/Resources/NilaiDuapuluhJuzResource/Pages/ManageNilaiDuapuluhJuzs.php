<?php

namespace App\Filament\Resources\NilaiDuapuluhJuzResource\Pages;

use App\Filament\Resources\NilaiDuapuluhJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiDuapuluhJuzs extends ManageRecords
{
    protected static string $resource = NilaiDuapuluhJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
