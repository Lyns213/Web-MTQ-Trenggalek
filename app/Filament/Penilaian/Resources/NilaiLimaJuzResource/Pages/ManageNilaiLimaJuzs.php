<?php

namespace App\Filament\Penilaian\Resources\NilaiLimaJuzResource\Pages;

use App\Filament\Penilaian\Resources\NilaiLimaJuzResource;
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
