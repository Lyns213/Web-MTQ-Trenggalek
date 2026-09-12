<?php

namespace App\Filament\Penilaian\Resources\NilaiMmqResource\Pages;

use App\Filament\Penilaian\Resources\NilaiMmqResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiMmqs extends ManageRecords
{
    protected static string $resource = NilaiMmqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
