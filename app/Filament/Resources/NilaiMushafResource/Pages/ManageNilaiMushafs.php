<?php

namespace App\Filament\Resources\NilaiMushafResource\Pages;

use App\Filament\Resources\NilaiMushafResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiMushafs extends ManageRecords
{
    protected static string $resource = NilaiMushafResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
