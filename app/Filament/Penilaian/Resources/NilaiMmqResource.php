<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\NilaiMmq;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Filament\Penilaian\Concerns\HasLiveScoreActions;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Penilaian\Resources\NilaiMmqResource\Pages;
use App\Filament\Penilaian\Resources\NilaiMmqResource\RelationManagers;

class NilaiMmqResource extends Resource
{
    protected static ?string $model = NilaiMmq::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'KTIQ';

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
                            ->formatStateUsing(fn(NilaiMmq $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('bobot_materi')
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
                                        ->body('Maksimal nilai untuk bobot_materi adalah 40')
                                        ->send();
                                }
                                $kaidah_dan_gaya_bahasa = floatval($get('kaidah_dan_gaya_bahasa'));
                                $logika_dan_organisasi_pesan = floatval($get('logika_dan_organisasi_pesan'));
                                $presentasi = floatval($get('presentasi'));
                                $total = floatval($state) + $kaidah_dan_gaya_bahasa + $logika_dan_organisasi_pesan + $presentasi;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('kaidah_dan_gaya_bahasa')
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
                                        ->body('Maksimal nilai untuk Irama dan presentasi adalah 30')
                                        ->send();
                                }
                                $bobot_materi = floatval($get('bobot_materi'));
                                $logika_dan_organisasi_pesan = floatval($get('logika_dan_organisasi_pesan'));
                                $presentasi = floatval($get('presentasi'));
                                $total = floatval($state) + $bobot_materi + $logika_dan_organisasi_pesan + $presentasi;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('logika_dan_organisasi_pesan')
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
                                        ->body('Maksimal nilai untuk logika_dan_organisasi_pesan adalah 30')
                                        ->send();
                                }
                                $bobot_materi = floatval($get('bobot_materi'));
                                $kaidah_dan_gaya_bahasa = floatval($get('kaidah_dan_gaya_bahasa'));
                                $presentasi = floatval($get('presentasi'));
                                $total = floatval($state) + $bobot_materi + $kaidah_dan_gaya_bahasa + $presentasi;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('presentasi')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(20)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 20'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 20) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk logika_dan_organisasi_pesan adalah 20')
                                        ->send();
                                }
                                $bobot_materi = floatval($get('bobot_materi'));
                                $kaidah_dan_gaya_bahasa = floatval($get('kaidah_dan_gaya_bahasa'));
                                $logika_dan_organisasi_pesan = floatval($get('logika_dan_organisasi_pesan'));
                                $total = floatval($state) + $bobot_materi + $kaidah_dan_gaya_bahasa + $logika_dan_organisasi_pesan;
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
                TextColumn::make('bobot_materi'),
                TextColumn::make('kaidah_dan_gaya_bahasa'),
                TextColumn::make('logika_dan_organisasi_pesan'),
                TextColumn::make('presentasi'),
                TextColumn::make('total'),
                HasLiveScoreActions::getTimerTableColumn('mmq'),
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
            ->heading(fn () => new \Illuminate\Support\HtmlString(view('filament.penilaian.components.cabang-stats-header', ['modelClass' => static::$model])->render()))
            ->headerActions([
                HasLiveScoreActions::getLiveScoreHeaderAction('mmq'),
                // ExportAction::make()
                //     ->label(__('Download Excel'))
                //     ->color('success')
                //     ->exports([
                //         ExcelExport::make()->fromTable()->except([
                //             'index',
                //         ]),
                //     ])
                Action::make('viewNilaiMmq')
                    ->label('Penilaian MMQ')
                    ->url(route('nilai-mmq.index'))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Input Nilai')
                    ->icon('heroicon-o-plus')
                    ->color('success')
                    ->successNotificationTitle('Nilai berhasil disimpan')
                    ->after(function ($data, $record) {
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_bobot_materi = $record->bobot_materi * 1000000;
                        $record->bobot_kaidah_dan_gaya_bahasa = $record->kaidah_dan_gaya_bahasa * 10000;
                        $record->bobot_logika_dan_organisasi_pesan = $record->logika_dan_organisasi_pesan * 100;
                        $record->final_bobot = $record->bobot_bobot_materi + $record->bobot_kaidah_dan_gaya_bahasa + $record->bobot_logika_dan_organisasi_pesan + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(
                        fn($record): bool => $record->total != 0 && $record->total != null &&
                            $record->bobot_materi != 0 && $record->bobot_materi != null &&
                            $record->kaidah_dan_gaya_bahasa != 0 && $record->kaidah_dan_gaya_bahasa != null &&
                            $record->logika_dan_organisasi_pesan != 0 && $record->logika_dan_organisasi_pesan != null &&
                            $record->presentasi != 0 && $record->presentasi != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Nilai')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hidden(
                        fn($record): bool => $record->total == 0 || $record->total == null ||
                            $record->bobot_materi == 0 || $record->bobot_materi == null ||
                            $record->kaidah_dan_gaya_bahasa == 0 || $record->kaidah_dan_gaya_bahasa == null ||
                            $record->logika_dan_organisasi_pesan == 0 || $record->logika_dan_organisasi_pesan == null ||
                            $record->presentasi == 0 && $record->presentasi == null
                    ),
                ...HasLiveScoreActions::getLiveScoreTableActions('mmq'),
            ])
            ->recordClasses(HasLiveScoreActions::getRecordClasses('mmq'))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['peserta.utusan', 'peserta.cabang']);
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
            'index' => Pages\ManageNilaiMmqs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu == 31 && Auth::user()->cabang_id_dua == 32;
    }
}
