<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiDewasa;
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
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Penilaian\Resources\NilaiDewasaResource\Pages;
use App\Filament\Penilaian\Resources\NilaiDewasaResource\RelationManagers;

class NilaiDewasaResource extends Resource
{
    protected static ?string $model = NilaiDewasa::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Tilawah Dewasa';

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
                            ->formatStateUsing(fn(NilaiDewasa $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('tajwid')
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
                                        ->body('Maksimal nilai untuk Tajwid adalah 30')
                                        ->send();
                                }
                                $lagu = floatval($get('lagu'));
                                $fashahah = floatval($get('fashahah'));
                                $suara = floatval($get('suara'));
                                $total = floatval($state) + $lagu + $fashahah + $suara;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('lagu')
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
                                $tajwid = floatval($get('tajwid'));
                                $fashahah = floatval($get('fashahah'));
                                $suara = floatval($get('suara'));
                                $total = floatval($state) + $tajwid + $fashahah + $suara;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('fashahah')
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
                                        ->body('Maksimal nilai untuk Fashahah adalah 30')
                                        ->send();
                                }
                                $tajwid = floatval($get('tajwid'));
                                $lagu = floatval($get('lagu'));
                                $suara = floatval($get('suara'));
                                $total = floatval($state) + $tajwid + $lagu + $suara;
                                $set('total', floatval($total));
                            }),

                        TextInput::make('suara')
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
                                        ->body('Maksimal nilai untuk Fashahah adalah 15')
                                        ->send();
                                }
                                $tajwid = floatval($get('tajwid'));
                                $lagu = floatval($get('lagu'));
                                $fashahah = floatval($get('fashahah'));
                                $total = floatval($state) + $tajwid + $lagu + $fashahah;
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
                TextColumn::make('tajwid'),
                TextColumn::make('lagu'),
                TextColumn::make('fashahah'),
                TextColumn::make('suara'),
                TextColumn::make('total'),
            ])
            ->paginated(false)
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
                Action::make('viewNilaiDewasa')
                    ->label('Penilaian Tilawah Dewasa')
                    ->url(route('nilai-dewasa.index'))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Input Nilai')
                    ->after(function ($data, $record) {
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_tajwid = $record->tajwid * 1000000;
                        $record->bobot_lagu = $record->lagu * 10000;
                        $record->bobot_fashahah = $record->fashahah * 100;
                        $record->final_bobot = $record->bobot_tajwid + $record->bobot_lagu + $record->bobot_fashahah + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai sudah sesuai, karena tidak bisa diubah')
                    ->hidden(fn ($record): bool => $record->total != 0 && $record->total != null &&
                        $record->tajwid != 0 && $record->tajwid != null &&
                        $record->lagu != 0 && $record->lagu != null &&
                        $record->fashahah != 0 && $record->fashahah != null &&
                        $record->suara != 0 && $record->suara != null
                    ),
                Tables\Actions\ViewAction::make()
                    ->label('Lihat Nilai')
                    ->hidden(fn ($record): bool => $record->total == 0 || $record->total == null ||
                        $record->tajwid == 0 || $record->tajwid == null ||
                        $record->lagu == 0 || $record->lagu == null ||
                        $record->fashahah == 0 || $record->fashahah == null ||
                        $record->suara == 0 || $record->suara == null
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
            'index' => Pages\ManageNilaiDewasas::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu==7 && Auth::user()->cabang_id_dua==8;
    }
}
