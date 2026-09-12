<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiMushaf;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\NilaiMushafResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\NilaiMushafResource\RelationManagers;

class NilaiMushafResource extends Resource
{
    protected static ?string $model = NilaiMushaf::class;

    protected static ?int $navigationSort = 113;

    protected static ?string $navigationGroup = 'Penilaian';

    protected static ?string $navigationLabel = "MKQ HIASAN MUSHAF";

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
                            ->formatStateUsing(fn(NilaiMushaf $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('kebenaran_kaidah_khat')
                            ->numeric()
                            ->default(0)
                            ->live(onBlur: true)
                            ->maxValue(35)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 35'))
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                if ($state > 35) {
                                    Notification::make()
                                        ->title(('Nilai melebihi batas maksimum'))
                                        ->danger()
                                        ->body('Maksimal nilai untuk kebenaran_kaidah_khat adalah 35')
                                        ->send();
                                }
                                $keindahan_khat = floatval($get('keindahan_khat'));
                                $keindahan_hiasan_dan_lukisan = floatval($get('keindahan_hiasan_dan_lukisan'));
                                $total = floatval($state) + $keindahan_khat + $keindahan_hiasan_dan_lukisan;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('keindahan_khat')
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
                                $kebenaran_kaidah_khat = floatval($get('kebenaran_kaidah_khat'));
                                $keindahan_hiasan_dan_lukisan = floatval($get('keindahan_hiasan_dan_lukisan'));
                                $total = floatval($state) + $kebenaran_kaidah_khat + $keindahan_hiasan_dan_lukisan;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('keindahan_hiasan_dan_lukisan')
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
                                        ->body('Maksimal nilai untuk keindahan_hiasan_dan_lukisan adalah 40')
                                        ->send();
                                }
                                $kebenaran_kaidah_khat = floatval($get('kebenaran_kaidah_khat'));
                                $keindahan_khat = floatval($get('keindahan_khat'));
                                $total = floatval($state) + $kebenaran_kaidah_khat + $keindahan_khat;
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
                    ->formatStateUsing(fn (NilaiMushaf $record): string => $record->peserta->jenis_kelamin == 'putra' ? 'L' : 'P'),
                TextColumn::make('peserta.utusan.kecamatan'),
                TextColumn::make('kebenaran_kaidah_khat'),
                TextColumn::make('keindahan_khat'),
                TextColumn::make('keindahan_hiasan_dan_lukisan'),
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
                        $record->bobot_kebenaran_kaidah_khat = $record->kebenaran_kaidah_khat * 1000000;
                        $record->bobot_keindahan_khat = $record->keindahan_khat * 10000;
                        $record->bobot_keindahan_hiasan_dan_lukisan = $record->keindahan_hiasan_dan_lukisan * 100;
                        $record->final_bobot = $record->bobot_kebenaran_kaidah_khat + $record->bobot_keindahan_khat + $record->bobot_keindahan_hiasan_dan_lukisan + $record->bobot_total;
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
            'index' => Pages\ManageNilaiMushafs::route('/'),
        ];
    }
}
