<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiSatuJuz;
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
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use App\Filament\Penilaian\Resources\NilaiSatuJuzResource\Pages;
use App\Filament\Penilaian\Resources\NilaiSatuJuzResource\RelationManagers;

class NilaiSatuJuzResource extends Resource
{
    protected static ?string $model = NilaiSatuJuz::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'MHQ 1 Juz dan Tilawah';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('peserta_id')
                // ->relationship('peserta', 'nama')
                ->live(onBlur: true)
                ->disabled()
                ->formatStateUsing(fn(NilaiSatuJuz $record): string => $record->peserta->nama ?? ''),
                Split::make([
                    Section::make([
                        TextInput::make('til_tajwid')
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
                                        ->body('Maksimal nilai untuk til_tajwid adalah 30')
                                        ->send();
                                }
                                $til_lagu = floatval($get('til_lagu'));
                                $til_suara = floatval($get('til_suara'));
                                $til_fashahah = floatval($get('til_fashahah'));
                                $total_tilawah = floatval($state) + $til_lagu + $til_suara + $til_fashahah;
                                $set('total_tilawah', floatval($total_tilawah));
                            }),

                        TextInput::make('til_lagu')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(25)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 25'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 25) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk Irama dan til_fashahah adalah 25')
                                        ->send();
                                }
                                $til_tajwid = floatval($get('til_tajwid'));
                                $til_suara = floatval($get('til_suara'));
                                $til_fashahah = floatval($get('til_fashahah'));
                                $total_tilawah = floatval($state) + $til_tajwid + $til_suara + $til_fashahah;
                                $set('total_tilawah', floatval($total_tilawah));
                            }),

                        TextInput::make('til_suara')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(15)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 15'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 15) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk til_suara adalah 15')
                                        ->send();
                                }
                                $til_tajwid = floatval($get('til_tajwid'));
                                $til_lagu = floatval($get('til_lagu'));
                                $til_fashahah = floatval($get('til_fashahah'));
                                $total_tilawah = floatval($state) + $til_tajwid + $til_lagu + $til_fashahah;
                                $set('total_tilawah', floatval($total_tilawah));
                            }),

                        TextInput::make('til_fashahah')
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
                                        ->body('Maksimal nilai untuk til_suara adalah 30')
                                        ->send();
                                }
                                $til_tajwid = floatval($get('til_tajwid'));
                                $til_lagu = floatval($get('til_lagu'));
                                $til_suara = floatval($get('til_suara'));
                                $total_tilawah = floatval($state) + $til_tajwid + $til_lagu + $til_suara;
                                $set('total_tilawah', floatval($total_tilawah));
                            }),
                    ]),



                    //PERBATASAN GUYS






                    Section::make([
                    TextInput::make('tah_tahfizh')
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
                                        ->body('Maksimal nilai untuk tah_tahfizh adalah 50')
                                        ->send();
                                }
                                $tah_tajwid = floatval($get('tah_tajwid'));
                                $tah_fashahah = floatval($get('tah_fashahah'));
                                $total_tahfizh = floatval($state) + $tah_tajwid + $tah_fashahah;
                                $set('total_tahfizh', floatval($total_tahfizh));
                            }),
                        TextInput::make('tah_tajwid')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(25)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 25'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 25) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk Irama dan Suara adalah 25')
                                        ->send();
                                }
                                $tah_tahfizh = floatval($get('tah_tahfizh'));
                                $tah_fashahah = floatval($get('tah_fashahah'));
                                $total_tahfizh = floatval($state) + $tah_tahfizh + $tah_fashahah;
                                $set('total_tahfizh', floatval($total_tahfizh));
                            }),
                        TextInput::make('tah_fashahah')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(25)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 25'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 25) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk tah_fashahah adalah 25')
                                        ->send();
                                }
                                $tah_tahfizh = floatval($get('tah_tahfizh'));
                                $tah_tajwid = floatval($get('tah_tajwid'));
                                $total_tahfizh = floatval($state) + $tah_tahfizh + $tah_tajwid;
                                $set('total_tahfizh', floatval($total_tahfizh));
                            }),

                        ]),

                    Section::make([
                        TextInput::make('total_tilawah')
                            ->numeric()
                            ->readOnly()
                            ->live(onBlur: true)
                            ->reactive(),
                    ]),
                    Section::make([
                        TextInput::make('total_tahfizh')
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
                    ->label('No')
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
                TextColumn::make('til_tajwid'),
                TextColumn::make('til_lagu'),
                TextColumn::make('til_suara'),
                TextColumn::make('til_fashahah'),
                TextColumn::make('tah_tahfizh'),
                TextColumn::make('tah_tajwid'),
                TextColumn::make('tah_fashahah'),
                TextColumn::make('total_tilawah'),
                TextColumn::make('total_tahfizh'),
                TextColumn::make('total'),
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
                // ExportAction::make()
                //     ->label(__('Download Excel'))
                //     ->color('success')
                //     ->exports([
                //         ExcelExport::make()->fromTable()->except([
                //             'index',
                //         ]),
                //     ])
                Action::make('viewNilaiSatuJuz')
                    ->label('Penilaian MHQ 1 Juz dan Tilawah')
                    ->url(route('nilai-satujuz.index'))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Input Nilai')
                    ->after(function ($data, $record) {
                        $record->total = $record->total_tilawah + $record->total_tahfizh;
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_total_tahfizh = $record->total_tahfizh * 1000000;
                        $record->bobot_til_tajwid = $record->til_tajwid * 10000;
                        $record->bobot_tah_tahfizh = $record->tah_tahfizh * 100;
                        $record->final_bobot = $record->bobot_total + $record->bobot_til_tajwid + $record->bobot_tah_tahfizh + $record->bobot_total_tahfizh;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(fn ($record): bool => $record->total_tilawah != 0 && $record->total_tilawah != null &&
                        $record->til_tajwid != 0 && $record->til_tajwid != null &&
                        $record->til_lagu != 0 && $record->til_lagu != null &&
                        $record->til_suara != 0 && $record->til_suara != null &&
                        $record->til_fashahah != 0 && $record->til_fashahah != null &&
                        $record->tah_tahfizh != 0 && $record->tah_tahfizh != null &&
                        $record->tah_tajwid != 0 && $record->tah_tajwid != null &&
                        $record->tah_fashahah != 0 && $record->tah_fashahah != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Nilai')
                    ->hidden(fn ($record): bool => $record->total_tilawah == 0 || $record->total_tilawah == null ||
                        $record->til_tajwid == 0 || $record->til_tajwid == null ||
                        $record->til_lagu == 0 || $record->til_lagu == null ||
                        $record->til_suara == 0 || $record->til_suara == null ||
                        $record->til_fashahah == 0 && $record->til_fashahah == null ||
                        $record->tah_tahfizh == 0 || $record->tah_tahfizh == null ||
                        $record->tah_tajwid == 0 || $record->tah_tajwid == null ||
                        $record->tah_fashahah == 0 || $record->tah_fashahah == null
                    ),
            ])
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
            'index' => Pages\ManageNilaiSatuJuzs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu==9 && Auth::user()->cabang_id_dua==10;
    }
}
