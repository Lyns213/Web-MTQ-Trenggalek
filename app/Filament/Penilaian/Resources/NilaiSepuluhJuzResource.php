<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiSepuluhJuz;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Split;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Penilaian\Concerns\HasLiveScoreActions;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;
use App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource\Pages;
use App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource\RelationManagers;




class NilaiSepuluhJuzResource extends Resource
{
    protected static ?string $model = NilaiSepuluhJuz::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'MHQ 10 Juz';

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
                            ->formatStateUsing(fn(NilaiSepuluhJuz $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('tahfizh')
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
                                        ->body('Maksimal nilai untuk tahfizh adalah 50')
                                        ->send();
                                }
                                $tajwid = floatval($get('tajwid'));
                                $fashahah = floatval($get('fashahah'));
                                $total = floatval($state) + $tajwid + $fashahah;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('tajwid')
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
                                $tahfizh = floatval($get('tahfizh'));
                                $fashahah = floatval($get('fashahah'));
                                $total = floatval($state) + $tahfizh + $fashahah;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('fashahah')
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
                                        ->body('Maksimal nilai untuk Fashahah adalah 25')
                                        ->send();
                                }
                                $tahfizh = floatval($get('tahfizh'));
                                $tajwid = floatval($get('tajwid'));
                                $total = floatval($state) + $tahfizh + $tajwid;
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
                TextColumn::make('tahfizh'),
                TextColumn::make('tajwid'),
                TextColumn::make('fashahah'),
                TextColumn::make('total'),
                HasLiveScoreActions::getTimerTableColumn('sepuluhjuz'),
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
            ->heading(HasLiveScoreActions::getCabangStatsHeader(static::$model))
            ->headerActions([
                HasLiveScoreActions::getLiveScoreHeaderAction('sepuluhjuz'),
                Action::make('viewNilaiSepuluhJuz')
                    ->label('Penilaian MHQ 10 Juz')
                    ->url(route('nilai-sepuluhjuz.index'))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
            ])
            ->actions([
                ...HasLiveScoreActions::getLiveScoreTableActions('sepuluhjuz'),
            ])
            ->recordClasses(HasLiveScoreActions::getRecordClasses('sepuluhjuz'))
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
            'index' => Pages\ManageNilaiSepuluhJuzs::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu == 13 && Auth::user()->cabang_id_dua == 14;
    }
}
