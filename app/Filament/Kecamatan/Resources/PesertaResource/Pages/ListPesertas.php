<?php

namespace App\Filament\Kecamatan\Resources\PesertaResource\Pages;

use App\Filament\Kecamatan\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;

class ListPesertas extends ListRecords
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        if (PesertaResource::isTahunAktif()) {
            return [
                Actions\CreateAction::make()
                    ->label('Tambah Peserta')
                    ->icon('heroicon-o-user-plus')
                    ->size(ActionSize::Large),
            ];
        }

        return [];
    }

    protected static ?string $title = 'Daftar Peserta';
}
