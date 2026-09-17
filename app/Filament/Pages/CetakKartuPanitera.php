<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CetakKartuPanitera extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Cetak Kartu';

    protected static ?string $navigationLabel = 'Panitera';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Cetak ID Card Panitera';

    protected static ?string $slug = 'cetak-kartu/panitera';

    protected static string $view = 'filament.pages.cetak-kartu-placeholder';

    public string $kategori = 'Panitera';
}
