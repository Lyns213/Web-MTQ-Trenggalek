<?php

namespace App\Filament\Penilaian\Resources\NilaiMushafResource\Pages;

use App\Filament\Penilaian\Resources\NilaiMushafResource;
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

    protected ?string $subheading = 'Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali';
}
