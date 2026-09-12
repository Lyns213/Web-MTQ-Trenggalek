<?php

namespace App\Filament\Kecamatan\Resources\PesertaResource\Pages;

use App\Filament\Kecamatan\Resources\PesertaResource;
use Filament\Actions;
use Carbon\Carbon;
use App\Models\Tahun;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;

class EditPeserta extends EditRecord
{
    protected static string $resource = PesertaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Hapus Peserta')
                ->size(ActionSize::Large)
                ->icon('heroicon-o-user-minus')
                ->hidden(fn() => !PesertaResource::isTahunAktif()),
        ];
    }

    protected static ?string $title = 'Edit Peserta';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $tahunAktif = \App\Models\Tahun::where('is_active', true)->first();
        if (!$tahunAktif || !\Carbon\Carbon::now()->between($tahunAktif->batas_awal, $tahunAktif->batas_akhir)) {
            \Filament\Notifications\Notification::make()
                ->title('Pendaftaran Ditutup')
                ->body('Pendaftaran peserta hanya dapat dilakukan dalam rentang waktu yang ditentukan.')
                ->danger()
                ->send();
            throw new \Exception('Pendaftaran peserta ditutup.');
        }
        return $data;
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        if (!PesertaResource::isTahunAktif()) {
            Notification::make()
                ->title('Pendaftaran Ditutup')
                ->body('Edit peserta tidak dapat dilakukan karena tidak ada tahun aktif yang berjalan.')
                ->danger()
                ->send();
            $this->redirect(PesertaResource::getUrl('index'));
        }
    }
}
