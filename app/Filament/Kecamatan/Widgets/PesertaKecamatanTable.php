<?php

namespace App\Filament\Kecamatan\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Peserta;
use App\Models\Tahun;
use App\Models\Cabang;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\Alignment;

class PesertaKecamatanTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {
        return $table
            ->heading('Daftar Peserta Se-Kabupaten Trenggalek')
            ->query(Peserta::query()->where('tahun_id', session('selected_tahun_id', \App\Models\Tahun::where('is_active', true)->first()?->id))) // Mengambil semua data peserta
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Peserta')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('utusan.kecamatan')
                    ->label('Utusan Kecamatan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('cabang.nama_cabang')
                    ->label('Cabang Lomba')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make("jenis_kelamin")
                                    ->label('Jenis Kelamin')
                                    ->sortable()
                                    ->searchable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->label('Status Verifikasi')
                    ->searchable()
                    ->alignment(Alignment::Center),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Daftar Peserta Kosong')
            ->emptyStateDescription('Silahkan daftarkan peserta dengan memilih "Daftar Peserta"')
            ->filters([
                SelectFilter::make('utusan.kecamatan')
                    ->relationship('utusan', 'kecamatan')
                    ->label(__('Kecamatan'))
                    ->native(false),
                SelectFilter::make('cabang.nama_cabang')
                    ->relationship('cabang', 'nama_cabang')
                    ->label(__('Cabang'))
                    ->native(false),
                SelectFilter::make('jenis_kelamin')
                    ->label(__('Jenis Kelamin'))
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'Laki-laki') {
                            return $query->whereHas('cabang', function (Builder $q) {
                                $q->where('nama_cabang', 'like', '%Putra%');
                            });
                        } elseif ($data['value'] === 'Perempuan') {
                            return $query->whereHas('cabang', function (Builder $q) {
                                $q->where('nama_cabang', 'like', '%Putri%');
                            });
                        }
                        return $query;
                    })
                    ->native(false),
            ]);
    }
}
