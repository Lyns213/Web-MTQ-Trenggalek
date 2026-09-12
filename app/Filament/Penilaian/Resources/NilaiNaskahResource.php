<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiNaskah;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
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
use App\Filament\Penilaian\Resources\NilaiNaskahResource\Pages;
use App\Filament\Penilaian\Resources\NilaiNaskahResource\RelationManagers;

class NilaiNaskahResource extends Resource
{
    protected static ?string $model = NilaiNaskah::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'MKQ NASKAH';

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
                            ->formatStateUsing(fn(NilaiNaskah $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('kebenaran_kaidah_khat_wajib')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(60)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 60'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 60) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk kebenaran_kaidah_khat_wajib adalah 60')
                                        ->send();
                                }
                                $keindahan_khat_wajib = floatval($get('keindahan_khat_wajib'));
                                $kebenaran_kaidah_khat_pilihan = floatval($get('kebenaran_kaidah_khat_pilihan'));
                                $keindahan_khat_pilihan = floatval($get('keindahan_khat_pilihan'));
                                $total = floatval($state) + $keindahan_khat_wajib + $kebenaran_kaidah_khat_pilihan + $keindahan_khat_pilihan;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('keindahan_khat_wajib')
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
                                        ->body('Maksimal nilai untuk Irama dan keindahan_khat_pilihan adalah 40')
                                        ->send();
                                }
                                $kebenaran_kaidah_khat_wajib = floatval($get('kebenaran_kaidah_khat_wajib'));
                                $kebenaran_kaidah_khat_pilihan = floatval($get('kebenaran_kaidah_khat_pilihan'));
                                $keindahan_khat_pilihan = floatval($get('keindahan_khat_pilihan'));
                                $total = floatval($state) + $kebenaran_kaidah_khat_wajib + $kebenaran_kaidah_khat_pilihan + $keindahan_khat_pilihan;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('kebenaran_kaidah_khat_pilihan')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(60)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 60'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 60) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk kebenaran_kaidah_khat_pilihan adalah 60')
                                        ->send();
                                }
                                $kebenaran_kaidah_khat_wajib = floatval($get('kebenaran_kaidah_khat_wajib'));
                                $keindahan_khat_wajib = floatval($get('keindahan_khat_wajib'));
                                $keindahan_khat_pilihan = floatval($get('keindahan_khat_pilihan'));
                                $total = floatval($state) + $kebenaran_kaidah_khat_wajib + $keindahan_khat_wajib + $keindahan_khat_pilihan;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('keindahan_khat_pilihan')
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
                                        ->body('Maksimal nilai untuk kebenaran_kaidah_khat_pilihan adalah 40')
                                        ->send();
                                }
                                $kebenaran_kaidah_khat_wajib = floatval($get('kebenaran_kaidah_khat_wajib'));
                                $keindahan_khat_wajib = floatval($get('keindahan_khat_wajib'));
                                $kebenaran_kaidah_khat_pilihan = floatval($get('kebenaran_kaidah_khat_pilihan'));
                                $total = floatval($state) + $kebenaran_kaidah_khat_wajib + $keindahan_khat_wajib + $kebenaran_kaidah_khat_pilihan;
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
                TextColumn::make('kebenaran_kaidah_khat_wajib'),
                TextColumn::make('keindahan_khat_wajib'),
                TextColumn::make('kebenaran_kaidah_khat_pilihan'),
                TextColumn::make('keindahan_khat_pilihan'),
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
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Input Nilai')
                    ->after(function ($data, $record) {
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_kebenaran_kaidah_khat_wajib = $record->kebenaran_kaidah_khat_wajib * 1000000;
                        $record->bobot_keindahan_khat_wajib = $record->keindahan_khat_wajib * 10000;
                        $record->bobot_kebenaran_kaidah_khat_pilihan = $record->kebenaran_kaidah_khat_pilihan * 100;
                        $record->final_bobot = $record->bobot_kebenaran_kaidah_khat_wajib + $record->bobot_keindahan_khat_wajib + $record->bobot_kebenaran_kaidah_khat_pilihan + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(
                        fn($record): bool => $record->total != 0 && $record->total != null &&
                            $record->kebenaran_kaidah_khat_wajib != 0 && $record->kebenaran_kaidah_khat_wajib != null &&
                            $record->keindahan_khat_wajib != 0 && $record->keindahan_khat_wajib != null &&
                            $record->kebenaran_kaidah_khat_pilihan != 0 && $record->kebenaran_kaidah_khat_pilihan != null &&
                            $record->keindahan_khat_pilihan != 0 && $record->keindahan_khat_pilihan != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Nilai')
                    ->hidden(
                        fn($record): bool => $record->total == 0 || $record->total == null ||
                            $record->kebenaran_kaidah_khat_wajib == 0 || $record->kebenaran_kaidah_khat_wajib == null ||
                            $record->keindahan_khat_wajib == 0 || $record->keindahan_khat_wajib == null ||
                            $record->kebenaran_kaidah_khat_pilihan == 0 || $record->kebenaran_kaidah_khat_pilihan == null ||
                            $record->keindahan_khat_pilihan == 0 || $record->keindahan_khat_pilihan == null
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
            'index' => Pages\ManageNilaiNaskahs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu == 23 && Auth::user()->cabang_id_dua == 24;
    }
}
