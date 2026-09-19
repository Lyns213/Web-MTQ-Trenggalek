<?php

namespace App\Filament\Resources\DewanHakimResource\Pages;

use App\Filament\Resources\DewanHakimResource;
use App\Models\Official;
use App\Models\Tahun;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDewanHakims extends ManageRecords
{
    protected static string $resource = DewanHakimResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Dewan Hakim')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah Dewan Hakim Baru')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['role'] = Official::ROLE_DEWAN_HAKIM;
                    $data['tahun_id'] = session('selected_tahun_id', Tahun::where('is_active', true)->first()?->id);
                    return $data;
                }),
        ];
    }
}
