<?php

namespace App\Filament\Kecamatan\Resources\PesertaResource\Pages;

use App\Models\Grup;
use Filament\Actions;
use Carbon\Carbon;
use App\Models\Tahun;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Kecamatan\Resources\PesertaResource;

class CreatePeserta extends CreateRecord
{
    protected static string $resource = PesertaResource::class;

    protected static ?string $title = 'Tambah Peserta';

    public function mount(): void
    {
        if (!PesertaResource::isTahunAktif()) {
            \Filament\Notifications\Notification::make()
                ->title('Akses Ditolak')
                ->body('Pendaftaran peserta hanya tersedia untuk tahun yang aktif dan dalam rentang waktu pendaftaran.')
                ->danger()
                ->send();
            redirect()->route('filament.kecamatan.resources.pesertas.index');
            return;
        }
        parent::mount();
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Berhasil')
            ->body('Peserta berhasil didaftarkan');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $cabangId = $this->record->cabang_id;
        $tahunId = $this->record->tahun_id;
        $utusanId = $this->record->utusan_id;

        if($cabangId == 19 || $cabangId == 20){
            $grup = Grup::where('tahun_id', $tahunId)->where('utusan_id', $utusanId)->first();
            $this->record->grup_id = $grup->id;
            $this->record->save();
        }
    }


    protected function mutateFormDataBeforeCreate(array $data): array
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

}
