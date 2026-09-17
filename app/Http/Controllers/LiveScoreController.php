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
            'timer' => '00:05:00',
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
            'timer' => '00:06:00',
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
            'timer' => '00:07:00',
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
            'timer' => '00:09:00',
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
            'timer' => '00:06:00',
            'fields' => [
                ['key' => 'total_tilawah', 'label' => 'TILAWAH', 'max' => 50],
                ['key' => 'total_tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
            ],
        ],
        'limajuz' => [
            'model' => \App\Models\NilaiLimaJuz::class,
            'table' => 'nilai_lima_juzs',
            'label' => 'MHQ 5 JUZ & TILAWAH',
            'timer' => '00:07:00',
            'fields' => [
                ['key' => 'total_tilawah', 'label' => 'TILAWAH', 'max' => 50],
                ['key' => 'total_tahfizh', 'label' => 'TAHFIZH', 'max' => 50],
            ],
        ],
        'sepuluhjuz' => [
            'model' => \App\Models\NilaiSepuluhJuz::class,
            'table' => 'nilai_sepuluh_juzs',
            'label' => 'MHQ 10 JUZ',
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
            'is_grup' => true,
            'fields' => [
                ['key' => 'total', 'label' => 'TOTAL NILAI', 'max' => 100],
            ],
        ],
        'msq' => [
            'model' => \App\Models\NilaiMsq::class,
            'table' => 'nilai_msqs',
            'label' => 'MSQ',
            'timer' => '00:20:00',
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
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
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
            'timer' => '00:05:00',
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

        $liveActiveId = Cache::get('mtq_live_active_' . $slug);
        if ($liveActiveId && !$id) {
            $id = $liveActiveId;
        }

        $dataResponse = $this->getData($slug, $id);
        $initialData = $dataResponse->getData(true);
        $cfg = self::$config[$slug] ?? self::$config['tartil'];

        return view('filament.penilaian.tartil-live', [
            'slug' => $slug,
            'cfg' => $cfg,
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
        $selectedTahunId = (request()->hasSession() && session()->has('selected_tahun_id'))
            ? session('selected_tahun_id')
            : $activeTahun?->id;

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

        if ($id) {
            if (request()->has('set_active')) {
                Cache::put('mtq_live_active_' . $slug, (int)$id, 86400);
                $liveActiveId = (int)$id;
            } else {
                $liveActiveId = Cache::get('mtq_live_active_' . $slug);
            }
        } else {
            $liveActiveId = Cache::get('mtq_live_active_' . $slug);
            if ($liveActiveId) {
                $id = $liveActiveId;
            }
        }

        if (!$id) {
            $currentRecord = $records->first();
            if ($currentRecord) {
                Cache::put('mtq_live_active_' . $slug, $currentRecord->id, 86400);
                $liveActiveId = $currentRecord->id;
            }
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
                if ($currentRecord) {
                    Cache::put('mtq_live_active_' . $slug, $currentRecord->id, 86400);
                    $liveActiveId = $currentRecord->id;
                }
            }
        }

        $defaultFields = [];
        foreach ($cfg['fields'] as $f) {
            $defaultFields[] = [
                'key' => $f['key'],
                'label' => $f['label'],
                'value' => 0.0,
                'max' => $f['max'] ?? 100,
                'pct' => 0.0,
            ];
        }

        $extractFields = function($record) use ($cfg) {
            $fieldsData = [];
            foreach ($cfg['fields'] as $f) {
                $val = (float)($record->{$f['key']} ?? 0);
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
            return $fieldsData;
        };

        $isGrup = !empty($cfg['is_grup']);
        $totalFieldsCount = count($cfg['fields']);
        $participants = $records->map(function ($r) use ($isGrup, $cfg, $extractFields, $totalFieldsCount) {
            if ($isGrup) {
                $nama = $r->grup?->nama ?? 'Grup';
                $no = 'GRUP';
                $kec = $r->grup?->utusan?->kecamatan ?? '-';
                $cabang = $r->grup?->peserta?->first()?->cabang?->nama_cabang ?? $cfg['label'];
                $pasfoto = null;
            } else {
                $nama = $r->peserta?->nama ?? '-';
                $no = $r->peserta?->no_peserta ?? '-';
                $kec = $r->peserta?->utusan?->kecamatan ?? $r->peserta?->tempat_lahir ?? '-';
                $cabang = $r->peserta?->cabang?->nama_cabang ?? $cfg['label'];
                $pasfoto = $r->peserta?->pasfoto ? asset('storage/' . $r->peserta->pasfoto) : null;
            }

            $filledFields = [];
            foreach ($cfg['fields'] as $f) {
                $rawVal = $r->{$f['key']} ?? null;
                $val = (float)($rawVal ?? 0);
                if (!is_null($rawVal) && $rawVal !== '' && $val > 0) {
                    $filledFields[] = $f['label'];
                }
            }
            $isComplete = count($filledFields) === $totalFieldsCount && $totalFieldsCount > 0;
            $evaluatedLabel = (!$isComplete && count($filledFields) > 0) ? implode(', ', $filledFields) : '';

            return [
                'id' => $r->id,
                'nama' => $nama,
                'no_peserta' => $no,
                'kecamatan' => strtoupper($kec),
                'cabang' => strtoupper($cabang),
                'pasfoto' => $pasfoto,
                'fields' => $extractFields($r),
                'is_complete' => $isComplete,
                'evaluated_label' => $evaluatedLabel,
                'total' => (float)($r->total ?? 0),
            ];
        })->values()->all();

        if (!$currentRecord || (!$isGrup && !$currentRecord->peserta) || ($isGrup && !$currentRecord->grup)) {
            $defaultTimer = $cfg['timer'] ?? '00:05:00';
            $parts = explode(':', $defaultTimer);
            $totalSecs = count($parts) === 3 ? ((int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2]) : (count($parts) === 2 ? ((int)$parts[0] * 60 + (int)$parts[1]) : 300);
            $m = floor($totalSecs / 60);
            $s = $totalSecs % 60;
            return new \Illuminate\Http\JsonResponse([
                'empty' => true,
                'cabang' => $cfg['label'],
                'fields' => $defaultFields,
                'total' => 0.0,
                'total_peserta' => $records->count(),
                'participants' => $participants,
                'timer' => [
                    'formatted' => sprintf('%02d:%02d', $m, $s),
                    'remaining' => $totalSecs,
                    'total' => $totalSecs,
                    'is_running' => false,
                ],
            ]);
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

        $activeTimerId = $currentRecord->id;
        $cacheKey = 'mtq_timer_' . $slug . '_' . $activeTimerId;
        $timerState = Cache::get($cacheKey);

        if (!$timerState) {
            $timerState = [
                'total_seconds' => $totalSeconds,
                'remaining_seconds' => $totalSeconds,
                'is_running' => false,
                'started_at' => null,
            ];
            Cache::put($cacheKey, $timerState, 86400);
        }

        if ($timerState['is_running'] && $timerState['started_at']) {
            $elapsed = time() - $timerState['started_at'];
            $remaining = max(0, $timerState['remaining_seconds'] - $elapsed);
            if ($remaining <= 0) {
                $timerState['is_running'] = false;
                $timerState['remaining_seconds'] = 0;
                $timerState['started_at'] = null;
                Cache::put($cacheKey, $timerState, 86400);
            }
        } else {
            $remaining = $timerState['remaining_seconds'];
        }
        $m = floor($remaining / 60);
        $s = $remaining % 60;
        $timerFormatted = sprintf('%02d:%02d', $m, $s);

        $cabangNama = $cabang ? $cabang->nama_cabang : $cfg['label'];

        $fieldsData = [];
        $currFilledFields = [];
        foreach ($cfg['fields'] as $f) {
            $rawVal = $currentRecord->{$f['key']} ?? null;
            $val = (float)($rawVal ?? 0);
            $max = $f['max'] ?? 100;
            $pct = min(100, max(0, ($val / $max) * 100));
            $fieldsData[] = [
                'key' => $f['key'],
                'label' => $f['label'],
                'value' => $val,
                'max' => $max,
                'pct' => $pct,
            ];
            if (!is_null($rawVal) && $rawVal !== '' && $val > 0) {
                $currFilledFields[] = $f['label'];
            }
        }

        $isCurrComplete = count($currFilledFields) === $totalFieldsCount && $totalFieldsCount > 0;
        $currEvaluatedLabel = (!$isCurrComplete && count($currFilledFields) > 0) ? implode(', ', $currFilledFields) : '';

        $totalVal = (float)($currentRecord->total ?? 0);

        return new \Illuminate\Http\JsonResponse([
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
                'is_complete' => $isCurrComplete,
                'evaluated_label' => $currEvaluatedLabel,
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
            'active_id' => $liveActiveId ? (int)$liveActiveId : null,
            'total_peserta' => $records->count(),
            'participants' => $participants,
        ]);
    }

    public function controlTimer(Request $request, $slug = 'tartil', $action = 'start')
    {
        $slug = strtolower($slug);
        $liveActiveId = Cache::get('mtq_live_active_' . $slug);
        $id = $request->input('id') ?: $liveActiveId;
        if (!$id) return new \Illuminate\Http\JsonResponse(['error' => 'No ID'], 400);

        if ($action === 'unshow') {
            Cache::forget('mtq_live_active_' . $slug);
            return new \Illuminate\Http\JsonResponse(['success' => true, 'unshow' => true]);
        }

        Cache::put('mtq_live_active_' . $slug, (int)$id, 86400);

        $cfg = self::$config[$slug] ?? self::$config['tartil'];
        $cacheKey = 'mtq_timer_' . $slug . '_' . $id;
        $timerState = Cache::get($cacheKey);

        if (!$timerState) {
            $defaultTimer = $cfg['timer'] ?? '00:05:00';
            $parts = explode(':', $defaultTimer);
            $totalSeconds = count($parts) === 3 ? ((int)$parts[0] * 3600 + (int)$parts[1] * 60 + (int)$parts[2]) : (count($parts) === 2 ? ((int)$parts[0] * 60 + (int)$parts[1]) : 300);
            $timerState = [
                'total_seconds' => $totalSeconds,
                'remaining_seconds' => $totalSeconds,
                'is_running' => false,
                'started_at' => null,
            ];
            Cache::put($cacheKey, $timerState, 86400);
        }

        if ($action === 'show') {
            $calcRemaining = (int)$timerState['remaining_seconds'];
            if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
                $elapsed = time() - $timerState['started_at'];
                $calcRemaining = max(0, $calcRemaining - $elapsed);
            }
            return new \Illuminate\Http\JsonResponse([
                'success' => true,
                'show' => true,
                'active_id' => (int)$id,
                'timer' => [
                    'remaining' => $calcRemaining,
                    'total' => (int)$timerState['total_seconds'],
                    'is_running' => (bool)$timerState['is_running'],
                ],
            ]);
        }

        if ($action === 'start') {
            if ($timerState['remaining_seconds'] <= 0) {
                $timerState['remaining_seconds'] = $timerState['total_seconds'];
            }
            if (!$timerState['is_running'] || empty($timerState['started_at'])) {
                $timerState['started_at'] = time();
            }
            $timerState['is_running'] = true;
            Cache::put($cacheKey, $timerState, 86400);
        } elseif ($action === 'pause') {
            if ($timerState['is_running'] && $timerState['started_at']) {
                $elapsed = time() - $timerState['started_at'];
                $timerState['remaining_seconds'] = max(0, $timerState['remaining_seconds'] - $elapsed);
            }
            $timerState['is_running'] = false;
            $timerState['started_at'] = null;
            Cache::put($cacheKey, $timerState, 86400);
        } elseif ($action === 'reset') {
            $timerState['is_running'] = false;
            $timerState['started_at'] = null;
            $timerState['remaining_seconds'] = $timerState['total_seconds'];
            Cache::put($cacheKey, $timerState, 86400);
        }

        $calcRemaining = (int)$timerState['remaining_seconds'];
        if (!empty($timerState['is_running']) && !empty($timerState['started_at'])) {
            $elapsed = time() - $timerState['started_at'];
            $calcRemaining = max(0, $calcRemaining - $elapsed);
        }

        return new \Illuminate\Http\JsonResponse([
            'success' => true,
            'timer' => [
                'remaining' => $calcRemaining,
                'total' => (int)$timerState['total_seconds'],
                'is_running' => (bool)$timerState['is_running'],
            ],
        ]);
    }

    public function simpanNilai(Request $request, $slug = 'tartil')
    {
        $slug = strtolower($slug);
        $id = $request->input('id');
        if (!$id) {
            return new \Illuminate\Http\JsonResponse(['error' => 'No ID provided'], 400);
        }

        $fields = \App\Filament\Penilaian\Concerns\HasLiveScoreActions::$cabangFields[$slug] ?? null;
        if (!$fields) {
            return new \Illuminate\Http\JsonResponse(['error' => 'Invalid slug'], 400);
        }

        $cfg = self::$config[$slug] ?? null;
        $modelClass = $cfg ? $cfg['model'] : null;
        if (!$modelClass) {
            return new \Illuminate\Http\JsonResponse(['error' => 'Invalid model'], 400);
        }

        $record = $modelClass::find($id);
        if (!$record) {
            return new \Illuminate\Http\JsonResponse(['error' => 'Record not found'], 404);
        }

        $total = 0;
        foreach ($fields as $field) {
            $key = $field['key'];
            $max = $field['max'] ?? 100;
            $val = min($max, max(0, floatval($request->input($key, 0))));
            $record->{$key} = $val;
            $total += $val;
        }

        if (in_array($slug, ['satujuz', 'limajuz'])) {
            $record->total_tilawah = floatval($record->til_tajwid ?? 0) + floatval($record->til_lagu ?? 0) + floatval($record->til_suara ?? 0) + floatval($record->til_fashahah ?? 0);
            $record->total_tahfizh = floatval($record->tah_tahfizh ?? 0) + floatval($record->tah_tajwid ?? 0) + floatval($record->tah_fashahah ?? 0);
            $total = $record->total_tilawah + $record->total_tahfizh;
        }

        $record->total = $total;

        if ($slug === 'tartil') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_tajwid = ($record->tajwid ?? 0) * 1000000;
            $record->bobot_irama_dan_suara = ($record->irama_dan_suara ?? 0) * 10000;
            $record->bobot_fashahah = ($record->fashahah ?? 0) * 100;
            $record->final_bobot = $record->bobot_tajwid + $record->bobot_irama_dan_suara + $record->bobot_fashahah + $record->bobot_total;
        } elseif (in_array($slug, ['anak', 'remaja', 'dewasa'])) {
            $record->bobot_total = $total * 100000000;
            $record->bobot_tajwid = ($record->tajwid ?? 0) * 1000000;
            $record->bobot_lagu = ($record->lagu ?? 0) * 10000;
            $record->bobot_fashahah = ($record->fashahah ?? 0) * 100;
            $record->final_bobot = $record->bobot_tajwid + $record->bobot_lagu + $record->bobot_fashahah + $record->bobot_total;
        } elseif (in_array($slug, ['sepuluhjuz', 'duapuluhjuz', 'tigapuluhjuz'])) {
            $record->bobot_total = $total * 100000000;
            $record->bobot_tahfizh = ($record->tahfizh ?? 0) * 1000000;
            $record->bobot_tajwid = ($record->tajwid ?? 0) * 10000;
            $record->bobot_fashahah = ($record->fashahah ?? 0) * 100;
            $record->final_bobot = $record->bobot_tahfizh + $record->bobot_tajwid + $record->bobot_fashahah + $record->bobot_total;
        } elseif (in_array($slug, ['satujuz', 'limajuz'])) {
            $record->bobot_total = $total * 100000000;
            $record->bobot_total_tahfizh = ($record->total_tahfizh ?? 0) * 1000000;
            $record->bobot_til_tajwid = ($record->til_tajwid ?? 0) * 10000;
            $record->bobot_tah_tahfizh = ($record->tah_tahfizh ?? 0) * 100;
            $record->final_bobot = $record->bobot_total + $record->bobot_til_tajwid + $record->bobot_tah_tahfizh + $record->bobot_total_tahfizh;
        } elseif ($slug === 'mushaf') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_kebenaran_kaidah_khat = ($record->kebenaran_kaidah_khat ?? 0) * 1000000;
            $record->bobot_keindahan_khat = ($record->keindahan_khat ?? 0) * 10000;
            $record->bobot_keindahan_hiasan_dan_lukisan = ($record->keindahan_hiasan_dan_lukisan ?? 0) * 100;
            $record->final_bobot = $record->bobot_kebenaran_kaidah_khat + $record->bobot_keindahan_khat + $record->bobot_keindahan_hiasan_dan_lukisan + $record->bobot_total;
        } elseif ($slug === 'dekorasi') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_kebenaran_kaidah_khath = ($record->kebenaran_kaidah_khath ?? 0) * 1000000;
            $record->bobot_keindahan_khath = ($record->keindahan_khath ?? 0) * 10000;
            $record->bobot_keindahan_hiasan_dan_lukisan = ($record->keindahan_hiasan_dan_lukisan ?? 0) * 100;
            $record->final_bobot = $record->bobot_kebenaran_kaidah_khath + $record->bobot_keindahan_khath + $record->bobot_keindahan_hiasan_dan_lukisan + $record->bobot_total;
        } elseif ($slug === 'kontemporer') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_unsur_kaligrafi = ($record->unsur_kaligrafi ?? 0) * 1000000;
            $record->bobot_unsur_seni_rupa = ($record->unsur_seni_rupa ?? 0) * 10000;
            $record->bobot_sentuhan_akhir = ($record->sentuhan_akhir ?? 0) * 100;
            $record->final_bobot = $record->bobot_unsur_kaligrafi + $record->bobot_unsur_seni_rupa + $record->bobot_sentuhan_akhir + $record->bobot_total;
        } elseif ($slug === 'msq') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_terjemahan_dan_materi = ($record->terjemahan_dan_materi ?? 0) * 1000000;
            $record->bobot_penghayatan_dan_retorika = ($record->penghayatan_dan_retorika ?? 0) * 10000;
            $record->bobot_tilawah = ($record->tilawah ?? 0) * 100;
            $record->final_bobot = $record->bobot_terjemahan_dan_materi + $record->bobot_penghayatan_dan_retorika + $record->bobot_tilawah + $record->bobot_total;
        } elseif ($slug === 'mmq') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_bobot_materi = ($record->bobot_materi ?? 0) * 1000000;
            $record->bobot_kaidah_dan_gaya_bahasa = ($record->kaidah_dan_gaya_bahasa ?? 0) * 10000;
            $record->bobot_logika_dan_organisasi_pesan = ($record->logika_dan_organisasi_pesan ?? 0) * 100;
            $record->final_bobot = $record->bobot_bobot_materi + $record->bobot_kaidah_dan_gaya_bahasa + $record->bobot_logika_dan_organisasi_pesan + $record->bobot_total;
        } elseif ($slug === 'naskah') {
            $record->bobot_total = $total * 100000000;
            $record->bobot_kebenaran_kaidah_khat_wajib = ($record->kebenaran_kaidah_khat_wajib ?? 0) * 1000000;
            $record->bobot_keindahan_khat_wajib = ($record->keindahan_khat_wajib ?? 0) * 10000;
            $record->bobot_kebenaran_kaidah_khat_pilihan = ($record->kebenaran_kaidah_khat_pilihan ?? 0) * 100;
            $record->final_bobot = $record->bobot_kebenaran_kaidah_khat_wajib + $record->bobot_keindahan_khat_wajib + $record->bobot_kebenaran_kaidah_khat_pilihan + $record->bobot_total;
        }

        $record->save();

        $selectedTahunId = session('selected_tahun_id', '0');
        Cache::forget('stats_hdr_' . md5($modelClass . '_' . $selectedTahunId));
        Cache::forget('cabang_stats_' . md5($modelClass . '_' . $selectedTahunId));

        $fieldKeys = array_column($fields, 'key');
        return new \Illuminate\Http\JsonResponse([
            'success' => true,
            'id' => $record->id,
            'total' => $total,
            'fields' => $record->only($fieldKeys),
        ]);
    }
}
