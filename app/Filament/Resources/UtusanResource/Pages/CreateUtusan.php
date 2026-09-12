<?php

namespace App\Filament\Resources\UtusanResource\Pages;

use App\Filament\Resources\UtusanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUtusan extends CreateRecord
{
    protected static string $resource = UtusanResource::class;

    protected static ?string $title = 'Tambah Utusan Kecamatan';
}
