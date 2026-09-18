<?php

namespace App\Filament\Resources\PaniteraResource\Pages;

use App\Filament\Resources\PaniteraResource;
use App\Models\Official;
use App\Models\Tahun;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePaniteras extends ManageRecords
{
    protected static string $resource = PaniteraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Panitera')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah Panitera Baru')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['role'] = Official::ROLE_PANITERA;
                    $data['tahun_id'] = session('selected_tahun_id', Tahun::where('is_active', true)->first()?->id);
                    return $data;
                }),
        ];
    }
}
