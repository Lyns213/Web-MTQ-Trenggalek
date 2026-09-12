<?php

namespace App\Filament\Kecamatan\Resources;

use App\Filament\Kecamatan\Resources\PesertaResource\Pages;
use App\Filament\Kecamatan\Resources\PesertaResource\RelationManagers;
use App\Models\Peserta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Collection;
use App\Models\Cabang;
use Filament\Forms\Get;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Livewire\Attributes\Reactive;
use PhpParser\Node\Stmt\Label;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use App\Models\Utusan;
use App\Models\Tahun;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Support\Enums\Alignment;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Daftar Peserta';

    public static function isTahunAktif(): bool
    {
        $tahunId = session("selected_tahun_id");
        if (!$tahunId) {
            return false;
        }
        $tahun = Tahun::find($tahunId);
        return $tahun && $tahun->is_active && Carbon::now()->between($tahun->batas_awal, $tahun->batas_akhir);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->label(__('NIK'))
                    ->validationAttribute('NIK')
                    ->required()
                    ->numeric()
                    ->minLength(16)
                    ->maxLength(16)
                    ->live(onBlur: true)
                    ->validationMessages([
                        'required' => 'Kolom NIK tidak boleh kosong',
                        'numeric' => 'NIK harus berupa angka',
                    ])
                    ->rules([
                        fn(Get $get, $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record) {
                            if (strlen($value) !== 16) {
                                $fail("NIK harus terdiri dari 16 digit.");
                            }
                            $tahunId = $get('tahun_id');
                            if (!$tahunId) {
                                $fail('Tahun belum dipilih.');
                            }
                            // Cek duplikat NIK HANYA saat TAMBAH ($record null).
                            // Saat EDIT langsung simpan, tanpa pengecekan NIK.
                            if ($record || !$tahunId || strlen($value) !== 16) {
                                return;
                            }
                            $pesertaExists = Peserta::where('nik', $value)
                                ->where('tahun_id', $tahunId)
                                ->exists();
                            if ($pesertaExists) {
                                $fail('NIK sudah pernah didaftarkan, periksa kembali');
                            }
                        },
                    ])
                    ->afterStateUpdated(function ($state, $set, $get, $record) {
                        if (strlen($state) > 16) {
                            $set('nik', substr($state, 0, 16));
                            Notification::make()
                                ->title(__('NIK Tidak Valid'))
                                ->danger()
                                ->body('Pastikan NIK berjumlah 16 digit')
                                ->send();
                        } elseif (strlen($state) < 16) {
                            Notification::make()
                                ->title(__('NIK Tidak Valid'))
                                ->danger()
                                ->body('Pastikan NIK berjumlah 16 digit')
                                ->send();
                        } else {
                            $tahunId = $get('tahun_id');
                            if (!$tahunId) {
                                Notification::make()
                                    ->title(__('Tahun belum dipilih'))
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $pesertaExists = Peserta::where('nik', $state)
                                ->where('tahun_id', $tahunId)
                                ->exists();
                            if ($pesertaExists) {
                                Notification::make()
                                    ->title(__('NIK Sudah Terdaftar'))
                                    ->danger()
                                    ->body('NIK sudah pernah didaftarkan, periksa kembali')
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(__('NIK Valid'))
                                    ->success()
                                    ->send();
                            }
                        }
                    }),

                Forms\Components\TextInput::make('nama')
                    ->label(__('Nama Lengkap'))
                    ->required()
                    ->notIn(['fukri', 'rejected'])
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Kolom Nama tidak boleh kosong',
                    ]),

                Forms\Components\Select::make('jenis_kelamin')
                    ->label(__('Jenis Kelamin'))
                    ->required()
                    ->options([
                        'putra' => 'Laki-Laki',
                        'putri' => 'Perempuan',
                    ])
                    ->native(false)
                    ->live()
                    ->validationMessages([
                        'required' => 'Kolom Jenis Kelamin tidak boleh kosong',
                    ])
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $set('cabang_id', null);
                    }),

                Forms\Components\TextInput::make('tempat_lahir')
                    ->label(__('Tempat Lahir'))
                    ->required()
                    ->maxLength(50)
                    ->validationMessages([
                        'required' => 'Kolom Tempat Lahir tidak boleh kosong',
                    ]),

                Forms\Components\DatePicker::make('tgl_lahir')
                    ->label(__('Tanggal Lahir'))
                    ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('d-m-Y')
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $set('cabang_id', null);
                    })
                    ->validationMessages([
                        'required' => 'Kolom Tanggal Lahir tidak boleh kosong',
                    ]),

                Forms\Components\Textarea::make('alamat_ktp')
                    ->label(__('Alamat KTP'))
                    ->required()
                    ->maxLength(500)
                    ->validationMessages([
                        'required' => 'Kolom Alamat KTP tidak boleh kosong',
                    ]),

                Hidden::make('utusan_id')
                    ->label(__('Utusan Kecamatan'))
                    ->required()
                    ->default(function () {
                        $user = Auth::user();
                        return $user ? $user->utusan_id : null;
                    })
                    ->live(),

                Forms\Components\Select::make('cabang_id')
                    ->label(__('Cabang yang Diikuti'))
                    ->required()
                    ->options(function (Get $get) {
                        $jenisKelamin = $get('jenis_kelamin');
                        $utusanId = $get('utusan_id');
                        $tahunId = $get('tahun_id');
                        $tglLahir = $get('tgl_lahir');

                        if (!$jenisKelamin || !$utusanId || !$tahunId) {
                            return [];
                        }

                        $query = Cabang::query()
                            ->where('gender_cabang', $jenisKelamin);

                        $cabangs = $query->get();

                        return $cabangs->filter(function ($cabang) use ($tglLahir) {
                            // Filter berdasarkan usia jika tgl_lahir sudah diisi
                            if ($tglLahir) {
                                $perTanggal = Carbon::parse($cabang->per_tanggal);
                                $diff = Carbon::parse($tglLahir)->diff($perTanggal);
                                $usiaTahun = $diff->y;
                                $usiaBulan = $diff->m;
                                $usiaHari = $diff->d;

                                preg_match('/(\d+)\s*tahun/', $cabang->batas_umur, $tahunMatches);
                                preg_match('/(\d+)\s*bulan/', $cabang->batas_umur, $bulanMatches);
                                preg_match('/(\d+)\s*hari/', $cabang->batas_umur, $hariMatches);
                                $maxTahun = isset($tahunMatches[1]) ? (int) $tahunMatches[1] : 0;
                                $maxBulan = isset($bulanMatches[1]) ? (int) $bulanMatches[1] : 0;
                                $maxHari = isset($hariMatches[1]) ? (int) $hariMatches[1] : 0;

                                // Cek apakah usia melebihi batas
                                if ($usiaTahun > $maxTahun) {
                                    return false;
                                } elseif ($usiaTahun == $maxTahun && $usiaBulan > $maxBulan) {
                                    return false;
                                } elseif ($usiaTahun == $maxTahun && $usiaBulan == $maxBulan && $usiaHari > $maxHari) {
                                    return false;
                                }
                            }
                            return true;
                        })->mapWithKeys(function ($cabang) use ($utusanId, $tahunId) {
                            $jumlahPeserta = Peserta::where('utusan_id', $utusanId)
                                ->where('tahun_id', $tahunId)
                                ->where('cabang_id', $cabang->id)
                                ->count();
                            $sisaKuota = max(0, $cabang->kuota - $jumlahPeserta);
                            $label = $cabang->nama_cabang;
                            if ($sisaKuota <= 0) {
                                $label .= ' (Kuota Habis)';
                            } else {
                                $label .= ' (Sisa Kuota: ' . $sisaKuota . ')';
                            }
                            return [$cabang->id => $label];
                        });
                    })
                    ->native(false)
                    ->live()
                    ->searchable()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $cabang = Cabang::find($state);
                        if ($cabang) {
                            $tglLahir = $get('tgl_lahir');
                            if ($tglLahir) {
                                $perTanggal = Carbon::parse($cabang->per_tanggal);
                                // Parse batas_umur: format "X tahun Y bulan Z hari"
                                preg_match('/(\d+)\s*tahun/', $cabang->batas_umur, $tahunMatches);
                                preg_match('/(\d+)\s*bulan/', $cabang->batas_umur, $bulanMatches);
                                preg_match('/(\d+)\s*hari/', $cabang->batas_umur, $hariMatches);
                                $maxTahun = isset($tahunMatches[1]) ? (int) $tahunMatches[1] : 0;
                                $maxBulan = isset($bulanMatches[1]) ? (int) $bulanMatches[1] : 0;
                                $maxHari = isset($hariMatches[1]) ? (int) $hariMatches[1] : 0;

                                // Hitung usia peserta di tanggal per_tanggal
                                $diff = Carbon::parse($tglLahir)->diff($perTanggal);
                                $usiaTahun = $diff->y;
                                $usiaBulan = $diff->m;
                                $usiaHari = $diff->d;

                                // Cek apakah usia melebihi batas
                                $melebihiBatas = false;
                                if ($usiaTahun > $maxTahun) {
                                    $melebihiBatas = true;
                                } elseif ($usiaTahun == $maxTahun && $usiaBulan > $maxBulan) {
                                    $melebihiBatas = true;
                                } elseif ($usiaTahun == $maxTahun && $usiaBulan == $maxBulan && $usiaHari > $maxHari) {
                                    $melebihiBatas = true;
                                }

                                if ($melebihiBatas) {
                                    $set('cabang_id', null);
                                    Notification::make()
                                        ->title('Usia Tidak Sesuai')
                                        ->body('Usia peserta (' . $usiaTahun . ' tahun ' . $usiaBulan . ' bulan ' . $usiaHari . ' hari) melebihi batas usia cabang ini (' . $cabang->batas_umur . ').')
                                        ->danger()
                                        ->send();
                                }
                            }
                        }
                    })
                    ->validationMessages([
                        'required' => 'Kolom Cabang yang Diikuti tidak boleh kosong',
                    ]),

                FileUpload::make("scan_surat_mandat")
                    ->label(__("Scan Surat Mandat (Tanda Tangan Camat)"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan surat mandat berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_surat_mandat')
                    ->label('Preview Scan Surat Mandat (Tanda Tangan Camat)')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_surat_mandat
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_surat_mandat) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make("scan_biodata")
                    ->label(__("Scan Biodata"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan biodata berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_biodata')
                    ->label('Preview Scan Biodata')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_biodata
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_biodata) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make("scan_ktp")
                    ->label(__("Scan KTP"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan KTP berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_ktp')
                    ->label('Preview Scan KTP')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_ktp
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_ktp) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make("scan_kk")
                    ->label(__("Scan Kartu Keluarga (KK)"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan KK berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_kk')
                    ->label('Preview Scan Kartu Keluarga (KK)')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_kk
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_kk) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make("scan_akta_kelahiran")
                    ->label(__("Scan Akta Kelahiran"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan akta kelahiran berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_akta_kelahiran')
                    ->label('Preview Scan Akta Kelahiran')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_akta_kelahiran
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_akta_kelahiran) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make("scan_ijazah")
                    ->label(__("Scan Ijazah"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan ijazah berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_ijazah')
                    ->label('Preview Scan Ijazah')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_ijazah
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_ijazah) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make("scan_surat_keterangan_domisili")
                    ->label(__("Scan Surat Keterangan Domisili"))
                    ->acceptedFileTypes(["application/pdf"])
                    ->maxSize(500)
                    ->helperText(new HtmlString("<strong>Petunjuk :</strong> Unggah scan surat keterangan domisili berukuran maksimal 500kb. Hanya file PDF."))
                    ->moveFiles(),

                Forms\Components\Placeholder::make('preview_scan_surat_keterangan_domisili')
                    ->label('Preview Scan Surat Keterangan Domisili')
                    ->dehydrated(false)
                    ->content(fn ($record) => $record?->scan_surat_keterangan_domisili
                        ? new HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_surat_keterangan_domisili) . '" style="width:100%;height:420px;border:1px solid #d1d5db;border-radius:8px;" allowfullscreen></iframe>')
                        : 'Belum ada file yang diunggah')
                    ->visible(fn ($record) => $record !== null),

                FileUpload::make('pasfoto')
                    ->label(__('Pas Foto'))
                    ->required()
                    ->image()
                    ->openable()
                    ->maxSize(500)
                    ->validationMessages([
                        'required' => 'File Pas Foto belum diunggah',
                    ])
                    ->moveFiles()
                    ->imageEditor()
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah pas foto berukuran maksimal 500kb.')),
                Hidden::make('user_id')
                    ->default(Auth::id()),
                Hidden::make('tahun_id')
                    ->default(function () {
                        $tahun = Tahun::where('is_active', true)->first();
                        return $tahun ? $tahun->id : null;
                    }),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')
                    ->label(__('NIK'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->limit(15)
                    ->label(__('Nama'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->label(__('Tempat Lahir'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label(__('Tgl Lahir'))
                    ->date('d-m-Y'),
                Tables\Columns\TextColumn::make('cabang.nama_cabang')
                    ->label(__('Cabang'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('pasfoto')
                    ->label(__('Pas Foto')),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->label('Status Verifikasi')
                    ->alignment(Alignment::Center),
            ])
            ->emptyStateHeading('Daftar Peserta Kosong')
            ->emptyStateDescription('Silahkan daftarkan peserta dengan memilih "Tambah Peserta"')
            ->paginated(false)
            ->filters([
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
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label(__('Lihat')),
                Tables\Actions\EditAction::make()
                    ->hidden(fn() => !static::isTahunAktif()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->hidden(fn() => !static::isTahunAktif()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPesertas::route('/'),
            'create' => Pages\CreatePeserta::route('/create'),
            'edit' => Pages\EditPeserta::route('/{record}/edit'),
        ];
    }

    protected static function parseAgeString($ageString)
    {
        preg_match('/(?:(\d+)\s*tahun)?\s*(?:(\d+)\s*bulan)?\s*(?:(\d+)\s*hari)?/', $ageString, $matches);

        return [
            'years' => (int) ($matches[1] ?? 0),
            'months' => (int) ($matches[2] ?? 0),
            'days' => (int) ($matches[3] ?? 0),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->where('utusan_id', Auth::user()->utusan_id);

        $selectedTahunId = session("selected_tahun_id");
        if ($selectedTahunId) {
            $query = $query->where("tahun_id", $selectedTahunId);
        }

        return $query;
    }
}
