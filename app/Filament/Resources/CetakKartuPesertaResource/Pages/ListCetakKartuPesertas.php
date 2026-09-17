<?php

namespace App\Filament\Resources\CetakKartuPesertaResource\Pages;

use App\Filament\Resources\CetakKartuPesertaResource;
use Filament\Resources\Pages\ListRecords;

class ListCetakKartuPesertas extends ListRecords
{
    protected static string $resource = CetakKartuPesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
