<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CetakKartuDewanHakim extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Cetak Kartu';

    protected static ?string $navigationLabel = 'Dewan Hakim';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Cetak ID Card Dewan Hakim';

    protected static ?string $slug = 'cetak-kartu/dewan-hakim';

    protected static string $view = 'filament.pages.cetak-kartu-placeholder';

    public string $kategori = 'Dewan Hakim';
}
