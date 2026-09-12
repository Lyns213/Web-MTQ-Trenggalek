<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\NilaiMsq;
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
use App\Filament\Resources\NilaiMsqResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\NilaiMsqResource\RelationManagers;

class NilaiMsqResource extends Resource
{
    protected static ?string $model = NilaiMsq::class;

    protected static ?int $navigationSort = 111;

    protected static ?string $navigationGroup = 'Penilaian';

    protected static ?string $navigationLabel = "MsQ";

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('peserta.no_peserta')
                    ->label('NoPes'),
                TextColumn::make('peserta.nama')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('peserta.jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn (NilaiMsq $record): string => $record->peserta->jenis_kelamin == 'putra' ? 'L' : 'P'),
                TextColumn::make('peserta.utusan.kecamatan'),
                TextColumn::make('terjemahan_dan_materi'),
                TextColumn::make('penghayatan_dan_retorika'),
                TextColumn::make('tilawah'),
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
                    ->after(function ($data, $record) {
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_terjemahan_dan_materi = $record->terjemahan_dan_materi * 1000000;
                        $record->bobot_penghayatan_dan_retorika = $record->penghayatan_dan_retorika * 10000;
                        $record->bobot_tilawah = $record->tilawah * 100;
                        $record->final_bobot = $record->bobot_terjemahan_dan_materi + $record->bobot_penghayatan_dan_retorika + $record->bobot_tilawah + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ManageNilaiMsqs::route('/'),
        ];
    }
}
