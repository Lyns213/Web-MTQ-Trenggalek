<?php

namespace App\Filament\Resources\NilaiLimaJuzResource\Pages;

use App\Filament\Resources\NilaiLimaJuzResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiLimaJuzs extends ManageRecords
{
    protected static string $resource = NilaiLimaJuzResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
