<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiKontemporer;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
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
use App\Filament\Penilaian\Resources\NilaiKontemporerResource\Pages;
use App\Filament\Penilaian\Resources\NilaiKontemporerResource\RelationManagers;

class NilaiKontemporerResource extends Resource
{
    protected static ?string $model = NilaiKontemporer::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'MKQ KONTEMPORER';

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
                            ->formatStateUsing(fn(NilaiKontemporer $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('unsur_kaligrafi')
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
                                        ->body('Maksimal nilai untuk unsur_kaligrafi adalah 30')
                                        ->send();
                                }
                                $unsur_seni_rupa = floatval($get('unsur_seni_rupa'));
                                $sentuhan_akhir = floatval($get('sentuhan_akhir'));
                                $total = floatval($state) + $unsur_seni_rupa + $sentuhan_akhir;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('unsur_seni_rupa')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(50)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 50'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 50) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk Irama dan Suara adalah 50')
                                        ->send();
                                }
                                $unsur_kaligrafi = floatval($get('unsur_kaligrafi'));
                                $sentuhan_akhir = floatval($get('sentuhan_akhir'));
                                $total = floatval($state) + $unsur_kaligrafi + $sentuhan_akhir;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('sentuhan_akhir')
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
                                        ->body('Maksimal nilai untuk sentuhan_akhir adalah 20')
                                        ->send();
                                }
                                $unsur_kaligrafi = floatval($get('unsur_kaligrafi'));
                                $unsur_seni_rupa = floatval($get('unsur_seni_rupa'));
                                $total = floatval($state) + $unsur_kaligrafi + $unsur_seni_rupa;
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
                TextColumn::make('unsur_kaligrafi'),
                TextColumn::make('unsur_seni_rupa'),
                TextColumn::make('sentuhan_akhir'),
                TextColumn::make('total'),
                HasLiveScoreActions::getTimerTableColumn('kontemporer'),
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
                HasLiveScoreActions::getLiveScoreHeaderAction('kontemporer'),
                // ExportAction::make()
                //     ->label(__('Download Excel'))
                //     ->color('success')
                //     ->exports([
                //         ExcelExport::make()->fromTable()->except([
                //             'index',
                //         ]),
                //     ])
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
                        $record->bobot_unsur_kaligrafi = $record->unsur_kaligrafi * 1000000;
                        $record->bobot_unsur_seni_rupa = $record->unsur_seni_rupa * 10000;
                        $record->bobot_sentuhan_akhir = $record->sentuhan_akhir * 100;
                        $record->final_bobot = $record->bobot_unsur_kaligrafi + $record->bobot_unsur_seni_rupa + $record->bobot_sentuhan_akhir + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(fn ($record): bool => $record->total != 0 && $record->total != null &&
                        $record->unsur_kaligrafi != 0 && $record->unsur_kaligrafi != null &&
                        $record->unsur_seni_rupa != 0 && $record->unsur_seni_rupa != null &&
                        $record->sentuhan_akhir != 0 && $record->sentuhan_akhir != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Lihat Nilai')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hidden(fn ($record): bool => $record->total == 0 || $record->total == null ||
                        $record->unsur_kaligrafi == 0 || $record->unsur_kaligrafi == null ||
                        $record->unsur_seni_rupa == 0 || $record->unsur_seni_rupa == null ||
                        $record->sentuhan_akhir == 0 || $record->sentuhan_akhir == null),
                ...HasLiveScoreActions::getLiveScoreTableActions('kontemporer'),
            ])
            ->recordClasses(HasLiveScoreActions::getRecordClasses('kontemporer'))
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
            'index' => Pages\ManageNilaiKontemporers::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu==29 && Auth::user()->cabang_id_dua==30;
    }
}
