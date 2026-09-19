<?php

namespace App\Filament\Resources\PanitiaResource\Pages;

use App\Filament\Resources\PanitiaResource;
use App\Models\Official;
use App\Models\Tahun;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManagePanitias extends ManageRecords
{
    protected static string $resource = PanitiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Panitia')
                ->icon('heroicon-o-plus')
                ->modalHeading('Tambah Panitia Baru')
                ->mutateFormDataUsing(function (array $data): array {
                    $data['role'] = Official::ROLE_PANITIA;
                    $data['tahun_id'] = session('selected_tahun_id', Tahun::where('is_active', true)->first()?->id);
                    return $data;
                }),
        ];
    }
}
