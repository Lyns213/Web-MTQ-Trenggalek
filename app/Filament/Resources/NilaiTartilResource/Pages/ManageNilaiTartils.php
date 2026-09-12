<?php

namespace App\Filament\Resources\NilaiTartilResource\Pages;

use App\Filament\Resources\NilaiTartilResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiTartils extends ManageRecords
{
    protected static string $resource = NilaiTartilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
