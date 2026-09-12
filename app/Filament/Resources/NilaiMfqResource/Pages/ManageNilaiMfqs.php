<?php

namespace App\Filament\Resources\NilaiMfqResource\Pages;

use App\Filament\Resources\NilaiMfqResource;
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
