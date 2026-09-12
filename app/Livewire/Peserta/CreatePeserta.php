<?php

namespace App\Livewire\Peserta;

use App\Models\Peserta;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Collection;
use App\Models\Cabang;
use Filament\Forms\Get;
use Carbon\Carbon;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Actions\Concerns\InteractsWithActions;

class CreatePeserta extends Component implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    public ?array $data = [];

    public array $disabledFields = [
        'nama' => true,
        'jenis_kelamin' => true,
        'tempat_lahir' => true,
        'tgl_lahir' => true,
        'alamat_ktp' => true,
        'utusan_id' => true,
        'cabang_id' => true,
        'scan_ktp' => true,
    ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // A. DATA DIRI
                Forms\Components\TextInput::make('nik')
                    ->label(__('NIK'))
                    ->required()
                    ->numeric()
                    ->length(16)
                    ->live()
                    ->unique(column: 'nik')
                    ->afterStateUpdated(function ($state, $set) {
                        $pesertaExists = Peserta::where('nik', $state)->exists();

                        if (!$pesertaExists && strlen($state) == 16) {
                            $this->disabledFields = array_fill_keys(array_keys($this->disabledFields), false);
                        } else {
                            $this->disabledFields = array_fill_keys(array_keys($this->disabledFields), true);
                            $this->disabledFields['nik'] = false;
                        }
                    })
                    ->validationMessages([
                        'required' => 'Kolom NIK tidak boleh kosong',
                        'unique' => 'NIK sudah pernah didaftarkan.',
                    ]),

                Forms\Components\TextInput::make('nama')
                    ->label(__('Nama Lengkap'))
                    ->required()
                    ->maxLength(255)
                    ->disabled(fn (Get $get) => $this->disabledFields['nama'])
                    ->validationMessages([
                        'required' => 'Kolom Nama Lengkap tidak boleh kosong',
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
                    ->afterStateUpdated(function ($state, $set) {
                        $set('cabang_id', null);
                    })
                    ->validationMessages([
                        'required' => 'Kolom Jenis Kelamin tidak boleh kosong',
                    ]),

                Forms\Components\TextInput::make('tempat_lahir')
                    ->label(__('Tempat Lahir'))
                    ->required()
                    ->maxLength(50)
                    ->disabled(fn (Get $get) => $this->disabledFields['tempat_lahir'])
                    ->validationMessages([
                        'required' => 'Kolom Tempat Lahir tidak boleh kosong',
                    ]),

                Forms\Components\DatePicker::make('tgl_lahir')
                    ->label(__('Tanggal Lahir'))
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    ->disabled(fn (Get $get) => $this->disabledFields['tgl_lahir'])
                    ->validationMessages([
                        'required' => 'Kolom Tanggal Lahir tidak boleh kosong',
                    ]),

                Forms\Components\Textarea::make('alamat_ktp')
                    ->label(__('Alamat KTP'))
                    ->required()
                    ->maxLength(500)
                    ->disabled(fn (Get $get) => $this->disabledFields['alamat_ktp'])
                    ->validationMessages([
                        'required' => 'Kolom Alamat KTP tidak boleh kosong',
                    ]),

                // B. UPLOAD DOKUMEN
                Forms\Components\Select::make('cabang_id')
                    ->label(__('Cabang yang Diikuti'))
                    ->required()
                    ->relationship('cabang', 'nama_cabang')
                    ->options(function (Get $get) {
                        $jenisKelamin = $get('jenis_kelamin');
                        if (!$jenisKelamin) {
                            return [];
                        }
                        return Cabang::where('gender_cabang', $jenisKelamin)->pluck('nama_cabang', 'id');
                    })
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function ($state, $get, $set) {
                        $cabang = Cabang::find($state);
                        if ($cabang) {
                            $set('jenis_kelamin', $cabang->gender_cabang);
                        }
                    })
                    ->validationMessages([
                        'required' => 'Kolom Cabang yang Diikuti tidak boleh kosong',
                    ]),

                FileUpload::make('scan_surat_mandat')
                    ->label(__('Surat Mandat'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File Surat Mandat belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('scan_biodata')
                    ->label(__('Biodata'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File Biodata belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('scan_ktp')
                    ->label(__('KTP'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File KTP belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('scan_kk')
                    ->label(__('KK'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File KK belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('scan_akta_kelahiran')
                    ->label(__('Akta Kelahiran'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File Akta Kelahiran belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('scan_ijazah')
                    ->label(__('Ijazah'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File Ijazah belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('scan_surat_keterangan_domisili')
                    ->label(__('Surat Keterangan Domisili'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File Surat Keterangan Domisili belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),

                FileUpload::make('pasfoto')
                    ->label(__('Pas Foto'))
                    ->image()
                    ->maxSize(500)
                    ->directory('peserta-documents')
                    ->validationMessages([
                        'required' => 'File Pas Foto belum diunggah',
                    ])
                    ->helperText(new HtmlString('<strong>Petunjuk :</strong> Unggah foto berukuran maksimal 500kb.'))
                    ->moveFiles(),
            ])
            ->statePath('data')
            ->model(Peserta::class)
            ->columns(1)
            ->extraAttributes(['class' => 'max-w-3xl mx-auto my-10 px-8 py-10 bg-gray-100 rounded-lg shadow-lg']);
    }

    public function create(): void
    {
        $data = $this->form->getState();
        $record = Peserta::create($data);
        $this->form->model($record)->saveRelationships();
    }

    public function render(): View
    {
        return view('livewire.peserta.create-peserta');
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
