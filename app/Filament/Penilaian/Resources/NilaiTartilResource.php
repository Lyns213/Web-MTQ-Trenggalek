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
use App\Filament\Penilaian\Concerns\HasLiveScoreActions;
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
                HasLiveScoreActions::getTimerTableColumn('tartil'),
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
            ->heading(function () {
                $tahunId = session('selected_tahun_id', '0');
                $html = Cache::remember('stats_hdr_tartil_' . $tahunId, 120, fn () => view('filament.penilaian.components.tartil-stats-header')->render());
                return new \Illuminate\Support\HtmlString($html);
            })
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
                Tables\Actions\Action::make('inputNilai')
                    ->label('')
                    ->tooltip(fn ($record) => ($record->total == 0 || $record->total == null) ? 'Input Nilai' : 'Lihat Nilai')
                    ->icon(fn ($record) => ($record->total == 0 || $record->total == null) ? 'heroicon-o-plus' : 'heroicon-o-eye')
                    ->color(fn ($record) => ($record->total == 0 || $record->total == null) ? 'success' : 'info')
                    ->extraAttributes(fn ($record) => [
                        'class' => 'btn-input-nilai',
                        'data-record-id' => $record->id,
                        'data-slug' => 'tartil',
                        'data-nama' => $record->peserta?->nama ?? '',
                        'data-tajwid' => floatval($record->tajwid ?? 0),
                        'data-irama' => floatval($record->irama_dan_suara ?? 0),
                        'data-fashahah' => floatval($record->fashahah ?? 0),
                        'data-total' => floatval($record->total ?? 0),
                    ])
                    ->alpineClickHandler(fn ($record) => "window.mtqOpenInputNilai('tartil', {$record->id}, " . json_encode($record->peserta?->nama ?? '') . ", " . floatval($record->tajwid ?? 0) . ", " . floatval($record->irama_dan_suara ?? 0) . ", " . floatval($record->fashahah ?? 0) . ", " . floatval($record->total ?? 0) . ")"),
                ...HasLiveScoreActions::getLiveScoreTableActions('tartil'),
            ])
            ->recordClasses(HasLiveScoreActions::getRecordClasses('tartil'))
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
