<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Peserta;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\PesertaUnverified;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PesertaUnverifiedResource\Pages;
use App\Filament\Resources\PesertaUnverifiedResource\RelationManagers;

class PesertaUnverifiedResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?int $navigationSort = 52;

    protected static ?string $navigationLabel = 'Belum Diterima';

    protected static ?string $navigationIcon = 'heroicon-o-x-circle';

    protected static ?string $navigationGroup = 'Pendaftaran Peserta';

    public static function getNavigationBadge(): ?string
    {
        $selectedTahunId = session("selected_tahun_id", \App\Models\Tahun::where("is_active", true)->first()?->id);
        return static::getModel()::where('is_verified', false)->where("tahun_id", $selectedTahunId)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('nik'),
                TextColumn::make('nama'),
                TextColumn::make('tempat_dan_tanggal_lahir')
                    ->label('Tempat, Tanggal Lahir'),
                TextColumn::make('alamat_ktp')
                    ->limit(20),
                IconColumn::make('is_verified')
                    ->label('Diterima')
                    ->boolean(),
                    // ->action(function ($record, $column) {
                    //     $name = $column->getName();
                    //     $record->update([
                    //         $name => !$record->$name
                    //     ]);
                    // }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListPesertaUnverifieds::route('/'),
            // 'create' => Pages\CreatePesertaUnverified::route('/create'),
            // 'edit' => Pages\EditPesertaUnverified::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('is_verified', 0);
        
        $selectedTahunId = session("selected_tahun_id");
        if ($selectedTahunId) {
            $query = $query->where("tahun_id", $selectedTahunId);
        }
        
        return $query;
    }
}
