<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Grup;
use Filament\Tables;
use App\Models\Tahun;
use App\Models\Peserta;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TahunResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TahunResource\RelationManagers;

class TahunResource extends Resource
{
    protected static ?int $navigationSort = 151;
    protected static ?string $navigationLabel = 'Tahun';
    protected static ?string $navigationGroup = 'Pengaturan';
    protected static ?string $model = Tahun::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->numeric()
                    ->unique(ignoreRecord: true),
                DatePicker::make('batas_awal')
                    ->label('Tanggal Dibuka')
                    ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('d-m-Y'),
                DatePicker::make('batas_akhir')
                    ->label('Tanggal Ditutup')
                    ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('d-m-Y'),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('tahun'),
                ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
                TextColumn::make('batas_awal')
                    ->label('Tanggal Dibuka')
                    ->date('d-m-Y'),
                TextColumn::make('batas_akhir')
                    ->label('Tanggal Ditutup')
                    ->date('d-m-Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                ->before(function ($record) {
                        // Hapus peserta terkait (opsional, jika Anda ingin menghapus peserta juga)
                        Peserta::where('tahun_id', $record->id)->update(['grup_id' => null]);
                        Peserta::where('tahun_id', $record->id)->update(['tahun_id' => null]);

                        // Hapus grup terkait
                        Grup::where('tahun_id', $record->id)->delete();
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                // Hapus peserta terkait (opsional, jika Anda ingin menghapus peserta juga)
                                Peserta::where('tahun_id', $record->id)->update(['grup_id' => null]);
                                Peserta::where('tahun_id', $record->id)->update(['tahun_id' => null]);

                                // Hapus grup terkait
                                Grup::where('tahun_id', $record->id)->delete();
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTahuns::route('/'),
            'create' => Pages\CreateTahun::route('/create'),
            'edit' => Pages\EditTahun::route('/{record}/edit'),
        ];
    }
}
