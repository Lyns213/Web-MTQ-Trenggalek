<?php

namespace App\Filament\Penilaian\Resources\NilaiDewasaResource\Pages;

use App\Filament\Penilaian\Resources\NilaiDewasaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiDewasas extends ManageRecords
{
    protected static string $resource = NilaiDewasaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
