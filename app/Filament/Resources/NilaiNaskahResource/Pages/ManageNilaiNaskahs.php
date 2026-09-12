<?php

namespace App\Filament\Resources\NilaiNaskahResource\Pages;

use App\Filament\Resources\NilaiNaskahResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiNaskahs extends ManageRecords
{
    protected static string $resource = NilaiNaskahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
