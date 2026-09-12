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
            ['class' => \App\Filament\Penilaian\Resources\NilaiTartilResource::class, 'table' => 'nilai_tartils', 'label' => 'Tartil'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiAnakResource::class, 'table' => 'nilai_anaks', 'label' => 'Tilawah Anak-anak'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiRemajaResource::class, 'table' => 'nilai_remajas', 'label' => 'Tilawah Remaja'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDewasaResource::class, 'table' => 'nilai_dewasas', 'label' => 'Tilawah Dewasa'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiSatuJuzResource::class, 'table' => 'nilai_satu_juzs', 'label' => 'MHQ 1 Juz dan Tilawah'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiLimaJuzResource::class, 'table' => 'nilai_lima_juzs', 'label' => 'MHQ 5 Juz dan Tilawah'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource::class, 'table' => 'nilai_sepuluh_juzs', 'label' => 'MHQ 10 Juz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDuapuluhJuzResource::class, 'table' => 'nilai_duapuluh_juzs', 'label' => 'MHQ 20 Juz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiTigapuluhJuzResource::class, 'table' => 'nilai_tigapuluh_juzs', 'label' => 'MHQ 30 Juz'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMfqResource::class, 'table' => 'nilai_mfqs', 'label' => 'MFQ'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMmqResource::class, 'table' => 'nilai_mmqs', 'label' => 'MMQ'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMsqResource::class, 'table' => 'nilai_msqs', 'label' => 'MSQ'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiNaskahResource::class, 'table' => 'nilai_naskahs', 'label' => 'MKQ Naskah'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDekorasiResource::class, 'table' => 'nilai_dekorasis', 'label' => 'MKQ Dekorasi'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMushafResource::class, 'table' => 'nilai_mushafs', 'label' => 'MKQ Hiasan Mushaf'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiKontemporerResource::class, 'table' => 'nilai_kontemporers', 'label' => 'MKQ Kontemporer'],
        ];

        $activeTahun = \App\Models\Tahun::where('is_active', true)->first();
        $selectedTahunId = session("selected_tahun_id", $activeTahun?->id);
        $currentTahun = $selectedTahunId ? \App\Models\Tahun::find($selectedTahunId) : $activeTahun;

        $stats = null;
        $cabangLabel = $user->name ?? 'Cabang';

        foreach ($resources as $r) {
            if (!$r['class']::canViewAny()) continue;
            if (!Schema::hasTable($r['table'])) continue;

            $table = $r['table'];
            $query = DB::table($table)->join('pesertas', "$table.peserta_id", '=', 'pesertas.id');
            if ($selectedTahunId) {
                $query->where('pesertas.tahun_id', $selectedTahunId);
            }

            $total = (clone $query)->count();
            $sudahDinilai = (clone $query)->whereNotNull("$table.total")->where("$table.total", '>', 0)->count();
            $belumDinilai = max(0, $total - $sudahDinilai);
            $laki = (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putra%')->count();
            $perempuan = (clone $query)->where('pesertas.jenis_kelamin', 'like', '%putri%')->count();

            $stats = [
                'total' => $total,
                'sudah_dinilai' => $sudahDinilai,
                'belum_dinilai' => $belumDinilai,
                'laki' => $laki,
                'perempuan' => $perempuan,
                'tahun' => $currentTahun?->tahun ?? date('Y'),
            ];
            $cabangLabel = $r['label'];
            break;
        }

        return ['stats' => $stats, 'cabangLabel' => $cabangLabel];
    }
}
