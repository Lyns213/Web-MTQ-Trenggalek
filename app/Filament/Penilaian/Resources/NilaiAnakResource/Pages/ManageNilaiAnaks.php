<?php

namespace App\Filament\Penilaian\Resources\NilaiAnakResource\Pages;

use App\Filament\Penilaian\Resources\NilaiAnakResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiAnaks extends ManageRecords
{
    protected static string $resource = NilaiAnakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
