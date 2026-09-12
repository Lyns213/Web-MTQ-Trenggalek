<?php

namespace App\Filament\Resources\UtusanResource\Pages;

use App\Filament\Resources\UtusanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUtusan extends EditRecord
{
    protected static string $resource = UtusanResource::class;
    protected static ?string $title = 'Edit Utusan Kecamatan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
