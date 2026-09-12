<?php

namespace App\Filament\Resources\NilaiMsqResource\Pages;

use App\Filament\Resources\NilaiMsqResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiMsqs extends ManageRecords
{
    protected static string $resource = NilaiMsqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
