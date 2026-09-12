<?php

namespace App\Filament\Resources\NilaiDekorasiResource\Pages;

use App\Filament\Resources\NilaiDekorasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiDekorasis extends ManageRecords
{
    protected static string $resource = NilaiDekorasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
