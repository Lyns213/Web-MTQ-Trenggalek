<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\NilaiMfq;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Penilaian\Concerns\HasLiveScoreActions;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Penilaian\Resources\NilaiMfqResource\Pages;
use App\Filament\Penilaian\Resources\NilaiMfqResource\RelationManagers;

class NilaiMfqResource extends Resource
{
    protected static ?string $model = NilaiMfq::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'MFQ';

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
                    ->label('Rank')
                    ->rowIndex(),
                TextColumn::make('grup.nama')
                    ->label('Grup')
                    ->searchable(),
                TextColumn::make('total'),
                HasLiveScoreActions::getTimerTableColumn('mfq'),
            ])
            ->defaultSort('total', 'desc')
            ->filters([
                SelectFilter::make('grup.jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'putra' => 'Laki-laki',
                        'putri' => 'Perempuan',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value === 'putra') {
                            // Filter peserta dengan cabang Tartil Putra atau Tartil Putri
                            return $query->whereHas('grup', function (Builder $query) {
                                $query->where('jenis_kelamin', 'putra');
                            });
                        } elseif ($value === 'putri') {
                            // Filter peserta dengan cabang Tilawah Anak-anak Putra atau Tilawah Anak-anak Putri
                            return $query->whereHas('grup', function (Builder $query) {
                                $query->where('jenis_kelamin', 'putri');
                            });
                        }
                    }),
            ])
            ->headerActions([
                HasLiveScoreActions::getLiveScoreHeaderAction('mfq'),
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
                    ->label('')
                    ->tooltip('Input Nilai')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(
                        fn($record): bool => $record->total != 0 && $record->total != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Nilai')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hidden(
                        fn($record): bool => $record->total == 0 || $record->total == null
                    ),
                ...HasLiveScoreActions::getLiveScoreTableActions('mfq'),
            ])
            ->recordClasses(HasLiveScoreActions::getRecordClasses('mfq'))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $selectedTahunId = session("selected_tahun_id");
        if ($selectedTahunId) {
            $query = $query->whereHas("grup", function ($q) use ($selectedTahunId) {
                $q->where("tahun_id", $selectedTahunId);
            });
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNilaiMfqs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu==19 && Auth::user()->cabang_id_dua==20;
    }

}
