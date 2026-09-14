<?php

namespace App\Filament\Penilaian\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class PenilaianButtonsWidget extends Widget
{
    protected static string $view = 'filament.penilaian.widgets.penilaian-buttons';

    protected int | string | array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = Auth::user();
        if (!$user) {
            return ['accessibleResources' => []];
        }

        $resources = [
            ['class' => \App\Filament\Penilaian\Resources\NilaiTartilResource::class, 'label' => 'Tartil', 'icon' => '📖', 'url' => '/penilaian/nilai-tartils', 'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiAnakResource::class, 'label' => 'Tilawah Anak-anak', 'icon' => '👶', 'url' => '/penilaian/nilai-anaks', 'color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiRemajaResource::class, 'label' => 'Tilawah Remaja', 'icon' => '', 'url' => '/penilaian/nilai-remajas', 'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDewasaResource::class, 'label' => 'Tilawah Dewasa', 'icon' => '🧔', 'url' => '/penilaian/nilai-dewasas', 'color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiSatuJuzResource::class, 'label' => 'MHQ 1 Juz', 'icon' => '1️⃣', 'url' => '/penilaian/nilai-satu-juzs', 'color' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiLimaJuzResource::class, 'label' => 'MHQ 5 Juz', 'icon' => '5️', 'url' => '/penilaian/nilai-lima-juzs', 'color' => 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiSepuluhJuzResource::class, 'label' => 'MHQ 10 Juz', 'icon' => '', 'url' => '/penilaian/nilai-sepuluh-juzs', 'color' => 'linear-gradient(135deg, #fccb90 0%, #d57eeb 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDuapuluhJuzResource::class, 'label' => 'MHQ 20 Juz', 'icon' => '2️⃣0️⃣', 'url' => '/penilaian/nilai-duapuluh-juzs', 'color' => 'linear-gradient(135deg, #e0c3fc 0%, #8ec5fc 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiTigapuluhJuzResource::class, 'label' => 'MHQ 30 Juz', 'icon' => '3️⃣0️⃣', 'url' => '/penilaian/nilai-tigapuluh-juzs', 'color' => 'linear-gradient(135deg, #f5576c 0%, #ff6b6b 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMfqResource::class, 'label' => 'MFQ', 'icon' => '🏆', 'url' => '/penilaian/nilai-mfqs', 'color' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMmqResource::class, 'label' => 'KTIQ', 'icon' => '📝', 'url' => '/penilaian/nilai-mmqs', 'color' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMsqResource::class, 'label' => 'MSQ', 'icon' => '', 'url' => '/penilaian/nilai-msqs', 'color' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiNaskahResource::class, 'label' => 'MKQ Naskah', 'icon' => '📜', 'url' => '/penilaian/nilai-naskahs', 'color' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiDekorasiResource::class, 'label' => 'MKQ Dekorasi', 'icon' => '', 'url' => '/penilaian/nilai-dekorasis', 'color' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiMushafResource::class, 'label' => 'MKQ Hiasan Mushaf', 'icon' => '', 'url' => '/penilaian/nilai-mushafs', 'color' => 'linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%)'],
            ['class' => \App\Filament\Penilaian\Resources\NilaiKontemporerResource::class, 'label' => 'MKQ Kontemporer', 'icon' => '🎭', 'url' => '/penilaian/nilai-kontemporers', 'color' => 'linear-gradient(135deg, #fccb90 0%, #d57eeb 100%)'],
        ];

        $accessible = [];
        foreach ($resources as $r) {
            try {
                if ($r['class']::canViewAny()) {
                    $accessible[] = $r;
                }
            } catch (\Throwable $e) {
                // skip if error
            }
        }

        return ['accessibleResources' => $accessible];
    }
}
