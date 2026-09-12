<?php

namespace App\Filament\Penilaian\Resources\NilaiMsqResource\Pages;

use App\Filament\Penilaian\Resources\NilaiMsqResource;
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

    protected ?string $subheading = 'Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali';
}
