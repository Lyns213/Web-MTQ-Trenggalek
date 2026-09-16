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
use Illuminate\Support\Facades\Cache;
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
        $calcScript = '
            let tEl = document.querySelector(\'input[id$="tajwid"], input[name$="tajwid"]\');
            let iEl = document.querySelector(\'input[id$="irama_dan_suara"], input[name$="irama_dan_suara"]\');
            let fEl = document.querySelector(\'input[id$="fashahah"], input[name$="fashahah"]\');
            let totEl = document.querySelector(\'input[id$="total"], input[name$="total"]\');

            let t = parseFloat(tEl ? tEl.value : 0) || 0;
            let i = parseFloat(iEl ? iEl.value : 0) || 0;
            let f = parseFloat(fEl ? fEl.value : 0) || 0;

            if (totEl) {
                totEl.value = (t + i + f).toFixed(2);
                totEl.dispatchEvent(new Event(\'input\', { bubbles: true }));
            }
        ';

        return $form
            ->schema([
                Split::make([
                    Section::make([
                        TextInput::make('peserta_id')
                            ->label(__('Nama'))
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(fn(NilaiTartil $record): string => $record->peserta->nama ?? ''),
                        TextInput::make('tajwid')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(40)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 40'))
                            ->extraInputAttributes([
                                'oninput' => "if(parseFloat(this.value)>40){this.value=40;} {$calcScript}",
                            ]),
                        TextInput::make('irama_dan_suara')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(30)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 30'))
                            ->extraInputAttributes([
                                'oninput' => "if(parseFloat(this.value)>30){this.value=30;} {$calcScript}",
                            ]),
                        TextInput::make('fashahah')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(30)
                            ->helperText(new HtmlString('<strong>Petunjuk :</strong> Input nilai maksimal 30'))
                            ->extraInputAttributes([
                                'oninput' => "if(parseFloat(this.value)>30){this.value=30;} {$calcScript}",
                            ]),
                    ]),
                    Section::make([
                        TextInput::make('total')
                            ->numeric()
                            ->readOnly(),
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
                        if (Cache::get('mtq_live_active_tartil') != $record->id) {
                            return '';
                        }
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = Cache::get($cacheKey);

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
                        if (Cache::get('mtq_live_active_tartil') != $record->id) {
                            return [];
                        }
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = Cache::get($cacheKey);

                        if (!$timerState) {
                            return [];
                        }

                        $remaining = $timerState['remaining_seconds'];
                        if ($timerState['is_running'] && $timerState['started_at']) {
                            $elapsed = time() - $timerState['started_at'];
                            $remaining = max(0, $remaining - $elapsed);
                        }

                        return [
                            'data-remaining' => $remaining,
                            'data-total-seconds' => $timerState['total_seconds'] ?? 300,
                            'data-record-id' => $record->id,
                            'data-is-running' => ($timerState['is_running'] ?? false) ? 1 : 0,
                            'data-format' => 'hms',
                            'data-slug' => 'tartil',
                            'class' => 'timer-cell',
                        ];
                    })
                    ->color('warning')
                    ->weight('bold'),
                        ])
            ->actions([
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
            ->heading(fn () => new \Illuminate\Support\HtmlString(view('filament.penilaian.components.tartil-stats-header')->render()))
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
                    ->tooltip(fn ($record) => ($record->total == 0 || $record->total == null) ? 'Input Nilai' : 'Lihat Nilai')
                    ->icon(fn ($record) => ($record->total == 0 || $record->total == null) ? 'heroicon-o-plus' : 'heroicon-o-eye')
                    ->color(fn ($record) => ($record->total == 0 || $record->total == null) ? 'success' : 'info')
                    ->visible(fn ($record) => Cache::get('mtq_live_active_tartil') == $record->id)
                    ->successNotificationTitle('Nilai berhasil disimpan')
                    ->using(function (NilaiTartil $record, array $data): NilaiTartil {
                        $tajwid = min(40, max(0, floatval($data['tajwid'] ?? 0)));
                        $irama = min(30, max(0, floatval($data['irama_dan_suara'] ?? 0)));
                        $fashahah = min(30, max(0, floatval($data['fashahah'] ?? 0)));
                        $total = $tajwid + $irama + $fashahah;

                        $record->tajwid = $tajwid;
                        $record->irama_dan_suara = $irama;
                        $record->fashahah = $fashahah;
                        $record->total = $total;
                        $record->bobot_total = $total * 100000000;
                        $record->bobot_tajwid = $tajwid * 1000000;
                        $record->bobot_irama_dan_suara = $irama * 10000;
                        $record->bobot_fashahah = $fashahah * 100;
                        $record->final_bobot = $record->bobot_tajwid + $record->bobot_irama_dan_suara + $record->bobot_fashahah + $record->bobot_total;
                        $record->save();

                        Notification::make()
                            ->title('Nilai berhasil disimpan')
                            ->success()
                            ->send();

                        return $record;
                    })
                    ->modalHeading('Input Nilai')
                    ->modalDescription('Pastikan input nilai dengan tepat, karena kesempatan mengisi hanya sekali'),
                Action::make('showLive')
                    ->label('')
                    ->tooltip('Tampilkan Peserta')
                    ->icon('heroicon-o-tv')
                    ->color('info')
                    ->action(function ($record) {
                        Cache::put('mtq_live_active_tartil', $record->id, 86400);
                        \Filament\Notifications\Notification::make()
                            ->title('Peserta ditampilkan di live score')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => Cache::get('mtq_live_active_tartil') != $record->id),
                Action::make('unshowLive')
                    ->label('')
                    ->tooltip('Sembunyikan Peserta')
                    ->icon('heroicon-o-eye-slash')
                    ->color('gray')
                    ->action(function ($record) {
                        Cache::forget('mtq_live_active_tartil');
                        \Filament\Notifications\Notification::make()
                            ->title('Peserta disembunyikan dari live score')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => Cache::get('mtq_live_active_tartil') == $record->id),

                Action::make('toggleTimer')
                    ->label('')
                    ->tooltip(fn ($record) => (Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'Jeda Waktu' : 'Mulai Waktu')
                    ->icon(fn ($record) => (Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'heroicon-o-pause' : 'heroicon-o-play')
                    ->color(fn ($record) => (Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'warning' : 'success')
                    ->extraAttributes(fn ($record) => [
                        'class' => 'btn-toggle-timer',
                        'data-action-timer' => (Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'pause' : 'start',
                        'data-record-id' => $record->id,
                        'data-slug' => 'tartil',
                    ])
                    ->action(function ($record) {
                        $cacheKey = 'mtq_timer_tartil_' . $record->id;
                        $timerState = Cache::get($cacheKey);

                        if (!$timerState) {
                            $cabang = $record->peserta?->cabang;
                            $timer = $cabang ? $cabang->timer : '00:05:00';
                            $parts = explode(':', $timer);
                            $totalSeconds = count($parts) === 3 ? ((int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2]) : (count($parts) === 2 ? ((int)$parts[0] * 60 + (int)$parts[1]) : 300);

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
                            if ($timerState['remaining_seconds'] <= 0) {
                                $timerState['remaining_seconds'] = $timerState['total_seconds'];
                            }
                            if (!$timerState['is_running'] || empty($timerState['started_at'])) {
                                $timerState['started_at'] = time();
                            }
                            $timerState['is_running'] = true;
                            Cache::put('mtq_live_active_tartil', $record->id, 86400);
                            Notification::make()->title('Timer dimulai')->success()->send();
                        }

                        Cache::put($cacheKey, $timerState, 86400);
                    }),

                Action::make('resetTimer')
                    ->label('')
                    ->tooltip('Reset Waktu')
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reset Waktu')
                ->modalDescription('Apakah Anda yakin ingin mereset waktu?')
                ->modalSubmitActionLabel('Reset Waktu')
                ->extraAttributes(fn ($record) => [
                    'class' => 'btn-reset-timer',
                    'data-record-id' => $record->id,
                    'data-slug' => 'tartil',
                ])
                ->action(function ($record) {
                    $cacheKey = 'mtq_timer_tartil_' . $record->id;
                    $timerState = Cache::get($cacheKey);

                    if (!$timerState) {
                        $cabang = $record->peserta?->cabang;
                        $timer = $cabang ? $cabang->timer : '00:05:00';
                        $parts = explode(':', $timer);
                        $totalSeconds = count($parts) === 3 ? ((int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2]) : (count($parts) === 2 ? ((int)$parts[0] * 60 + (int)$parts[1]) : 300);
                        $timerState = [
                            'total_seconds' => $totalSeconds,
                            'remaining_seconds' => $totalSeconds,
                            'is_running' => false,
                            'started_at' => null,
                        ];
                    }

                    $timerState['is_running'] = false;
                    $timerState['started_at'] = null;
                    $timerState['remaining_seconds'] = $timerState['total_seconds'];
                    Cache::put($cacheKey, $timerState, 86400);
                    Notification::make()->title('Timer direset')->danger()->send();
                }),
            ])
            ->recordClasses(fn ($record) => (Cache::get('mtq_timer_tartil_' . $record->id)['is_running'] ?? false) ? 'timer-active-row' : '')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
