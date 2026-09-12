<?php

namespace App\Filament\Resources\NilaiTigapuluhJuzResource\Pages;

use App\Filament\Resources\NilaiTigapuluhJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiTigapuluhJuzs extends ManageRecords
{
    protected static string $resource = NilaiTigapuluhJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
