<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\NilaiMfq;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Resources\NilaiMfqResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\NilaiMfqResource\RelationManagers;

class NilaiMfqResource extends Resource
{
    protected static ?string $model = NilaiMfq::class;

    protected static ?int $navigationSort = 110;

    protected static ?string $navigationGroup = 'Penilaian';

    protected static ?string $navigationLabel = "MFQ";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('grup_id')
                            // ->relationship('peserta', 'nama')
                            ->label(__('Grup'))
                            ->live(onBlur: true)
                            ->disabled()
                            ->formatStateUsing(fn(NilaiMfq $record): string => $record->grup->nama ?? ''),
                TextInput::make('total')
                    ->numeric(),
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
                TextColumn::make('grup.nama')
                    ->label('Grup')
                    ->searchable(),
                TextColumn::make('total'),
            ])
            ->defaultSort('total', 'desc')
            ->filters([
                SelectFilter::make('peserta.jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'putra' => 'Laki-laki',
                        'putri' => 'Perempuan',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value === 'putra') {
                            // Filter peserta dengan cabang Tartil Putra atau Tartil Putri
                            return $query->whereHas('peserta', function (Builder $query) {
                                $query->where('jenis_kelamin', 'like', '%putra%');
                            });
                        } elseif ($value === 'putri') {
                            // Filter peserta dengan cabang Tilawah Anak-anak Putra atau Tilawah Anak-anak Putri
                            return $query->whereHas('peserta', function (Builder $query) {
                                $query->where('jenis_kelamin', 'like', '%putri%');
                            });
                        }
                    }),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Download Excel'))
                    ->color('success')
                    ->exports([
                        ExcelExport::make()->fromTable()->except([
                            'index',
                        ]),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Input Nilai')
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah'),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    
    public static function getEloquentQuery(): Builder
    {
        $selectedTahunId = session("selected_tahun_id", \App\Models\Tahun::where("is_active", true)->first()?->id);
        
        return parent::getEloquentQuery()->whereHas("peserta", function ($query) use ($selectedTahunId) {
            $query->where("tahun_id", $selectedTahunId);
        });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNilaiMfqs::route('/'),
        ];
    }
}
