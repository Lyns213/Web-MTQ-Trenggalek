<?php

namespace App\Filament\Resources\UtusanResource\Pages;

use App\Filament\Resources\UtusanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUtusans extends ListRecords
{
    protected static string $resource = UtusanResource::class;

    protected static ?string $title = 'Daftar Utusan Kecamatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
