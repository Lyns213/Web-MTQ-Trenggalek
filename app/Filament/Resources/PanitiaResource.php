<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PanitiaResource\Pages;
use App\Models\Official;
use App\Models\Tahun;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class PanitiaResource extends Resource
{
    protected static ?string $model = Official::class;

    protected static ?string $slug = 'cetak-kartu/panitia';

    protected static ?string $navigationGroup = 'Cetak Kartu';

    protected static ?string $navigationLabel = 'Panitia';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function getNavigationBadge(): ?string
    {
        $selectedTahunId = session('selected_tahun_id', Tahun::where('is_active', true)->first()?->id);

        return (string) static::getEloquentQuery()
            ->when($selectedTahunId, fn ($q) => $q->where('tahun_id', $selectedTahunId))
            ->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')
                    ->label('Nama Lengkap & Gelar')
                    ->required()
                    ->placeholder('Contoh: MUHAMMAD IRFAN, S.Kom')
                    ->maxLength(255),

                TextInput::make('jabatan')
                    ->label('Seksi / Jabatan')
                    ->placeholder('Contoh: Seksi Acara / Seksi Konsumsi')
                    ->maxLength(255),

                FileUpload::make('foto')
                    ->label('Pasfoto')
                    ->image()
                    ->directory('officials')
                    ->disk('public')
                    ->visibility('public')
                    ->imageEditor()
                    ->maxSize(5120)
                    ->helperText('Format JPG, PNG, atau WEBP (Maksimal 5MB)'),
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

                ImageColumn::make('foto')
                    ->label('Pasfoto')
                    ->circular()
                    ->disk('public')
                    ->defaultImageUrl(asset('images/logotgxmini.png')),

                TextColumn::make('nama')
                    ->label('Nama Lengkap & Gelar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('jabatan')
                    ->label('Seksi / Jabatan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->label('Ditambahkan')
                    ->dateTime('d M Y, H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('cetak')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->url(fn (Official $record): string => route('admin.cetak-kartu.official.single', $record->id))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('cetak_massal')
                    ->label('Download PDF Terpilih')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->action(function (Collection $records, Component $livewire): void {
                        $ids = $records->pluck('id')->join(',');
                        $url = route('admin.cetak-kartu.official.bulk', ['ids' => $ids]);
                        $livewire->js("window.location.href = '{$url}'");
                    })
                    ->deselectRecordsAfterCompletion(),
                DeleteBulkAction::make(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('role', Official::ROLE_PANITIA);

        $selectedTahunId = session('selected_tahun_id');
        if ($selectedTahunId) {
            $query->where('tahun_id', $selectedTahunId);
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePanitias::route('/'),
        ];
    }
}
