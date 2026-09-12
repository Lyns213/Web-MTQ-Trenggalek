<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\Tahun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LiveScoreController extends Controller
{
    public static array $config = [
        'tartil' => [
            'model' => \App\Models\NilaiTartil::class,
            'table' => 'nilai_tartils',
            'label' => 'TARTIL',
            'fields' => [
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 40],
                ['key' => 'irama_dan_suara', 'label' => 'IRAMA & SUARA', 'max' => 30],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 30],
            ],
        ],
        'anak' => [
            'model' => \App\Models\NilaiAnak::class,
            'table' => 'nilai_anaks',
            'label' => 'TILAWAH ANAK-ANAK',
            'fields' => [
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 30],
                ['key' => 'lagu', 'label' => 'LAGU', 'max' => 25],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 25],
                ['key' => 'suara', 'label' => 'SUARA', 'max' => 20],
            ],
        ],
        'remaja' => [
            'model' => \App\Models\NilaiRemaja::class,
            'table' => 'nilai_remajas',
            'label' => 'TILAWAH REMAJA',
            'fields' => [
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 30],
                ['key' => 'lagu', 'label' => 'LAGU', 'max' => 25],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 25],
                ['key' => 'suara', 'label' => 'SUARA', 'max' => 20],
            ],
        ],
        'dewasa' => [
            'model' => \App\Models\NilaiDewasa::class,
            'table' => 'nilai_dewasas',
            'label' => 'TILAWAH DEWASA',
            'fields' => [
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 30],
                ['key' => 'lagu', 'label' => 'LAGU', 'max' => 25],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 25],
                ['key' => 'suara', 'label' => 'SUARA', 'max' => 20],
            ],
        ],
        'satujuz' => [
            'model' => \App\Models\NilaiSatuJuz::class,
            'table' => 'nilai_satu_juzs',
            'label' => 'MHQ 1 JUZ & TILAWAH',
            'fields' => [
                ['key' => 'total_tilawah', 'label' => 'TILAWAH', 'max' => 50],
                ['key' => 'total_tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
            ],
        ],
        'limajuz' => [
            'model' => \App\Models\NilaiLimaJuz::class,
            'table' => 'nilai_lima_juzs',
            'label' => 'MHQ 5 JUZ & TILAWAH',
            'fields' => [
                ['key' => 'total_tilawah', 'label' => 'TILAWAH', 'max' => 50],
                ['key' => 'total_tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
            ],
        ],
        'sepuluhjuz' => [
            'model' => \App\Models\NilaiSepuluhJuz::class,
            'table' => 'nilai_sepuluh_juzs',
            'label' => 'MHQ 10 JUZ',
            'fields' => [
                ['key' => 'tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 25],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 25],
            ],
        ],
        'duapuluhjuz' => [
            'model' => \App\Models\NilaiDuapuluhJuz::class,
            'table' => 'nilai_duapuluh_juzs',
            'label' => 'MHQ 20 JUZ',
            'fields' => [
                ['key' => 'tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 25],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 25],
            ],
        ],
        'tigapuluhjuz' => [
            'model' => \App\Models\NilaiTigapuluhJuz::class,
            'table' => 'nilai_tigapuluh_juzs',
            'label' => 'MHQ 30 JUZ',
            'fields' => [
                ['key' => 'tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
                ['key' => 'tajwid', 'label' => 'TAJWID', 'max' => 25],
                ['key' => 'fashahah', 'label' => 'FASAHAH', 'max' => 25],
            ],
        ],
        'mfq' => [
            'model' => \App\Models\NilaiMfq::class,
            'table' => 'nilai_mfqs',
            'label' => 'MFQ',
            'is_grup' => true,
            'fields' => [
                ['key' => 'total', 'label' => 'TOTAL NILAI', 'max' => 100],
            ],
        ],
        'msq' => [
            'model' => \App\Models\NilaiMsq::class,
            'table' => 'nilai_msqs',
            'label' => 'MSQ',
            'fields' => [
                ['key' => 'terjemahan_dan_materi', 'label' => 'TERJEMAH & MATERI', 'max' => 40],
                ['key' => 'penghayatan_dan_retorika', 'label' => 'PENGHAYATAN & RETORIKA', 'max' => 30],
                ['key' => 'tilawah', 'label' => 'TILAWAH', 'max' => 30],
            ],
        ],
        'mmq' => [
            'model' => \App\Models\NilaiMmq::class,
            'table' => 'nilai_mmqs',
            'label' => 'MMQ',
            'fields' => [
                ['key' => 'bobot_materi', 'label' => 'BOBOT MATERI', 'max' => 40],
                ['key' => 'kaidah_dan_gaya_bahasa', 'label' => 'KAIDAH & GAYA BAHASA', 'max' => 25],
                ['key' => 'logika_dan_organisasi_pesan', 'label' => 'LOGIKA & ORGANISASI', 'max' => 20],
                ['key' => 'presentasi', 'label' => 'PRESENTASI', 'max' => 15],
            ],
        ],
        'naskah' => [
            'model' => \App\Models\NilaiNaskah::class,
            'table' => 'nilai_naskahs',
            'label' => 'MKQ NASKAH',
            'fields' => [
                ['key' => 'kebenaran_kaidah_khat_wajib', 'label' => 'KAIDAH WAJIB', 'max' => 35],
                ['key' => 'keindahan_khat_wajib', 'label' => 'KEINDAHAN WAJIB', 'max' => 25],
                ['key' => 'kebenaran_kaidah_khat_pilihan', 'label' => 'KAIDAH PILIHAN', 'max' => 25],
                ['key' => 'keindahan_khat_pilihan', 'label' => 'KEINDAHAN PILIHAN', 'max' => 15],
            ],
        ],
        'mushaf' => [
            'model' => \App\Models\NilaiMushaf::class,
            'table' => 'nilai_mushafs',
            'label' => 'MKQ HIASAN MUSHAF',
            'fields' => [
                ['key' => 'kebenaran_kaidah_khat', 'label' => 'KAIDAH KHAT', 'max' => 45],
                ['key' => 'keindahan_khat', 'label' => 'KEINDAHAN KHAT', 'max' => 35],
                ['key' => 'keindahan_hiasan_dan_lukisan', 'label' => 'HIASAN & LUKISAN', 'max' => 20],
            ],
        ],
        'dekorasi' => [
            'model' => \App\Models\NilaiDekorasi::class,
            'table' => 'nilai_dekorasis',
            'label' => 'MKQ DEKORASI',
            'fields' => [
                ['key' => 'kebenaran_kaidah_khath', 'label' => 'KAIDAH KHAT', 'max' => 45],
                ['key' => 'keindahan_khath', 'label' => 'KEINDAHAN KHAT', 'max' => 35],
                ['key' => 'keindahan_hiasan_dan_lukisan', 'label' => 'HIASAN & LUKISAN', 'max' => 20],
            ],
        ],
        'kontemporer' => [
            'model' => \App\Models\NilaiKontemporer::class,
            'table' => 'nilai_kontemporers',
            'label' => 'MKQ KONTEMPORER',
            'fields' => [
                ['key' => 'unsur_kaligrafi', 'label' => 'UNSUR KALIGRAFI', 'max' => 40],
                ['key' => 'unsur_seni_rupa', 'label' => 'UNSUR SENI RUPA', 'max' => 35],
                ['key' => 'sentuhan_akhir', 'label' => 'SENTUHAN AKHIR', 'max' => 25],
            ],
        ],
    ];

    public function index($slug = 'tartil', $id = null)
    {
        $slug = strtolower($slug);
        if (!isset(self::$config[$slug])) {
            $slug = 'tartil';
        }

        $dataResponse = $this->getData($slug, $id);
        $initialData = $dataResponse->getData(true);

        return view('filament.penilaian.tartil-live', [
            'slug' => $slug,
            'activeId' => $id,
            'initialData' => $initialData,
        ]);
    }

    private function getRecords(string $slug)
    {
        $cfg = self::$config[$slug] ?? self::$config['tartil'];
        $model = $cfg['model'];
        $table = $cfg['table'];

        $activeTahun = Tahun::where('is_active', true)->first();
        $selectedTahunId = session('selected_tahun_id', $activeTahun?->id);

        if (!empty($cfg['is_grup'])) {
            $query = $model::with(['grup.utusan', 'grup.peserta'])
                ->join('grups', "{$table}.grup_id", '=', 'grups.id');

            if ($selectedTahunId) {
                $query->where('grups.tahun_id', $selectedTahunId);
            }

            return $query->orderBy('grups.nama', 'asc')->select("{$table}.*")->get();
        }

        $query = $model::with(['peserta.utusan', 'peserta.cabang'])
            ->join('pesertas', "{$table}.peserta_id", '=', 'pesertas.id');

        if ($selectedTahunId) {
            $query->where('pesertas.tahun_id', $selectedTahunId);
        }

        return $query
            ->orderByRaw("CASE WHEN pesertas.no_peserta IS NOT NULL AND LENGTH(TRIM(pesertas.no_peserta)) > 0 THEN 0 ELSE 1 END, pesertas.no_peserta ASC, {$table}.id ASC")
            ->select("{$table}.*")
            ->get();
    }

    public function getData($slug = 'tartil', $id = null)
    {
        $slug = strtolower($slug);
        $cfg = self::$config[$slug] ?? self::$config['tartil'];
        $records = $this->getRecords($slug);

        $liveActiveId = Cache::get('mtq_live_active_' . $slug);
        if ($liveActiveId && !$id) {
            $id = $liveActiveId;
        }

        if (!$id) {
            $currentRecord = $records->first();
        } else {
            $currentRecord = $records->firstWhere('id', (int)$id);
            if (!$currentRecord) {
                $model = $cfg['model'];
                if (!empty($cfg['is_grup'])) {
                    $currentRecord = $model::with(['grup.utusan', 'grup.peserta'])->where('id', $id)->first();
                } else {
                    $currentRecord = $model::with(['peserta.utusan', 'peserta.cabang'])->where('id', $id)->first();
                }
            }
            if (!$currentRecord) {
                $currentRecord = $records->first();
            }
        }

        $isGrup = !empty($cfg['is_grup']);
        if (!$currentRecord || (!$isGrup && !$currentRecord->peserta) || ($isGrup && !$currentRecord->grup)) {
            return response()->json(['empty' => true, 'total' => $records->count()]);
        }

        $currentIndex = $records->search(fn ($r) => $r->id === $currentRecord->id);
        $nextRecord = ($currentIndex !== false && $currentIndex + 1 < $records->count()) ? $records->get($currentIndex + 1) : null;
        $previousRecord = ($currentIndex !== false && $currentIndex - 1 >= 0) ? $records->get($currentIndex - 1) : null;

        if ($isGrup) {
            $cabang = $currentRecord->grup->peserta?->first()?->cabang;
            $namaPeserta = $currentRecord->grup->nama;
            $noPeserta = 'GRUP';
            $tempatLahir = $currentRecord->grup->utusan->kecamatan ?? '-';
            $kecamatan = $currentRecord->grup->utusan->kecamatan ?? 'TRENGGALEK';
            $pasfotoUrl = null;
        } else {
            $cabang = $currentRecord->peserta->cabang;
            $namaPeserta = $currentRecord->peserta->nama;
            $noPeserta = $currentRecord->peserta->no_peserta ?? '-';
            $tempatLahir = $currentRecord->peserta->tempat_lahir;
            $kecamatan = $currentRecord->peserta->utusan->kecamatan ?? $currentRecord->peserta->tempat_lahir ?? 'TRENGGALEK';
            $pasfotoUrl = $currentRecord->peserta->pasfoto ? asset('storage/' . $currentRecord->peserta->pasfoto) : null;
        }

        $totalTimer = $cabang && $cabang->timer ? $cabang->timer : '00:05:00';
        $parts = explode(':', $totalTimer);
        if (count($parts) === 3) {
            $totalSeconds = ((int)$parts[0] * 3600) + ((int)$parts[1] * 60) + (int)$parts[2];
        } elseif (count($parts) === 2) {
            $totalSeconds = ((int)$parts[0] * 60) + (int)$parts[1];
        } else {
            $totalSeconds = 300;
        }

        $cacheKey = 'mtq_timer_' . $slug . '_' . $currentRecord->id;
        $timerState = Cache::get($cacheKey);

        if (!$timerState) {
            $timerState = [
                'total_seconds' => $totalSeconds,
                'remaining_seconds' => $totalSeconds,
                'is_running' => false,
                'started_at' => null,
            ];
            Cache::put($cacheKey, $timerState, 3600);
        }

        if ($timerState['is_running'] && $timerState['started_at']) {
            $elapsed = time() - $timerState['started_at'];
            $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
            $timerState['started_at'] = time();
            Cache::put($cacheKey, $timerState, 3600);
        }

        $remaining = $timerState['remaining_seconds'];
        $m = floor($remaining / 60);
        $s = $remaining % 60;
        $timerFormatted = sprintf('%02d:%02d', $m, $s);

        $cabangNama = $cabang ? $cabang->nama_cabang : $cfg['label'];

        $fieldsData = [];
        foreach ($cfg['fields'] as $f) {
            $val = (float)($currentRecord->{$f['key']} ?? 0);
            $max = $f['max'] ?? 100;
            $pct = min(100, max(0, ($val / $max) * 100));
            $fieldsData[] = [
                'key' => $f['key'],
                'label' => $f['label'],
                'value' => $val,
                'max' => $max,
                'pct' => $pct,
            ];
        }

        $totalVal = (float)($currentRecord->total ?? 0);

        return response()->json([
            'empty' => false,
            'current' => [
                'id' => $currentRecord->id,
                'nama' => $namaPeserta,
                'no_peserta' => $noPeserta,
                'tempat_lahir' => $tempatLahir,
                'kecamatan' => strtoupper($kecamatan),
                'cabang' => strtoupper($cabangNama),
                'pasfoto' => $pasfotoUrl,
                'fields' => $fieldsData,
                'total' => $totalVal,
            ],
            'next' => $nextRecord ? [
                'id' => $nextRecord->id,
                'nama' => $isGrup ? $nextRecord->grup->nama : $nextRecord->peserta->nama,
                'no_peserta' => $isGrup ? 'GRUP' : ($nextRecord->peserta->no_peserta ?? '-'),
            ] : null,
            'previous' => $previousRecord ? [
                'id' => $previousRecord->id,
                'nama' => $isGrup ? $previousRecord->grup->nama : $previousRecord->peserta->nama,
                'no_peserta' => $isGrup ? 'GRUP' : ($previousRecord->peserta->no_peserta ?? '-'),
            ] : null,
            'timer' => [
                'formatted' => $timerFormatted,
                'remaining' => $remaining,
                'total' => $timerState['total_seconds'],
                'is_running' => $timerState['is_running'],
            ],
            'total_peserta' => $records->count(),
        ]);
    }

    public function controlTimer(Request $request, $slug = 'tartil', $action = 'start')
    {
        $id = $request->input('id');
        if (!$id) return response()->json(['error' => 'No ID'], 400);

        $slug = strtolower($slug);
        $cacheKey = 'mtq_timer_' . $slug . '_' . $id;
        $timerState = Cache::get($cacheKey);

        if (!$timerState) {
            return response()->json(['error' => 'Timer not initialized'], 404);
        }

        if ($action === 'start') {
            if ($timerState['remaining_seconds'] > 0) {
                $timerState['is_running'] = true;
                $timerState['started_at'] = time();
                Cache::put($cacheKey, $timerState, 3600);
            }
        } elseif ($action === 'pause') {
            if ($timerState['is_running'] && $timerState['started_at']) {
                $elapsed = time() - $timerState['started_at'];
                $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
            }
            $timerState['is_running'] = false;
            $timerState['started_at'] = null;
            Cache::put($cacheKey, $timerState, 3600);
        } elseif ($action === 'reset') {
            $timerState['is_running'] = false;
            $timerState['started_at'] = null;
            $timerState['remaining_seconds'] = $timerState['total_seconds'];
            Cache::put($cacheKey, $timerState, 3600);
        }

        return response()->json(['success' => true]);
    }
}
