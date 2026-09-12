<?php

namespace App\Filament\Resources\NilaiRemajaResource\Pages;

use App\Filament\Resources\NilaiRemajaResource;
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
