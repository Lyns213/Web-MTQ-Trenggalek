<?php

namespace App\Filament\Penilaian\Resources\NilaiMfqResource\Pages;

use App\Filament\Penilaian\Resources\NilaiMfqResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiMfqs extends ManageRecords
{
    protected static string $resource = NilaiMfqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
