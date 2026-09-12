<?php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;

class EditPeserta extends EditRecord
{
    protected static string $resource = PesertaResource::class;

    protected static ?string $title = 'Edit Peserta';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Peserta')
                ->size(ActionSize::Large)
                ->icon('heroicon-o-user-minus'),
        ];
    }
}
