<?php

namespace App\Filament\Resources\PesertaUnverifiedResource\Pages;

use App\Filament\Resources\PesertaUnverifiedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesertaUnverifieds extends ListRecords
{
    protected static string $resource = PesertaUnverifiedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
