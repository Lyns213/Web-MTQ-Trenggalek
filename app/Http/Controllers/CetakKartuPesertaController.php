<?php

namespace App\Http\Controllers;

use App\Models\Official;
use App\Models\Peserta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CetakKartuPesertaController extends Controller
{
    /**
     * Cetak langsung download PDF kartu peserta tunggal
     * Menggunakan ukuran A6 (105mm x 148mm) pas ukuran kartu ID Card
     */
    public function single($id)
    {
        $peserta = Peserta::with(['cabang', 'utusan', 'tahun'])->findOrFail($id);

        $subrole = $this->formatSubrole($peserta->cabang?->nama_cabang, $peserta->jenis_kelamin);

        $cards = collect([[
            'role' => 'peserta',
            'subrole' => $subrole,
            'nomor' => $peserta->no_peserta ?: sprintf('%03d', $peserta->id),
            'nama' => strtoupper($peserta->nama),
            'photo_base64' => $this->getPhotoBase64($peserta->pasfoto),
        ]]);

        $bgTemplates = $this->loadBackgroundTemplates();

        $pdf = Pdf::loadView('cetak-kartu.pdf-cards', [
            'cards' => $cards,
            'bgTemplates' => $bgTemplates,
            'role' => 'peserta',
            'isSingle' => true,
        ]);

        // A6 size in pt (105mm x 148mm = 297.64pt x 419.53pt)
        $pdf->setPaper([0, 0, 297.64, 419.53], 'portrait');

        $nomor = $peserta->no_peserta ?: sprintf('%03d', $peserta->id);
        $filename = 'Kartu-Peserta-' . Str::slug($peserta->nama . '-' . $nomor) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Cetak langsung download PDF kartu peserta massal (bulk)
     * Menggunakan kertas A4 (4 kartu per lembar)
     */
    public function bulk(Request $request)
    {
        $ids = array_filter(explode(',', (string) $request->query('ids', '')));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu peserta untuk dicetak.');
        }

        $pesertas = Peserta::with(['cabang', 'utusan', 'tahun'])
            ->whereIn('id', $ids)
            ->get();

        if ($pesertas->isEmpty()) {
            return redirect()->back()->with('error', 'Data peserta tidak ditemukan.');
        }

        $cards = $pesertas->map(function ($p) {
            $subrole = $this->formatSubrole($p->cabang?->nama_cabang, $p->jenis_kelamin);

            return [
                'role' => 'peserta',
                'subrole' => $subrole,
                'nomor' => $p->no_peserta ?: sprintf('%03d', $p->id),
                'nama' => strtoupper($p->nama),
                'photo_base64' => $this->getPhotoBase64($p->pasfoto),
            ];
        });

        $bgTemplates = $this->loadBackgroundTemplates();

        $pdf = Pdf::loadView('cetak-kartu.pdf-cards', [
            'cards' => $cards,
            'bgTemplates' => $bgTemplates,
            'role' => 'peserta',
            'isSingle' => false,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = 'Kartu-Peserta-Massal-' . count($pesertas) . '-Peserta.pdf';

        return $pdf->download($filename);
    }

    /**
     * Cetak single ID Card Official (A6)
     */
    public function singleOfficial($id)
    {
        $official = Official::findOrFail($id);

        $cards = collect([[
            'role' => $official->role,
            'subrole' => strtoupper($official->jabatan ?? ''),
            'nomor' => '',
            'nama' => strtoupper($official->nama),
            'photo_base64' => $this->getPhotoBase64($official->foto),
        ]]);

        $bgTemplates = $this->loadBackgroundTemplates();

        $pdf = Pdf::loadView('cetak-kartu.pdf-cards', [
            'cards' => $cards,
            'bgTemplates' => $bgTemplates,
            'role' => $official->role,
            'isSingle' => true,
        ]);

        $pdf->setPaper([0, 0, 297.64, 419.53], 'portrait');

        $roleTitle = match($official->role) {
            'dewan_hakim' => 'Dewan-Hakim',
            'panitera' => 'Panitera',
            'panitia' => 'Panitia',
            default => 'Official'
        };

        $filename = 'Kartu-' . $roleTitle . '-' . Str::slug($official->nama) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Cetak bulk ID Card Official terpilih (A4, 4 kartu per lembar)
     */
    public function bulkOfficial(Request $request)
    {
        $ids = array_filter(explode(',', (string) $request->query('ids', '')));

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Pilih minimal satu data untuk dicetak.');
        }

        $officials = Official::whereIn('id', $ids)->get();

        if ($officials->isEmpty()) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $firstRole = $officials->first()->role;

        $cards = $officials->map(function ($o) {
            return [
                'role' => $o->role,
                'subrole' => strtoupper($o->jabatan ?? ''),
                'nomor' => '',
                'nama' => strtoupper($o->nama),
                'photo_base64' => $this->getPhotoBase64($o->foto),
            ];
        });

        $bgTemplates = $this->loadBackgroundTemplates();

        $pdf = Pdf::loadView('cetak-kartu.pdf-cards', [
            'cards' => $cards,
            'bgTemplates' => $bgTemplates,
            'role' => $firstRole,
            'isSingle' => false,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $roleTitle = match($firstRole) {
            'dewan_hakim' => 'Dewan-Hakim',
            'panitera' => 'Panitera',
            'panitia' => 'Panitia',
            default => 'Official'
        };

        $filename = 'Kartu-' . $roleTitle . '-Massal-' . count($officials) . '-Orang.pdf';

        return $pdf->download($filename);
    }

    /**
     * Format subrole cabang agar tidak terjadi duplikasi kata gender
     * Contoh: 'Tartil Putra' + 'putra' -> 'TARTIL PUTRA' (bukan 'TARTIL PUTRA PUTRA')
     */
    private function formatSubrole(?string $cabangNama, ?string $gender): string
    {
        $cabang = trim($cabangNama ?? '');
        $jk = trim($gender ?? '');

        if (!empty($jk) && !str_contains(strtolower($cabang), strtolower($jk))) {
            return strtoupper(trim($cabang . ' ' . $jk));
        }

        return strtoupper($cabang);
    }

    /**
     * Download ID Card Dewan Hakim
     */
    public function downloadDewanHakim(Request $request)
    {
        return $this->downloadOfficialCards($request, 'dewan_hakim', 'Dewan-Hakim');
    }

    /**
     * Download ID Card Panitera
     */
    public function downloadPanitera(Request $request)
    {
        return $this->downloadOfficialCards($request, 'panitera', 'Panitera');
    }

    /**
     * Download ID Card Panitia
     */
    public function downloadPanitia(Request $request)
    {
        return $this->downloadOfficialCards($request, 'panitia', 'Panitia');
    }

    /**
     * Helper download untuk Dewan Hakim, Panitera, Panitia
     */
    private function downloadOfficialCards(Request $request, string $roleKey, string $roleTitle)
    {
        $cards = collect();

        // 1. Cek input daftar nama beserta foto per orang (people[])
        $people = $request->input('people');
        if (is_array($people) && count($people) > 0) {
            foreach ($people as $index => $personData) {
                $nama = trim($personData['name'] ?? '');
                $photoBase64 = null;

                $photoFile = $request->file("people.{$index}.photo");
                if ($photoFile && $photoFile->isValid()) {
                    $photoBase64 = $this->fitPhotoToFrame($photoFile->getRealPath());
                }

                if (!empty($nama) || !empty($photoBase64)) {
                    $cards->push([
                        'role' => $roleKey,
                        'subrole' => '',
                        'nomor' => '',
                        'nama' => strtoupper($nama ?: 'NAMA LENGKAP'),
                        'photo_base64' => $photoBase64,
                    ]);
                }
            }
        }

        // 2. Jika tidak ada data people[], gunakan input textarea 'names'
        if ($cards->isEmpty()) {
            $namaListRaw = $request->input('names', '');
            $lines = array_values(array_filter(array_map('trim', explode("\n", (string) $namaListRaw))));

            $singlePhoto = $request->file('single_photo');
            $singlePhotoBase64 = null;
            if ($singlePhoto && $singlePhoto->isValid()) {
                $singlePhotoBase64 = $this->fitPhotoToFrame($singlePhoto->getRealPath());
            }

            if (empty($lines)) {
                $lines = [$request->input('single_name') ?: 'NAMA LENGKAP'];
            }

            $cards = collect($lines)->map(function ($nama) use ($roleKey, $singlePhotoBase64) {
                return [
                    'role' => $roleKey,
                    'subrole' => '',
                    'nomor' => '',
                    'nama' => strtoupper($nama),
                    'photo_base64' => $singlePhotoBase64,
                ];
            });
        }

        $isSingle = count($cards) === 1;

        $bgTemplates = $this->loadBackgroundTemplates();

        $pdf = Pdf::loadView('cetak-kartu.pdf-cards', [
            'cards' => $cards,
            'bgTemplates' => $bgTemplates,
            'role' => $roleKey,
            'isSingle' => $isSingle,
        ]);

        if ($isSingle) {
            $pdf->setPaper([0, 0, 297.64, 419.53], 'portrait');
        } else {
            $pdf->setPaper('a4', 'portrait');
        }

        $filename = 'Kartu-' . $roleTitle . '-' . count($cards) . '-Orang.pdf';

        return $pdf->download($filename);
    }

    /**
     * Preload 4 background template cards ke format base64
     */
    private function loadBackgroundTemplates(): array
    {
        return [
            'peserta' => $this->imageFileToBase64(public_path('images/card_bg_peserta.png')),
            'dewan_hakim' => $this->imageFileToBase64(public_path('images/card_bg_dewan_hakim.png')),
            'panitera' => $this->imageFileToBase64(public_path('images/card_bg_panitera.png')),
            'panitia' => $this->imageFileToBase64(public_path('images/card_bg_panitia.png')),
        ];
    }

    /**
     * Konversi pasfoto ke base64 dengan normalisasi rasio bingkai kartu (3:4)
     */
    private function getPhotoBase64(?string $pasfoto): ?string
    {
        if (empty($pasfoto)) {
            return null;
        }

        $candidates = [
            public_path('storage/' . $pasfoto),
            public_path($pasfoto),
            storage_path('app/public/' . $pasfoto),
        ];

        $targetFile = null;
        foreach ($candidates as $filePath) {
            if (file_exists($filePath) && is_file($filePath)) {
                $targetFile = $filePath;
                break;
            }
        }

        if (!$targetFile) {
            return null;
        }

        // Coba optimasi & sesuaikan proporsi pasfoto ke bingkai
        try {
            $fitted = $this->fitPhotoToFrame($targetFile);
            if ($fitted) {
                return $fitted;
            }
        } catch (\Throwable) {
            // Fallback ke file asli jika proses gagal
        }

        $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($targetFile));
    }

    /**
     * Potong dan sesuaikan pasfoto agar presisi dengan rasio bingkai kartu
     */
    private function fitPhotoToFrame(string $filePath): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        $raw = @file_get_contents($filePath);
        if (!$raw) {
            return null;
        }

        $src = @imagecreatefromstring($raw);
        if (!$src) {
            return null;
        }

        // Auto-orientasi dari metadata EXIF kamera HP
        if (function_exists('exif_read_data')) {
            try {
                $exif = @exif_read_data($filePath);
                if (!empty($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3:
                            $src = imagerotate($src, 180, 0);
                            break;
                        case 6:
                            $src = imagerotate($src, -90, 0);
                            break;
                        case 8:
                            $src = imagerotate($src, 90, 0);
                            break;
                    }
                }
            } catch (\Throwable) {
            }
        }

        $origW = imagesx($src);
        $origH = imagesy($src);

        if ($origW < 10 || $origH < 10) {
            imagedestroy($src);
            return null;
        }

        // Deteksi jika gambar hasil scan dengan margin putih lebar (seperti CamScanner)
        $tl = imagecolorsforindex($src, imagecolorat($src, 5, 5));
        $tr = imagecolorsforindex($src, imagecolorat($src, $origW - 6, 5));
        $isWhiteBorder = ($tl['red'] > 240 && $tl['green'] > 240 && $tl['blue'] > 240 &&
                           $tr['red'] > 240 && $tr['green'] > 240 && $tr['blue'] > 240);

        $cropMinX = 0;
        $cropMaxX = $origW;
        $cropMinY = 0;
        $cropMaxY = $origH;

        if ($isWhiteBorder && $origW > 100 && $origH > 100) {
            $midY = (int) ($origH * 0.4);
            for ($x = 0; $x < $origW; $x += 5) {
                $c = imagecolorsforindex($src, imagecolorat($src, $x, $midY));
                if ($c['red'] < 235 || $c['green'] < 235 || $c['blue'] < 235) {
                    $cropMinX = max(0, $x - 5);
                    break;
                }
            }
            for ($x = $origW - 1; $x >= 0; $x -= 5) {
                $c = imagecolorsforindex($src, imagecolorat($src, $x, $midY));
                if ($c['red'] < 235 || $c['green'] < 235 || $c['blue'] < 235) {
                    $cropMaxX = min($origW, $x + 5);
                    break;
                }
            }
            $contentMidX = (int) (($cropMinX + $cropMaxX) / 2);
            for ($y = 0; $y < $origH; $y += 5) {
                $c = imagecolorsforindex($src, imagecolorat($src, $contentMidX, $y));
                if ($c['red'] < 235 || $c['green'] < 235 || $c['blue'] < 235) {
                    $cropMinY = max(0, $y - 5);
                    break;
                }
            }
            for ($y = $origH - 1; $y >= 0; $y -= 5) {
                $c = imagecolorsforindex($src, imagecolorat($src, $contentMidX, $y));
                if ($c['red'] < 235 || $c['green'] < 235 || $c['blue'] < 235) {
                    $cropMaxY = min($origH, $y + 5);
                    break;
                }
            }
        }

        $contentW = max(10, $cropMaxX - $cropMinX);
        $contentH = max(10, $cropMaxY - $cropMinY);

        // Rasio bingkai kartu: 122 / 165 = ~0.7394
        $targetRatio = 122 / 165;
        $contentRatio = $contentW / $contentH;

        if ($contentRatio > $targetRatio) {
            // Gambar lebih lebar (landscape) -> crop kiri/kanan, pertahankan tinggi
            $finalW = (int) round($contentH * $targetRatio);
            $finalH = $contentH;
            $srcX = $cropMinX + (int) round(($contentW - $finalW) / 2);
            $srcY = $cropMinY;
        } else {
            // Gambar lebih tinggi (portrait) -> crop atas/bawah, fokus sedikit ke atas untuk wajah
            $finalW = $contentW;
            $finalH = (int) round($contentW / $targetRatio);
            $srcX = $cropMinX;
            $srcY = $cropMinY + (int) round(($contentH - $finalH) * 0.2);
        }

        $destW = 366;
        $destH = 495;

        $dst = imagecreatetruecolor($destW, $destH);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $destW, $destH, $white);
        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $destW, $destH, $finalW, $finalH);

        ob_start();
        imagejpeg($dst, null, 90);
        $jpegData = ob_get_clean();

        imagedestroy($src);
        imagedestroy($dst);

        if ($jpegData) {
            return 'data:image/jpeg;base64,' . base64_encode($jpegData);
        }

        return null;
    }

    private function imageFileToBase64(string $path): ?string
    {
        if (file_exists($path) && is_file($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
            return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
        }
        return null;
    }
}
