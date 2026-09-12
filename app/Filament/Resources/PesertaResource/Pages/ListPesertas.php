<?php

namespace App\Filament\Resources\PesertaResource\Pages;

use App\Filament\Resources\PesertaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;


class ListPesertas extends ListRecords
{
    protected static string $resource = PesertaResource::class;

    protected static ?string $title = 'Daftar Peserta';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()
            //     ->label('Tambah Peserta')
            //     ->size(ActionSize::Large)
            //     ->icon('heroicon-o-user-plus'),
            // ExportAction::make('export')
            //     ->label(__('Download Excel'))
            //     ->color('success'),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->color("info")
                ->label(__('Import Excel'))
                ->icon('heroicon-o-arrow-up-tray'),
        ];
    }
}
