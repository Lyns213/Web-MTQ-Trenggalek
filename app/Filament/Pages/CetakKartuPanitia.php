<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CetakKartuPanitia extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Cetak Kartu';

    protected static ?string $navigationLabel = 'Panitia';

    protected static ?int $navigationSort = 4;

    protected static ?string $title = 'Cetak ID Card Panitia';

    protected static ?string $slug = 'cetak-kartu/panitia';

    protected static string $view = 'filament.pages.cetak-kartu-placeholder';

    public string $kategori = 'Panitia';
}
