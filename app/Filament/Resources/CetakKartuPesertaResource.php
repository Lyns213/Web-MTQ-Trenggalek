<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CetakKartuPesertaResource\Pages;
use App\Models\Peserta;
use App\Models\Tahun;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class CetakKartuPesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $slug = 'cetak-kartu/peserta';

    protected static ?string $navigationLabel = 'Peserta';

    protected static ?string $navigationGroup = 'Cetak Kartu';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    public static function getNavigationBadge(): ?string
    {
        $selectedTahunId = session('selected_tahun_id', Tahun::where('is_active', true)->first()?->id);
        return (string) static::getModel()::where('is_verified', true)
            ->when($selectedTahunId, fn ($q) => $q->where('tahun_id', $selectedTahunId))
            ->count();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                ImageColumn::make('pasfoto')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(asset('images/logotgxmini.png')),
                TextColumn::make('no_peserta')
                    ->label('No. Peserta')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('nama')
                    ->label('Nama Peserta')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('utusan.kecamatan')
                    ->label('Kafilah / Utusan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('jenis_kelamin')
                    ->label('Golongan')
                    ->formatStateUsing(fn (?string $state): string => ucfirst($state ?? '-'))
                    ->badge()
                    ->color(fn (?string $state): string => $state === 'putra' ? 'info' : 'danger'),
                IconColumn::make('is_verified')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('is_verified')
                    ->label('Status Verifikasi')
                    ->options([
                        '1' => 'Sah (Terverifikasi)',
                        '0' => 'Belum Terverifikasi',
                    ])
                    ->default('1'),
                SelectFilter::make('cabang_id')
                    ->relationship('cabang', 'nama_cabang')
                    ->label('Cabang Lomba')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('utusan_id')
                    ->relationship('utusan', 'kecamatan')
                    ->label('Kafilah / Kecamatan')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('jenis_kelamin')
                    ->label('Golongan')
                    ->options([
                        'putra' => 'Putra',
                        'putri' => 'Putri',
                    ]),
            ])
            ->actions([
                Action::make('cetak')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->url(fn (Peserta $record): string => route('admin.cetak-kartu.peserta.single', $record->id)),
            ])
            ->bulkActions([
                BulkAction::make('cetak_massal')
                    ->label('Download PDF Terpilih')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->action(function (Collection $records, Component $livewire): void {
                        $ids = $records->pluck('id')->join(',');
                        $url = route('admin.cetak-kartu.peserta.bulk', ['ids' => $ids]);
                        $livewire->js("window.location.href = '{$url}'");
                    })
                    ->deselectRecordsAfterCompletion(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCetakKartuPesertas::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['cabang', 'utusan', 'tahun']);

        $selectedTahunId = session('selected_tahun_id');
        if ($selectedTahunId) {
            $query->where('tahun_id', $selectedTahunId);
        }

        return $query;
    }
}
