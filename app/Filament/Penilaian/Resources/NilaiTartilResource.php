<?php

namespace App\Filament\Penilaian\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\NilaiTartil;
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
use App\Filament\Penilaian\Resources\NilaiTartilResource\Pages;
use App\Filament\Resources\NilaiTartilResource\RelationManagers;

class NilaiTartilResource extends Resource
{
    protected static ?string $model = NilaiTartil::class;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Tartil';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make('peserta_id')
                            ->label(__('Nama'))
                            ->live(onBlur: true)
                            ->disabled()
                            ->formatStateUsing(fn(NilaiTartil $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('tajwid')
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
                                        ->body('Maksimal nilai untuk Tajwid adalah 40')
                                        ->send();
                                }
                                $irama_dan_suara = floatval($get('irama_dan_suara'));
                                $fashahah = floatval($get('fashahah'));
                                $total = floatval($state) + $irama_dan_suara + $fashahah;
                                $set('total', floatval($total));
                            }),
                        TextInput::make('irama_dan_suara')
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
                                $tajwid = floatval($get('tajwid'));
                                $fashahah = floatval($get('fashahah'));
                                $total = floatval($state) + $tajwid + $fashahah;
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
                                $irama_dan_suara = floatval($get('irama_dan_suara'));
                                $total = floatval($state) + $tajwid + $irama_dan_suara;
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
                ->from('md')
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
                TextColumn::make('irama_dan_suara'),
                TextColumn::make('fashahah'),
                TextColumn::make('total'),
                TextColumn::make('timer_countdown')
                    ->label('Timer')
                    ->getStateUsing(function ($record) {
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = \Cache::get($cacheKey);

                        if (!$timerState) {
                            return '';
                        }

                        $remaining = $timerState['remaining_seconds'];
                        if ($timerState['is_running'] && $timerState['started_at']) {
                            $elapsed = time() - $timerState['started_at'];
                            $remaining = max(0, $remaining - $elapsed);
                        }

                        $hours = floor($remaining / 3600);
                        $minutes = floor(($remaining % 3600) / 60);
                        $seconds = $remaining % 60;

                        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
                    })
                    ->extraAttributes(function ($record) {
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = \Cache::get($cacheKey);

                        if (!$timerState || !$timerState['is_running']) {
                            return [];
                        }

                        $remaining = $timerState['remaining_seconds'];
                        if ($timerState['started_at']) {
                            $elapsed = time() - $timerState['started_at'];
                            $remaining = max(0, $remaining - $elapsed);
                        }

                        return [
                            'data-remaining' => $remaining,
                            'data-record-id' => $record->id,
                            'class' => 'timer-cell',
                        ];
                    })
                    ->color('warning')
                    ->weight('bold'),
                        ])
            ->actions([
            ])
            ->paginated(false)
            ->poll('1s')
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
                            return $query->whereHas('peserta', function (Builder $query) {
                                $query->where('jenis_kelamin', 'like', '%putra%');
                            });
                        } elseif ($value === 'putri') {
                            return $query->whereHas('peserta', function (Builder $query) {
                                $query->where('jenis_kelamin', 'like', '%putri%');
                            });
                        }
                    }),
            ])
            ->headerActions([
                Action::make('viewNilaiTartil')
                    ->label('Penilaian Tartil')
                    ->url(route('nilai-tartil.index'))
                    ->icon('heroicon-o-eye')
                    ->openUrlInNewTab(),
                Action::make('viewNilaiTartilV2')
                    ->label('Penilaian Tartil V2')
                    ->url('/live-tartil')
                    ->icon('heroicon-o-tv')
                    ->color('info')
                    ->openUrlInNewTab(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->icon(fn ($record) => ($record->total == 0 || $record->total == null) ? 'heroicon-o-plus' : 'heroicon-o-eye')
                    ->color(fn ($record) => ($record->total == 0 || $record->total == null) ? 'success' : 'info')
                    ->after(function ($data, $record) {
                        $record->bobot_total = $record->total * 100000000;
                        $record->bobot_tajwid = $record->tajwid * 1000000;
                        $record->bobot_irama_dan_suara = $record->irama_dan_suara * 10000;
                        $record->bobot_fashahah = $record->fashahah * 100;
                        $record->final_bobot = $record->bobot_tajwid + $record->bobot_irama_dan_suara + $record->bobot_fashahah + $record->bobot_total;
                        $record->save();
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali'),
                Action::make('showLive')
                    ->label('')
                    ->icon('heroicon-o-tv')
                    ->color('info')
                    ->action(function ($record) {
                        \Cache::put('mtq_live_active_tartil', $record->id, 3600);
                        \Filament\Notifications\Notification::make()
                            ->title('Peserta ditampilkan di live score')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => \Cache::get('mtq_live_active_tartil') != $record->id),
                Action::make('unshowLive')
                    ->label('')
                    ->icon('heroicon-o-eye-slash')
                    ->color('gray')
                    ->action(function ($record) {
                        \Cache::forget('mtq_live_active_tartil');
                        \Filament\Notifications\Notification::make()
                            ->title('Peserta disembunyikan dari live score')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => \Cache::get('mtq_live_active_tartil') == $record->id),

                Action::make('toggleTimer')
                    ->label('')
                    ->icon(fn ($record) => (\Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => (\Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'warning' : 'success')
                    ->action(function ($record) {
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = \Cache::get($cacheKey);

                        if (!$timerState) {
                            $cabang = $record->peserta->cabang;
                            $timer = $cabang ? $cabang->timer : '00:05:00';
                            list($h, $m, $s) = explode(':', $timer);
                            $totalSeconds = ($h * 3600) + ($m * 60) + $s;

                            $timerState = [
                                'total_seconds' => $totalSeconds,
                                'remaining_seconds' => $totalSeconds,
                                'is_running' => false,
                                'started_at' => null,
                            ];
                        }

                        if ($timerState['is_running']) {
                            if ($timerState['started_at']) {
                                $elapsed = time() - $timerState['started_at'];
                                $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
                            }
                            $timerState['is_running'] = false;
                            $timerState['started_at'] = null;
                            Notification::make()->title('Timer dijeda')->warning()->send();
                        } else {
                            if ($timerState['remaining_seconds'] > 0) {
                                $timerState['is_running'] = true;
                                $timerState['started_at'] = time();
                                Notification::make()->title('Timer dimulai')->success()->send();
                            }
                        }

                        \Cache::put($cacheKey, $timerState, 3600);
                    }),

                Action::make('resetTimer')
                    ->label('')
                    ->icon('heroicon-o-arrow-path')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = \Cache::get($cacheKey);

                        if ($timerState) {
                            $timerState['is_running'] = false;
                            $timerState['started_at'] = null;
                            $timerState['remaining_seconds'] = $timerState['total_seconds'];
                            \Cache::put($cacheKey, $timerState, 3600);
                            Notification::make()->title('Timer direset')->danger()->send();
                        }
                    }),
            ])
            ->recordClasses(fn ($record) => (\Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'timer-active-row' : '')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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

    public static function getHeaderWidgets(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageNilaiTartils::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return Auth::user()->cabang_id_satu==1 && Auth::user()->cabang_id_dua==2;
    }
}
