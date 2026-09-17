<?php

namespace App\Http\Controllers;

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
        $namaListRaw = $request->input('names', '');
        $lines = array_values(array_filter(array_map('trim', explode("\n", (string) $namaListRaw))));

        if (empty($lines)) {
            $lines = [$request->input('single_name') ?: 'NAMA LENGKAP'];
        }

        $cards = collect($lines)->map(function ($nama) use ($roleKey) {
            return [
                'role' => $roleKey,
                'subrole' => '',
                'nomor' => '',
                'nama' => strtoupper($nama),
                'photo_base64' => null,
            ];
        });

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
     * Konversi pasfoto ke base64
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

        foreach ($candidates as $filePath) {
            if (file_exists($filePath) && is_file($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($filePath));
            }
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
