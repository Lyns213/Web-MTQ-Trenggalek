<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PesertaVerifiedResource\Pages;
use App\Filament\Resources\PesertaVerifiedResource\RelationManagers;
use App\Models\PesertaVerified;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Peserta;
use Filament\Tables\Columns\ToggleColumn;
use PhpOffice\PhpSpreadsheet\Calculation\Logical\Boolean;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class PesertaVerifiedResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?int $navigationSort = 53;

    protected static ?string $navigationLabel = 'Peserta Yang Sah';

    protected static ?string $navigationGroup = 'Pendaftaran Peserta';

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    public static function getNavigationBadge(): ?string
    {
        $selectedTahunId = session("selected_tahun_id", \App\Models\Tahun::where("is_active", true)->first()?->id);
        return static::getModel()::where('is_verified', true)->where("tahun_id", $selectedTahunId)->count();
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
                Tables\Actions\Action::make('cetak')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->url(fn (Peserta $record): string => route('admin.cetak-kartu.peserta.single', $record->id)),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('cetak_massal')
                    ->label('Download PDF Terpilih')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records, \Livewire\Component $livewire): void {
                        $ids = $records->pluck('id')->join(',');
                        $url = route('admin.cetak-kartu.peserta.bulk', ['ids' => $ids]);
                        $livewire->js("window.location.href = '{$url}'");
                    })
                    ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListPesertaVerifieds::route('/'),
            'create' => Pages\CreatePesertaVerified::route('/create'),
            // 'edit' => Pages\EditPesertaVerified::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('is_verified', 1);
        
        $selectedTahunId = session("selected_tahun_id");
        if ($selectedTahunId) {
            $query = $query->where("tahun_id", $selectedTahunId);
        }
        
        return $query;
    }
}
