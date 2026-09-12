<?php

namespace App\Filament\Resources\PesertaVerifiedResource\Pages;

use App\Filament\Resources\PesertaVerifiedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPesertaVerified extends EditRecord
{
    protected static string $resource = PesertaVerifiedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
