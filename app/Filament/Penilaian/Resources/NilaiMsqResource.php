<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\NilaiMsq;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Penilaian\Concerns\HasLiveScoreActions;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use App\Filament\Penilaian\Resources\NilaiMsqResource\Pages;
use App\Filament\Penilaian\Resources\NilaiMsqResource\RelationManagers;

class NilaiMsqResource extends Resource
{
    protected static ?string $model = NilaiMsq::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'MsQ';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make('peserta_id')
                            // ->relationship('peserta', 'nama')
                            ->live(onBlur: true)
                            ->disabled()
                            ->formatStateUsing(fn(NilaiMsq $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('terjemahan_dan_materi')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(40)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 40'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 40) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk terjemahan_dan_materi adalah 40')
                                        ->send();
                                }
                                $penghayatan_dan_retorika = floatval($get('penghayatan_dan_retorika'));
                                $tilawah = floatval($get('tilawah'));
                                $total = floatval($state) + $penghayatan_dan_retorika + $tilawah;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('penghayatan_dan_retorika')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(30)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 30'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 30) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk Irama dan Suara adalah 30')
                                        ->send();
                                }
                                $terjemahan_dan_materi = floatval($get('terjemahan_dan_materi'));
                                $tilawah = floatval($get('tilawah'));
                                $total = floatval($state) + $terjemahan_dan_materi + $tilawah;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('tilawah')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(30)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 30'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 30) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk tilawah adalah 30')
                                        ->send();
                                }
                                $terjemahan_dan_materi = floatval($get('terjemahan_dan_materi'));
                                $penghayatan_dan_retorika = floatval($get('penghayatan_dan_retorika'));
                                $total = floatval($state) + $terjemahan_dan_materi + $penghayatan_dan_retorika;
                                $set('total', floatval($total));
                            }),
                    ]),
                    Section::make([
                        TextInput::make('total')
                            ->numeric()
                            ->readOnly()
                            ->live(onBlur: true)
                            ->reactive(),
                    ]),
                ])
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
                TextColumn::make('peserta.no_peserta')
                    ->label('NoPes')
                    ->sortable(),
                TextColumn::make('peserta.nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('peserta.jenis_kelamin')
                    ->label('Jenis Kelamin'),
                TextColumn::make('peserta.utusan.kecamatan'),
                TextColumn::make('terjemahan_dan_materi'),
                TextColumn::make('penghayatan_dan_retorika'),
                TextColumn::make('tilawah'),
                TextColumn::make('total'),
                HasLiveScoreActions::getTimerTableColumn('msq'),
            ])
            ->defaultSort('final_bobot', 'desc')
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
                HasLiveScoreActions::getLiveScoreHeaderAction('msq'),
                // ExportAction::make()
                //     ->label(__('Download Excel'))
                //     ->color('success')
                //     ->exports([
                //         ExcelExport::make()->fromTable()->except([
                //             'index',
                //         ]),
                //     ])
                Action::make('viewNilaiMsq')
                    ->label('Penilaian MSQ')
                    ->url(route('nilai-msq.index'))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Input Nilai')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->after(function ($data, $record) {
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_terjemahan_dan_materi = $record->terjemahan_dan_materi * 1000000;
                        $record->bobot_penghayatan_dan_retorika = $record->penghayatan_dan_retorika * 10000;
                        $record->bobot_tilawah = $record->tilawah * 100;
                        $record->final_bobot = $record->bobot_terjemahan_dan_materi + $record->bobot_penghayatan_dan_retorika + $record->bobot_tilawah + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(fn ($record): bool => $record->total != 0 && $record->total != null &&
                        $record->terjemahan_dan_materi != 0 && $record->terjemahan_dan_materi != null &&
                        $record->penghayatan_dan_retorika != 0 && $record->penghayatan_dan_retorika != null &&
                        $record->tilawah != 0 && $record->tilawah != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Nilai')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hidden(fn ($record): bool => $record->total == 0 || $record->total == null ||
                        $record->terjemahan_dan_materi == 0 || $record->terjemahan_dan_materi == null ||
                        $record->penghayatan_dan_retorika == 0 || $record->penghayatan_dan_retorika == null ||
                        $record->tilawah == 0 || $record->tilawah == null),
                ...HasLiveScoreActions::getLiveScoreTableActions('msq'),
            ])
            ->recordClasses(HasLiveScoreActions::getRecordClasses('msq'))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $selectedTahunId = session("selected_tahun_id");
        if ($selectedTahunId) {
            $query = $query->whereHas("peserta", function ($q) use ($selectedTahunId) {
                $q->where("tahun_id", $selectedTahunId);
            });
        }
        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNilaiMsqs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu==21 && Auth::user()->cabang_id_dua==22;
    }
}
