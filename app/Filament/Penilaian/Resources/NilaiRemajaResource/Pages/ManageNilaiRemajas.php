<?php

namespace App\Filament\Penilaian\Resources\NilaiRemajaResource\Pages;

use App\Filament\Penilaian\Resources\NilaiRemajaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiRemajas extends ManageRecords
{
    protected static string $resource = NilaiRemajaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
