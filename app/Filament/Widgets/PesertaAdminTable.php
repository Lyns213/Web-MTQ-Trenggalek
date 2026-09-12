<?php

namespace App\Filament\Widgets;

use App\Models\Peserta;
use App\Models\Tahun;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Support\Enums\Alignment;

class PesertaAdminTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;
    public function table(Table $table): Table
    {
        return $table
            ->heading('Tabel Peserta')
            ->query(Peserta::query()->where('tahun_id', session('selected_tahun_id', Tahun::where('is_active', true)->first()?->id))) // Mengambil semua data peserta
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
                // Tables\Columns\TextColumn::make('jenis_kelamin')
                //     ->label('Jenis Kelamin')
                //     ->sortable()
                //     ->searchable(),
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
            ->filters([
                SelectFilter::make('utusan.kecamatan')
                    ->relationship('utusan', 'kecamatan')
                    ->label(__(' Bace Kecamatan'))
                    ->native(false),
                SelectFilter::make('cabang_merged')
                    ->label('Bace Cabang')
                    ->options([
                        'tartil' => 'Tartil',
                        'tilawah anak-anak' => 'Tilawah Anak-anak',
                        'tilawah remaja' => 'Tilawah Remaja',
                        'tilawah dewasa' => 'Tilawah Dewasa',
                        'mhq 1 juz dan tilawah' => 'MHQ 1 juz dan Tilawah',
                        'mhq 5 juz dan tilawah' => 'MHQ 5 juz dan Tilawah',
                        'mhq 10 juz' => 'MHQ 10 juz',
                        'mhq 20 juz' => 'MHQ 20 juz',
                        'mhq 30 juz' => 'MHQ 30 juz',
                        'mfq' => 'MFQ',
                        'msq' => 'MSQ',
                        'mkq naskah' => 'MKQ Naskah',
                        'mkq hiasan mushaf' => 'MKQ Hiasan Mushaf',
                        'mkq dekorasi' => 'MKQ Dekorasi',
                        'mkq kontemporer' => 'MKQ Kontemporer',
                        'mmq' => 'MMQ',
                    ])
                    ->query(function (Builder $query, array $data) {
                        $value = $data['value'] ?? null;

                        if ($value === 'tartil') {
                            // Filter peserta dengan cabang Tartil Putra atau Tartil Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%Tartil%');
                            });
                        } elseif ($value === 'tilawah anak-anak') {
                            // Filter peserta dengan cabang Tilawah Anak-anak Putra atau Tilawah Anak-anak Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%Tilawah Anak-anak%');
                            });
                        } elseif ($value === 'tilawah remaja') {
                            // Filter peserta dengan cabang Tilawah Remaja Putra atau Tilawah Remaja Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%Tilawah Remaja%');
                            });
                        } elseif ($value === 'tilawah dewasa') {
                            // Filter peserta dengan cabang Tilawah Dewasa Putra atau Tilawah Dewasa Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%Tilawah Dewasa%');
                            });
                        } elseif ($value === 'mhq 1 juz dan tilawah') {
                            // Filter peserta dengan cabang MHQ 1 juz dan Tilawah Putra atau MHQ 1 juz dan Tilawah Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MHQ 1 juz dan Tilawah%');
                            });
                        } elseif ($value === 'mhq 5 juz dan tilawah') {
                            // Filter peserta dengan cabang MHQ 5 juz dan Tilawah Putra atau MHQ 5 juz dan Tilawah Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MHQ 5 juz dan Tilawah%');
                            });
                        } elseif ($value === 'mhq 10 juz') {
                            // Filter peserta dengan cabang MHQ 10 juz Putra atau MHQ 10 juz Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MHQ 10 juz%');
                            });
                        } elseif ($value === 'mhq 20 juz') {
                            // Filter peserta dengan cabang MHQ 20 juz Putra atau MHQ 20 juz Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MHQ 20 juz%');
                            });
                        } elseif ($value === 'mhq 30 juz') {
                            // Filter peserta dengan cabang MHQ 30 juz Putra atau MHQ 30 juz Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MHQ 30 juz%');
                            });
                        } elseif ($value === 'mfq') {
                            // Filter peserta dengan cabang MFQ Putra atau MFQ Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MFQ%');
                            });
                        } elseif ($value === 'msq') {
                            // Filter peserta dengan cabang MSQ Putra atau MSQ Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MSQ%');
                            });
                        } elseif ($value === 'mkq naskah') {
                            // Filter peserta dengan cabang MKQ Naskah Putra atau MKQ Naskah Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MKQ Naskah%');
                            });
                        } elseif ($value === 'mkq hiasan mushaf') {
                            // Filter peserta dengan cabang MKQ Hiasan Mushaf Putra atau MKQ Hiasan Mushaf Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MKQ Hiasan Mushaf%');
                            });
                        } elseif ($value === 'mkq dekorasi') {
                            // Filter peserta dengan cabang MKQ Dekorasi Putra atau MKQ Dekorasi Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MKQ Dekorasi%');
                            });
                        } elseif ($value === 'mkq kontemporer') {
                            // Filter peserta dengan cabang MKQ Kontemporer Putra atau MKQ Kontemporer Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MKQ Kontemporer%');
                            });
                        } elseif ($value === 'mmq') {
                            // Filter peserta dengan cabang MMQ Putra atau MMQ Putri
                            return $query->whereHas('cabang', function (Builder $query) {
                                $query->where('nama_cabang', 'like', '%MMQ%');
                            });
                        }
                        return $query;
                    })
                    ->native(false),
                SelectFilter::make('cabang.nama_cabang')
                    ->relationship('cabang', 'nama_cabang')
                    ->label(__('Cabang'))
                    ->native(false),
            ]);
    }
}
