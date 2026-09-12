<?php

namespace App\Filament\Penilaian\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardStatsWidget extends Widget
{
    protected static string $view = 'filament.penilaian.widgets.dashboard-stats';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = Auth::user();
        if (!$user) {
            return ['stats' => null, 'cabangLabel' => ''];
        }

        $resources = [
            ['class' => \App\Filament\Penilaian\Resources\NilaiTartilResource::class, 'table' => 'nilai_tartils', 'label' => 'Tartil', 'slug' => 'tartil'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiAnakResource::class, 'table' => 'nilai_anaks', 'label' => 'Tilawah Anak-anak', 'slug' => 'anak'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiRemajaResource::class, 'table' => 'nilai_remajas', 'label' => 'Tilawah Remaja', 'slug' => 'remaja'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDewasaResource::class, 'table' => 'nilai_dewasas', 'label' => 'Tilawah Dewasa', 'slug' => 'dewasa'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiSatuJuzResource::class, 'table' => 'nilai_satu_juzs', 'label' => 'MHQ 1 Juz dan Tilawah', 'slug' => 'satujuz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiLimaJuzResource::class, 'table' => 'nilai_lima_juzs', 'label' => 'MHQ 5 Juz dan Tilawah', 'slug' => 'limajuz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource::class, 'table' => 'nilai_sepuluh_juzs', 'label' => 'MHQ 10 Juz', 'slug' => 'sepuluhjuz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDuapuluhJuzResource::class, 'table' => 'nilai_duapuluh_juzs', 'label' => 'MHQ 20 Juz', 'slug' => 'duapuluhjuz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiTigapuluhJuzResource::class, 'table' => 'nilai_tigapuluh_juzs', 'label' => 'MHQ 30 Juz', 'slug' => 'tigapuluhjuz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMfqResource::class, 'table' => 'nilai_mfqs', 'label' => 'MFQ', 'slug' => 'mfq', 'is_grup' => true],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMmqResource::class, 'table' => 'nilai_mmqs', 'label' => 'MMQ', 'slug' => 'mmq'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMsqResource::class, 'table' => 'nilai_msqs', 'label' => 'MSQ', 'slug' => 'msq'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiNaskahResource::class, 'table' => 'nilai_naskahs', 'label' => 'MKQ Naskah', 'slug' => 'naskah'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDekorasiResource::class, 'table' => 'nilai_dekorasis', 'label' => 'MKQ Dekorasi', 'slug' => 'dekorasi'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMushafResource::class, 'table' => 'nilai_mushafs', 'label' => 'MKQ Hiasan Mushaf', 'slug' => 'mushaf'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiKontemporerResource::class, 'table' => 'nilai_kontemporers', 'label' => 'MKQ Kontemporer', 'slug' => 'kontemporer'],
        ];

        $activeTahun = \App\Models\Tahun::where('is_active', true)->first();
        $selectedTahunId = session("selected_tahun_id", $activeTahun?->id);
        $currentTahun = $selectedTahunId ? \App\Models\Tahun::find($selectedTahunId) : $activeTahun;

        $stats = null;
        $cabangLabel = $user->name ?? 'Cabang';
        $cabangSlug = 'tartil';

        foreach ($resources as $r) {
            if (!$r['class']::canViewAny()) continue;
            if (!Schema::hasTable($r['table'])) continue;

            $table = $r['table'];
            if (!empty($r['is_grup'])) {
                $query = DB::table($table)->join('grups', "$table.grup_id", '=', 'grups.id');
                if ($selectedTahunId) {
                    $query->where('grups.tahun_id', $selectedTahunId);
                }
                $total = (clone $query)->count();
                $sudahDinilai = (clone $query)->whereNotNull("$table.total")->where("$table.total", '>', 0)->count();
                $belumDinilai = max(0, $total - $sudahDinilai);
                $laki = (clone $query)->where('grups.jenis_kelamin', 'like', '%putra%')->count();
                $perempuan = (clone $query)->where('grups.jenis_kelamin', 'like', '%putri%')->count();
            } else {
                $query = DB::table($table)->join('pesertas', "$table.peserta_id", '=', 'pesertas.id');
                if ($selectedTahunId) {
                    $query->where('pesertas.tahun_id', $selectedTahunId);
                }
                $total = (clone $query)->count();
                $sudahDinilai = (clone $query)->whereNotNull("$table.total")->where("$table.total", '>', 0)->count();
                $belumDinilai = max(0, $total - $sudahDinilai);
                $laki = (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putra%')->count();
                $perempuan = (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putri%')->count();
            }

            $stats = [
                'total' => $total,
                'sudah_dinilai' => $sudahDinilai,
                'belum_dinilai' => $belumDinilai,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'tahun' => $currentTahun?->tahun ?? date('Y'),
            ];
            $cabangLabel = $r['label'];
            $cabangSlug = $r['slug'];
            break;
        }

        return ['stats' => $stats, 'cabangLabel' => $cabangLabel, 'cabangSlug' => $cabangSlug];
    }
}
