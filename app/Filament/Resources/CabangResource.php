<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CabangResource\Pages;
use App\Filament\Resources\CabangResource\RelationManagers;
use App\Models\Cabang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;

class CabangResource extends Resource
{
    protected static ?string $model = Cabang::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $navigationLabel = 'Cabang / Golongan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_cabang')
                    ->required()
                    ->maxLength(255)
                    ->label(__('Nama cabang lomba')),
                Forms\Components\Select::make('gender_cabang')
                    ->required()
                    ->native(false)
                    ->options([
                        'putra' => 'Putra',
                        'putri' => 'Putri',
                    ]),
                Forms\Components\TextInput::make('batas_umur')
                    ->required()
                    ->helperText(new HtmlString('<strong>Contoh :</strong> 10 tahun 11 bulan 29 hari')),
                Forms\Components\DatePicker::make('per_tanggal')
                    ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('d-m-Y'),
                Forms\Components\TimePicker::make('timer')
                    ->label(__('Durasi Lomba'))
                    ->prefixIcon('heroicon-m-play')
                    ->native(false)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('nama_cabang'),
                Tables\Columns\TextColumn::make('gender_cabang'),
                Tables\Columns\TextColumn::make('batas_umur'),
                Tables\Columns\TextColumn::make('per_tanggal')
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('kuota'),
                Tables\Columns\TextColumn::make('timer')
                    ->label(__('Durasi Lomba')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCabangs::route('/'),
            'create' => Pages\CreateCabang::route('/create'),
            'edit' => Pages\EditCabang::route('/{record}/edit'),
        ];
    }
}
