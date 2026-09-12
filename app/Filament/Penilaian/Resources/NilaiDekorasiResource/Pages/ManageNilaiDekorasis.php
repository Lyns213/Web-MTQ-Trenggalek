<?php

namespace App\Filament\Penilaian\Resources\NilaiDekorasiResource\Pages;

use App\Filament\Penilaian\Resources\NilaiDekorasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageNilaiDekorasis extends ManageRecords
{
    protected static string $resource = NilaiDekorasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    protected ?string $subheading = 'Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali';
}
