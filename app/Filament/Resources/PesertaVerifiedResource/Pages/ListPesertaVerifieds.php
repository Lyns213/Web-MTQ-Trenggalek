<?php

namespace App\Filament\Resources\PesertaVerifiedResource\Pages;

use App\Filament\Resources\PesertaVerifiedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPesertaVerifieds extends ListRecords
{
    protected static string $resource = PesertaVerifiedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
