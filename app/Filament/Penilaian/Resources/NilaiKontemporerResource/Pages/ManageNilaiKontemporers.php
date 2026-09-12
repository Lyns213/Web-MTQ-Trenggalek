<?php

namespace App\Filament\Penilaian\Resources\NilaiKontemporerResource\Pages;

use App\Filament\Penilaian\Resources\NilaiKontemporerResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiKontemporers extends ManageRecords
{
    protected static string $resource = NilaiKontemporerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
