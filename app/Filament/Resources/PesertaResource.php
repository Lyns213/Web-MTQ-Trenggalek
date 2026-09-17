<?php

namespace App\Filament\Resources;

use Closure;
use Carbon\Carbon;
use Filament\Forms;
use App\Models\Grup;
use Filament\Tables;
use App\Models\Tahun;
use App\Models\Cabang;
use App\Models\Peserta;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Label;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Actions\CreateAction;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Hidden;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Support\Enums\VerticalAlignment;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\PesertaResource\Pages;
use Filament\Infolists\Components\Actions\Action;
use Filament\Notifications\Livewire\Notifications;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use App\Filament\Resources\PesertaResource\RelationManagers;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;

class PesertaResource extends Resource
{
    protected static ?string $model = Peserta::class;

    protected static ?int $navigationSort = 51;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'Jumlah Pendaftar';

    protected static ?string $navigationGroup = 'Pendaftaran Peserta';

    public static function getNavigationBadge(): ?string
    {
        $selectedTahunId = session("selected_tahun_id", \App\Models\Tahun::where("is_active", true)->first()?->id);
        return static::getModel()::where("tahun_id", $selectedTahunId)->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')
                    ->label(__('NIK'))
                    ->validationAttribute('NIK')
                    // ->required()
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
                            // Validasi panjang NIK
                            if (strlen($value) !== 16) {
                                $fail("NIK harus terdiri dari 16 digit.");
                            }

                            $tahunId = $get('tahun_id');
                            if (!$tahunId) {
                                $fail('Tahun belum dipilih.');
                            }

                            // Cek duplikat NIK HANYA saat bentuk TAMBAH ($record null).
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
                        // Memastikan panjang state maksimal 16 digit
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
                            // Saat EDIT langsung simpan tanpa cek NIK
                            if ($record) {
                                return;
                            }

                            $tahunId = $get('tahun_id');
                            if (!$tahunId) {
                                Notification::make()
                                    ->title(__('Tahun belum dipilih'))
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Periksa apakah ada peserta dengan NIK dan tahun_id yang sama
                            $pesertaExists = Peserta::where('nik', $state)
                                ->where('tahun_id', $tahunId)
                                ->exists();

                            if ($pesertaExists) {
                                // NIK sudah terdaftar untuk tahun yang sama
                                Notification::make()
                                    ->title(__('NIK Sudah Terdaftar'))
                                    ->danger()
                                    ->body('NIK sudah pernah didaftarkan, periksa kembali')
                                    ->send();
                            } else {
                                // NIK valid
                                Notification::make()
                                    ->title(__('NIK Valid'))
                                    ->success()
                                    ->send();
                            }
                        }
                    }),

                Forms\Components\TextInput::make('nama')
                    ->label(__('Nama Lengkap'))
                    // ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'Kolom Nama tidak boleh kosong',
                    ]),
                Forms\Components\Select::make('jenis_kelamin')
                    ->label(__('Jenis Kelamin'))
                    // ->required()
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
                    // ->required()
                    ->maxLength(50)
                    ->validationMessages([
                        'required' => 'Kolom Tempat Lahir tidak boleh kosong',
                    ]),
                Forms\Components\DatePicker::make('tgl_lahir')
                    ->label(__('Tanggal Lahir'))
                    // ->required()
                    ->native(false)
                    ->closeOnDateSelection()
                    ->displayFormat('d-m-Y')
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        // Kosongkan field cabang_id jika tanggal lahir diubah
                        $set('cabang_id', null);
                    })
                    ->validationMessages([
                        'required' => 'Kolom Tanggal Lahir tidak boleh kosong',
                    ]),
                Forms\Components\Textarea::make('alamat_ktp')
                    ->label(__('Alamat KTP'))
                    // ->required()
                    ->maxLength(500)
                    ->validationMessages([
                        'required' => 'Kolom Alamat KTP tidak boleh kosong',
                    ]),
                Forms\Components\Textarea::make('alamat_domisili')
                    ->label(__('Alamat Domisili'))
                    // ->required()
                    ->maxLength(500)
                    ->validationMessages([
                        'required' => 'Kolom Alamat KTP tidak boleh kosong',
                    ]),
                Forms\Components\Select::make('utusan_id')
                    ->label(__('Utusan Kecamatan'))
                    // ->required()
                    ->relationship('utusan', 'kecamatan')
                    ->native(false)
                    ->live() // Menambahkan live update
                    ->afterStateUpdated(function ($state, $set, $get) {
                        $set('cabang_id', null); // Kosongkan field cabang_id saat utusan_id berubah
                    })
                    ->validationMessages([
                        'required' => 'Kolom Utusan Kecamatan tidak boleh kosong',
                    ]),
                Forms\Components\Select::make('cabang_id')
                    ->label(__('Cabang yang Diikuti'))
                    ->preload()
                    ->relationship('cabang', 'nama_cabang')
                    // ->required()
                    ->live()
                    ->options(function (Get $get) {
                        $utusanId = $get('utusan_id');
                        $jenisKelamin = $get('jenis_kelamin');
                        $tahunId = $get('tahun_id');
                        $tglLahir = $get('tgl_lahir');

                        if (!$utusanId || !$jenisKelamin || !$tahunId) {
                            return [];
                        }

                        return Cabang::query()
                            ->where('gender_cabang', $jenisKelamin)
                            ->get()
                            ->filter(function ($cabang) use ($utusanId, $tahunId, $tglLahir) {
                                // Filter by kuota
                                $kuota = $cabang->kuota;
                                $jumlahPeserta = Peserta::where('utusan_id', $utusanId)
                                    ->where('tahun_id', $tahunId)
                                    ->where('cabang_id', $cabang->id)
                                    ->count();
                                if ($jumlahPeserta >= $kuota) {
                                    return false;
                                }

                                // Filter by usia jika tgl_lahir sudah diisi
                                if ($tglLahir) {
                                    $perTanggal = Carbon::parse($cabang->per_tanggal);
                                    $diff = Carbon::parse($tglLahir)->diff($perTanggal);
                                    $usiaTahun = $diff->y;
                                    $usiaBulan = $diff->m;
                                    $usiaHari = $diff->d;

                                    $batasUmur = self::parseAgeString($cabang->batas_umur);
                                    $maxTahun = $batasUmur['years'];
                                    $maxBulan = $batasUmur['months'];
                                    $maxHari = $batasUmur['days'];

                                    if ($usiaTahun > $maxTahun) {
                                        return false;
                                    } elseif ($usiaTahun == $maxTahun && $usiaBulan > $maxBulan) {
                                        return false;
                                    } elseif ($usiaTahun == $maxTahun && $usiaBulan == $maxBulan && $usiaHari > $maxHari) {
                                        return false;
                                    }
                                }

                                return true;
                            })
                            ->pluck('nama_cabang', 'id');
                    })
                    ->native(false)
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $birthdate = $get('tgl_lahir');
                        if (!$birthdate) {
                            // Tanggal lahir belum dipilih, reset cabang_id dan beri notifikasi
                            $set('cabang_id', null);
                            Notification::make()
                                ->title(__('Silakan mengisi tanggal lahir terlebih dahulu sebelum memilih cabang.'))
                                ->danger()
                                ->send();
                            return;
                        }

                        $cabang = Cabang::find($state);
                        if ($cabang) {
                            $perTanggal = Carbon::parse($cabang->per_tanggal);
                            $ageInDays = Carbon::parse($birthdate)->diffInDays($perTanggal);

                            // Parse batas_umur (e.g., '10 tahun 11 bulan 29 hari')
                            $batasUmur = self::parseAgeString($cabang->batas_umur);
                            $maxAgeDate = $perTanggal->copy()
                                ->subYears($batasUmur['years'])
                                ->subMonths($batasUmur['months'])
                                ->subDays($batasUmur['days']);
                            $maxAgeInDays = $maxAgeDate->diffInDays($perTanggal);

                            if ($ageInDays > $maxAgeInDays + 1) {
                                $set('cabang_id', null);
                                Notification::make()
                                    ->title(__('Usia peserta melebihi batas maksimal untuk cabang ini.'))
                                    ->danger()
                                    ->send();
                            }
                        }
                    })
                    ->reactive()
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
                        // Mengambil id tahun yang is_active bernilai true
                        $tahun = Tahun::where('is_active', true)->first();
                        return $tahun ? $tahun->id : null;
                    }),
                // TextInput::make('grup_id')
                //    ->default(function (Get $get ) {
                //         if($get('cabang_id') == 19 || $get('cabang_id') == 20){
                //             $grup = Grup::where('tahun_id', $get('tahun_id'))->where('utusan_id', $get('utusan_id'))->where('jenis_kelamin', $get('jenis_kelamin'))->first();
                //             return $grup ? $grup->id : null;
                //         }
                //     })
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('index')
                //     ->label('No')
                //     ->rowIndex()
                //     ->toggleable(),
                TextInputColumn::make('no_peserta')
                    ->label(__('NoPes'))
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label(__('NIK'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nama')
                    ->limit(15)
                    ->label(__('Nama'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label(__('L/P'))
                    ->toggleable()
                    ->formatStateUsing(fn (Peserta $record): string => $record->jenis_kelamin == 'putra' ? 'L' : 'P'),
                Tables\Columns\TextColumn::make('tempat_lahir')
                    ->label(__('Tempat Lahir'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tgl_lahir')
                    ->label(__('Tgl Lahir'))
                    ->date('d-m-Y')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('alamat_ktp')
                    ->limit(20)
                    ->label(__('Alamat KTP'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('utusan.kecamatan')
                    ->label(__('Utusan'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('cabang.nama_cabang')
                    ->label(__('Cabang'))
                    ->searchable()
                    ->toggleable(),
                IconColumn::make('is_verified')
                    ->label(__('Verifikasi'))
                    ->size(IconColumn\IconColumnSize::ExtraLarge)
                    ->boolean()
                    ->action(function ($record, $column) {
                        $name = $column->getName();
                        $isVerified = !$record->$name;

                        // Jika verifikasi berhasil
                        if ($isVerified) {
                            if (in_array($record->cabang_id, [1, 2])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiTartil::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [3, 4])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiAnak::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [5, 6])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiRemaja::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [7, 8])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiDewasa::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [9, 10])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiSatuJuz::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [11, 12])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiLimaJuz::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [13, 14])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiSepuluhJuz::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [15, 16])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiDuapuluhJuz::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [17, 18])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiTigapuluhJuz::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [19, 20])) {
                                // Cek apakah data dengan grup_id ini sudah ada di tabel nilai_mfq
                                $nilaiMfqExists = \App\Models\NilaiMfq::where('grup_id', $record->grup_id)->exists();

                                if (!$nilaiMfqExists) {
                                    // Jika belum ada, buat data baru
                                    \App\Models\NilaiMfq::create([
                                        'grup_id' => $record->grup_id,
                                    ]);

                                    Notification::make()
                                        ->title('Verifikasi Berhasil')
                                        ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                        ->success()
                                        ->send();
                                } else {
                                    // Jika sudah ada, tampilkan notifikasi tanpa membuat data baru
                                    Notification::make()
                                        ->title('Verifikasi Berhasil')
                                        ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                        ->success()
                                        ->send();
                                }
                            }

                            if (in_array($record->cabang_id, [21, 22])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiMsq::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [23, 24])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiNaskah::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [25, 26])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiMushaf::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [27, 28])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiDekorasi::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [29, 30])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiKontemporer::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                            if (in_array($record->cabang_id, [31, 32])) {
                                // Tambahkan data baru di tabel nilai_tartils
                                \App\Models\NilaiMmq::create([
                                    'peserta_id' => $record->id,
                                ]);
                                Notification::make()
                                    ->title('Verifikasi Berhasil')
                                    ->body('Peserta ' . $record->nama . ' berhasil diverifikasi')
                                    ->success()
                                    ->send();
                            }
                        } else {
                            // Jika membatalkan verifikasi, pastikan total null atau 0
                            $nilaiTartil = \App\Models\NilaiTartil::where('peserta_id', $record->id)->first();
                            $nilaiAnak = \App\Models\NilaiAnak::where('peserta_id', $record->id)->first();
                            $nilaiRemaja = \App\Models\NilaiRemaja::where('peserta_id', $record->id)->first();
                            $nilaiDewasa = \App\Models\NilaiDewasa::where('peserta_id', $record->id)->first();
                            $nilaiSatuJuz = \App\Models\NilaiSatuJuz::where('peserta_id', $record->id)->first();
                            $nilaiLimaJuz = \App\Models\NilaiLimaJuz::where('peserta_id', $record->id)->first();
                            $nilaiSepuluhJuz = \App\Models\NilaiSepuluhJuz::where('peserta_id', $record->id)->first();
                            $nilaiDuapuluhJuz = \App\Models\NilaiDuapuluhJuz::where('peserta_id', $record->id)->first();
                            $nilaiTigapuluhJuz = \App\Models\NilaiTigapuluhJuz::where('peserta_id', $record->id)->first();
                            $nilaiMfq = \App\Models\NilaiMfq::where('grup_id', $record->grup_id)->first();
                            $nilaiMfqExists = \App\Models\NilaiMfq::where('grup_id', $record->grup_id)->exists();
                            $nilaiMsq = \App\Models\NilaiMsq::where('peserta_id', $record->id)->first();
                            $nilaiNaskah = \App\Models\NilaiNaskah::where('peserta_id', $record->id)->first();
                            $nilaiMushaf = \App\Models\NilaiMushaf::where('peserta_id', $record->id)->first();
                            $nilaiDekorasi = \App\Models\NilaiDekorasi::where('peserta_id', $record->id)->first();
                            $nilaiKontemporer = \App\Models\NilaiKontemporer::where('peserta_id', $record->id)->first();
                            $nilaiMmq = \App\Models\NilaiMmq::where('peserta_id', $record->id)->first();

                            if ($nilaiTartil && ($nilaiTartil->total === null || $nilaiTartil->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiTartil->delete();
                            } elseif ($nilaiAnak && ($nilaiAnak->total === null || $nilaiAnak->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiAnak->delete();
                            } elseif ($nilaiRemaja && ($nilaiRemaja->total === null || $nilaiRemaja->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiRemaja->delete();
                            } elseif ($nilaiDewasa && ($nilaiDewasa->total === null || $nilaiDewasa->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiDewasa->delete();
                            } elseif ($nilaiSatuJuz && ($nilaiSatuJuz->total === null || $nilaiSatuJuz->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiSatuJuz->delete();
                            } elseif ($nilaiLimaJuz && ($nilaiLimaJuz->total === null || $nilaiLimaJuz->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiLimaJuz->delete();
                            } elseif ($nilaiSepuluhJuz && ($nilaiSepuluhJuz->total === null || $nilaiSepuluhJuz->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiSepuluhJuz->delete();
                            } elseif ($nilaiDuapuluhJuz && ($nilaiDuapuluhJuz->total === null || $nilaiDuapuluhJuz->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiDuapuluhJuz->delete();
                            } elseif ($nilaiTigapuluhJuz && ($nilaiTigapuluhJuz->total === null || $nilaiTigapuluhJuz->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiTigapuluhJuz->delete();
                            } elseif (!$nilaiMfqExists) {
                                // $record->update([$name => $isVerified]);
                            }
                            elseif ($nilaiMfq && ($nilaiMfq->total === null || $nilaiMfq->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiMfq->delete();
                            } elseif ($nilaiMsq && ($nilaiMsq->total === null || $nilaiMsq->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiMsq->delete();
                            } elseif ($nilaiNaskah && ($nilaiNaskah->total === null || $nilaiNaskah->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiNaskah->delete();
                            } elseif ($nilaiMushaf && ($nilaiMushaf->total === null || $nilaiMushaf->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiMushaf->delete();
                            } elseif ($nilaiDekorasi && ($nilaiDekorasi->total === null || $nilaiDekorasi->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiDekorasi->delete();
                            } elseif ($nilaiKontemporer && ($nilaiKontemporer->total === null || $nilaiKontemporer->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiKontemporer->delete();
                            } elseif ($nilaiMmq && ($nilaiMmq->total === null || $nilaiMmq->total == 0)) {
                                // Hapus data jika memenuhi syarat
                                $nilaiMmq->delete();
                            } else {
                                // Mungkin tambahkan notifikasi bahwa penghapusan gagal
                                // return back()->with('error', 'Gagal membatalkan verifikasi: total tidak null atau tidak 0.');
                                return Notification::make()
                                    ->title('Pembatalan Verifikasi Gagal')
                                    ->body('Silahkan kosongkan terlebih dahulu nilai dari peserta ' . $record->nama)
                                    ->danger()
                                    ->send();
                            }
                            Notification::make()
                                ->title('Pembatalan Verifikasi Berhasil')
                                ->body('Pembatalan verifikasi peserta ' . $record->nama . ' berhasil dilakukan')
                                ->success()
                                ->send();
                        }

                        // Update status verifikasi
                        $record->update([$name => $isVerified]);
                    })
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('pasfoto'),
            ])
            ->filters([
                SelectFilter::make('jenis_kelamin')
                    ->label(__('Jenis Kelamin'))
                    ->options([
                        'putra' => 'Laki-laki',
                        'putri' => 'Perempuan',
                    ])
                    ->native(false),
                SelectFilter::make('utusan.kecamatan')
                    ->relationship('utusan', 'kecamatan')
                    ->label(__('Kecamatan'))
                    ->native(false),
                SelectFilter::make('cabang_merged')
                    ->label('Cabang')
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
                    ->label(__('Kategori'))
                    ->native(false),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__('Download Excel'))
                    ->color('success')
                    ->exports([
                        ExcelExport::make()->fromTable()->except([
                            'index',
                        ]),
                    ]),
                Tables\Actions\CreateAction::make()
                    ->label(__('Tambah Peserta'))
                    ->icon('heroicon-o-user-plus'),
            ])
            ->actions([
                Tables\Actions\Action::make('cetak')
                    ->label('PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->url(fn (Peserta $record): string => route('admin.cetak-kartu.peserta.single', $record->id)),
                Tables\Actions\ViewAction::make()
                    ->label(__('Lihat'))
                    ->modalWidth('Screen')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel(__('Tutup')),
                Tables\Actions\EditAction::make()
                    ->label(__('Edit')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('cetak_massal')
                        ->label('Download PDF')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('warning')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, \Livewire\Component $livewire): void {
                            $ids = $records->pluck('id')->join(',');
                            $url = route('admin.cetak-kartu.peserta.bulk', ['ids' => $ids]);
                            $livewire->js("window.location.href = '{$url}'");
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function getEloquentQuery(): Builder
    {
        $selectedTahunId = session("selected_tahun_id", \App\Models\Tahun::where("is_active", true)->first()?->id);
        return parent::getEloquentQuery()->where("tahun_id", $selectedTahunId);
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                // ═══════════════════════════════════════════════════════════
                //  HEADER — Avatar · Identitas · Status Verifikasi
                // ═══════════════════════════════════════════════════════════
                \Filament\Infolists\Components\Grid::make(12)
                    ->schema([

                        // ── Avatar ──
                        \Filament\Infolists\Components\ImageEntry::make('pasfoto')
                            ->label('')
                            ->disk('public')
                            ->circular()
                            ->width(120)
                            ->height(120)
                            ->columnSpan(2),

                        // ── Nama, No Peserta, JK ──
                        \Filament\Infolists\Components\Grid::make(1)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('nama')
                                    ->label('')
                                    ->size('xl')
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                                \Filament\Infolists\Components\TextEntry::make('no_peserta')
                                    ->label('No. Peserta')
                                    ->icon('heroicon-o-hashtag')
                                    ->size('sm')
                                    ->color('gray'),

                                \Filament\Infolists\Components\TextEntry::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => $state === 'putra' ? 'Laki-laki' : 'Perempuan')
                                    ->color(fn ($state) => $state === 'putra' ? 'info' : 'danger'),
                            ])
                            ->columnSpan(6),

                        // ── Status + Tombol Verifikasi ──
                        \Filament\Infolists\Components\Grid::make(1)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('is_verified')
                                    ->label('Status Verifikasi')
                                    ->badge()
                                    ->size('lg')
                                    ->formatStateUsing(fn ($state) => $state ? 'Terverifikasi' : 'Belum Diverifikasi')
                                    ->color(fn ($state) => $state ? 'success' : 'warning')
                                    ->icon(fn ($state) => $state ? 'heroicon-m-check-badge' : 'heroicon-m-clock'),

                                \Filament\Infolists\Components\Actions::make([
                                    \Filament\Infolists\Components\Actions\Action::make('toggle_verifikasi')
                                        ->label(fn ($record) => $record->is_verified ? 'Batalkan Verifikasi' : 'Verifikasi Peserta')
                                        ->icon(fn ($record) => $record->is_verified ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                                        ->color(fn ($record) => $record->is_verified ? 'danger' : 'success')
                                        ->action(function ($record) {
                                            $record->is_verified = !$record->is_verified;
                                            $record->save();

                                            if ($record->is_verified) {
                                                static::createNilaiRecord($record);
                                            } else {
                                                static::removeNilaiRecord($record);
                                            }

                                            \Filament\Notifications\Notification::make()
                                                ->title($record->is_verified ? 'Peserta Terverifikasi' : 'Verifikasi Dibatalkan')
                                                ->success()
                                                ->send();
                                        }),
                                ]),
                            ])
                            ->columnSpan(4),
                    ]),

                // ═══════════════════════════════════════════════════════════
                //  DATA DIRI
                // ═══════════════════════════════════════════════════════════
                \Filament\Infolists\Components\Section::make('Data Diri')
                    ->icon('heroicon-o-user-circle')
                    ->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(2)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('nik')
                                    ->label('NIK')
                                    ->icon('heroicon-o-identification'),
                                \Filament\Infolists\Components\TextEntry::make('tempat_lahir')
                                    ->label('Tempat Lahir'),
                                \Filament\Infolists\Components\TextEntry::make('tgl_lahir')
                                    ->label('Tanggal Lahir')
                                    ->date('d M Y'),
                                \Filament\Infolists\Components\TextEntry::make('alamat_ktp')
                                    ->label('Alamat (KTP)')
                                    ->columnSpanFull(),
                                \Filament\Infolists\Components\TextEntry::make('alamat_domisili')
                                    ->label('Alamat (Domisili)')
                                    ->columnSpanFull(),
                            ]),
                    ]),

                // ═══════════════════════════════════════════════════════════
                //  KATEGORI PESERTA
                // ═══════════════════════════════════════════════════════════
                \Filament\Infolists\Components\Section::make('Kategori Peserta')
                    ->icon('heroicon-o-tag')
                    ->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(4)
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('utusan.kecamatan')
                                    ->label('Kecamatan')
                                    ->badge()
                                    ->color('primary'),
                                \Filament\Infolists\Components\TextEntry::make('cabang.nama_cabang')
                                    ->label('Cabang')
                                    ->badge()
                                    ->color('info'),
                                \Filament\Infolists\Components\TextEntry::make('grup.nama')
                                    ->label('Grup')
                                    ->badge()
                                    ->color('gray'),
                                \Filament\Infolists\Components\TextEntry::make('tahun.nama_tahun')
                                    ->label('Tahun')
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ]),

                // ═══════════════════════════════════════════════════════════
                //  BERKAS / DOKUMEN — preview inline + toggle verifikasi
                // ═══════════════════════════════════════════════════════════
                \Filament\Infolists\Components\Section::make('Berkas / Dokumen')
                    ->icon('heroicon-o-folder-open')
                    ->collapsible()
                    ->schema([
                        \Filament\Infolists\Components\Grid::make(1)
                            ->schema([

                                // ── Scan KTP ──
                                \Filament\Infolists\Components\Section::make('Scan KTP')
                                    ->icon(fn ($record) => $record->scan_ktp ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_ktp ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_ktp')
                                            ->label(fn ($record) => $record->check_scan_ktp ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_ktp ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_ktp ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_ktp = !$record->check_scan_ktp;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_ktp ? 'Scan KTP diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_ktp_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_ktp
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_ktp) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                                // ── Surat Mandat ──
                                \Filament\Infolists\Components\Section::make('Surat Mandat')
                                    ->icon(fn ($record) => $record->scan_surat_mandat ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_surat_mandat ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_surat_mandat')
                                            ->label(fn ($record) => $record->check_scan_surat_mandat ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_surat_mandat ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_surat_mandat ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_surat_mandat = !$record->check_scan_surat_mandat;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_surat_mandat ? 'Surat Mandat diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_surat_mandat_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_surat_mandat
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_surat_mandat) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                                // ── Scan KK ──
                                \Filament\Infolists\Components\Section::make('Scan KK')
                                    ->icon(fn ($record) => $record->scan_kk ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_kk ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_kk')
                                            ->label(fn ($record) => $record->check_scan_kk ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_kk ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_kk ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_kk = !$record->check_scan_kk;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_kk ? 'Scan KK diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_kk_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_kk
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_kk) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                                // ── Scan Ijazah ──
                                \Filament\Infolists\Components\Section::make('Scan Ijazah')
                                    ->icon(fn ($record) => $record->scan_ijazah ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_ijazah ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_ijazah')
                                            ->label(fn ($record) => $record->check_scan_ijazah ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_ijazah ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_ijazah ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_ijazah = !$record->check_scan_ijazah;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_ijazah ? 'Scan Ijazah diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_ijazah_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_ijazah
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_ijazah) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                                // ── Akta Kelahiran ──
                                \Filament\Infolists\Components\Section::make('Akta Kelahiran')
                                    ->icon(fn ($record) => $record->scan_akta_kelahiran ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_akta_kelahiran ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_akta')
                                            ->label(fn ($record) => $record->check_scan_akta_kelahiran ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_akta_kelahiran ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_akta_kelahiran ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_akta_kelahiran = !$record->check_scan_akta_kelahiran;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_akta_kelahiran ? 'Akta Kelahiran diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_akta_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_akta_kelahiran
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_akta_kelahiran) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                                // ── Scan Biodata ──
                                \Filament\Infolists\Components\Section::make('Scan Biodata')
                                    ->icon(fn ($record) => $record->scan_biodata ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_biodata ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_biodata')
                                            ->label(fn ($record) => $record->check_scan_biodata ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_biodata ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_biodata ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_biodata = !$record->check_scan_biodata;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_biodata ? 'Scan Biodata diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_biodata_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_biodata
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_biodata) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                                // ── Surat Domisili ──
                                \Filament\Infolists\Components\Section::make('Surat Domisili')
                                    ->icon(fn ($record) => $record->scan_surat_keterangan_domisili ? 'heroicon-o-document-check' : 'heroicon-o-x-mark')
                                    ->iconColor(fn ($record) => $record->scan_surat_keterangan_domisili ? 'success' : 'danger')
                                    ->collapsible()
                                    ->collapsed()
                                    ->headerActions([
                                        \Filament\Infolists\Components\Actions\Action::make('check_scan_domisili')
                                            ->label(fn ($record) => $record->check_scan_surat_domisili ? '✓ Diverifikasi' : 'Verifikasi')
                                            ->icon(fn ($record) => $record->check_scan_surat_domisili ? 'heroicon-m-check-circle' : 'heroicon-o-check')
                                            ->color(fn ($record) => $record->check_scan_surat_domisili ? 'success' : 'gray')
                                            ->action(function ($record) {
                                                $record->check_scan_surat_domisili = !$record->check_scan_surat_domisili;
                                                $record->save();
                                                \Filament\Notifications\Notification::make()
                                                    ->title($record->check_scan_surat_domisili ? 'Surat Domisili diverifikasi' : 'Verifikasi dibatalkan')
                                                    ->success()->send();
                                            }),
                                    ])
                                    ->schema([
                                        \App\Filament\Infolists\Components\RawHtmlEntry::make('scan_domisili_viewer')
            ->label('')
            ->state(fn ($record) => $record->scan_surat_keterangan_domisili
                                                ? new \Illuminate\Support\HtmlString('<iframe src="/pdf-viewer.html?file=/storage/' . e($record->scan_surat_keterangan_domisili) . '" style="width:100%;height:85vh;border:none;border-radius:8px;" allowfullscreen></iframe>')
                                                : '<p style="color:#9ca3af;font-style:italic;">Belum ada dokumen yang diunggah</p>'
                                            )
                                    ]),

                            ]),
                    ]),
            ]);
    }
    protected static function createNilaiRecord(Peserta $record): void
    {
        $cabangId = $record->cabang_id;

        $nilaiClasses = [
            [1, 2, \App\Models\NilaiTartil::class],
            [3, 4, \App\Models\NilaiAnak::class],
            [5, 6, \App\Models\NilaiRemaja::class],
            [7, 8, \App\Models\NilaiDewasa::class],
            [9, 10, \App\Models\NilaiSatuJuz::class],
            [11, 12, \App\Models\NilaiLimaJuz::class],
            [13, 14, \App\Models\NilaiSepuluhJuz::class],
            [15, 16, \App\Models\NilaiDuapuluhJuz::class],
            [17, 18, \App\Models\NilaiTigapuluhJuz::class],
        ];

        foreach ($nilaiClasses as [$from, $to, $class]) {
            if ($cabangId >= $from && $cabangId <= $to) {
                $class::create(['peserta_id' => $record->id]);
                break;
            }
        }

        // Special case for cabang MFQ (19, 20)
        if (in_array($cabangId, [19, 20]) && $record->grup_id) {
            $exists = \App\Models\NilaiMfq::where('grup_id', $record->grup_id)->exists();
            if (!$exists) {
                \App\Models\NilaiMfq::create([
                    'peserta_id' => $record->id,
                    'grup_id' => $record->grup_id,
                ]);
            }
        }
    }

    // =========================================================================
    // HELPER METHOD: Remove Nilai Record
    // =========================================================================
    protected static function removeNilaiRecord(Peserta $record): void
    {
        $cabangId = $record->cabang_id;

        $nilaiModels = [
            [1, 2, \App\Models\NilaiTartil::class],
            [3, 4, \App\Models\NilaiAnak::class],
            [5, 6, \App\Models\NilaiRemaja::class],
            [7, 8, \App\Models\NilaiDewasa::class],
            [9, 10, \App\Models\NilaiSatuJuz::class],
            [11, 12, \App\Models\NilaiLimaJuz::class],
            [13, 14, \App\Models\NilaiSepuluhJuz::class],
            [15, 16, \App\Models\NilaiDuapuluhJuz::class],
            [17, 18, \App\Models\NilaiTigapuluhJuz::class],
        ];

        foreach ($nilaiModels as [$from, $to, $class]) {
            if ($cabangId >= $from && $cabangId <= $to) {
                $class::where('peserta_id', $record->id)->delete();
                break;
            }
        }

        if (in_array($cabangId, [19, 20])) {
            \App\Models\NilaiMfq::where('peserta_id', $record->id)->delete();
        }
    }

    public static function isTahunAktif(): bool
    {
        $tahunId = session("selected_tahun_id");
        if (!$tahunId) {
            return false;
        }
        $tahun = Tahun::find($tahunId);
        return $tahun && $tahun->is_active && Carbon::now()->between($tahun->batas_awal, $tahun->batas_akhir);
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
}
