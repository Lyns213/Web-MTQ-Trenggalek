<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Peserta - MTQ Kabupaten Trenggalek</title>
    <link rel="icon" href="{{ asset('images/logotgxmini.png') }}" type="image/png">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            line-height: 1.3;
        }

        /* Toolbar Layar */
        .toolbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: #0f172a;
            color: #fff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }

        .toolbar-title {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 600;
        }

        .toolbar-title span {
            background: #d97706;
            color: #fff;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 12px;
        }

        .toolbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-gold {
            background: linear-gradient(135deg, #d97706, #b45309);
            color: #fff;
        }
        .btn-gold:hover {
            opacity: 0.9;
        }

        .btn-secondary {
            background: #334155;
            color: #f8fafc;
        }
        .btn-secondary:hover {
            background: #475569;
        }

        /* Print Sheets Layout */
        .print-container {
            max-width: 210mm;
            margin: 20px auto;
            padding: 0;
        }

        .card-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #fff;
            margin: 0 auto 25px auto;
            padding: 10mm;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
            display: grid;
            grid-template-columns: repeat(2, 92mm);
            grid-auto-rows: 136mm;
            gap: 6mm 6mm;
            justify-content: center;
            align-content: start;
        }

        /* Badge Card Design */
        .card-peserta {
            width: 92mm;
            height: 136mm;
            position: relative;
            background: #ffffff;
            border-radius: 12px;
            border: 3px solid #581c87;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Color variations for Putri */
        .card-peserta.putri {
            border-color: #831843;
        }

        .card-peserta.putri .card-header-bg {
            background: linear-gradient(135deg, #701a75, #831843);
        }

        .card-peserta.putri .badge-number {
            background: #831843;
            border-color: #f472b6;
        }

        /* Card Header */
        .card-header {
            position: relative;
            height: 38mm;
            background: linear-gradient(135deg, #3b0764, #581c87);
            color: #fff;
            padding: 5px 8px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        /* Left arch / Islamic dome ornament overlay */
        .card-arch-overlay {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: auto;
            opacity: 0.25;
            pointer-events: none;
            mix-blend-mode: screen;
        }

        .card-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            z-index: 2;
        }

        .logo-box {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-box img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .header-titles {
            flex: 1;
            padding: 0 4px;
        }

        .instansi-title {
            font-family: 'Cinzel', serif;
            font-size: 6.5pt;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #fef08a;
            line-height: 1.1;
        }

        .daerah-title {
            font-size: 8pt;
            font-weight: 800;
            letter-spacing: 0.8px;
            color: #ffffff;
            margin-top: 1px;
            text-transform: uppercase;
        }

        .tahun-title {
            font-size: 6pt;
            font-weight: 600;
            color: #fde68a;
        }

        .badge-title-wrap {
            position: relative;
            z-index: 2;
            margin-top: 2px;
        }

        .badge-card-type {
            display: inline-block;
            background: linear-gradient(90deg, #d97706, #f59e0b, #d97706);
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            font-size: 7.5pt;
            letter-spacing: 1.2px;
            padding: 2px 14px;
            border-radius: 999px;
            text-transform: uppercase;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            border: 1px solid #fef08a;
        }

        /* Card Body */
        .card-body {
            flex: 1;
            position: relative;
            padding: 6px 8px 4px 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #ffffff;
            background-image: radial-gradient(#e2e8f0 0.75px, transparent 0.75px);
            background-size: 8px 8px;
        }

        /* Photo & Name Section */
        .photo-wrap {
            width: 28mm;
            height: 35mm;
            border-radius: 6px;
            border: 2.5px solid #d97706;
            overflow: hidden;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
            margin-top: -12px;
            position: relative;
            z-index: 5;
        }

        .photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            font-size: 26pt;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: #f1f5f9;
        }

        .participant-name {
            margin-top: 4px;
            font-size: 9.5pt;
            font-weight: 800;
            color: #0f172a;
            text-align: center;
            text-transform: uppercase;
            line-height: 1.15;
            max-height: 2.3em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            padding: 0 4px;
        }

        .badge-number {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #3b0764;
            color: #fde68a;
            font-size: 7pt;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 4px;
            margin-top: 3px;
            border: 1px solid #d97706;
            letter-spacing: 0.5px;
        }

        /* Details Meta Table */
        .meta-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            font-size: 6.8pt;
        }

        .meta-table tr td {
            padding: 2px 3px;
            vertical-align: top;
        }

        .meta-table .lbl {
            width: 28%;
            color: #475569;
            font-weight: 600;
        }

        .meta-table .sep {
            width: 3%;
            text-align: center;
            color: #94a3b8;
        }

        .meta-table .val {
            width: 69%;
            color: #0f172a;
            font-weight: 700;
        }

        .val-cabang {
            color: #1e3a8a !important;
            font-weight: 800 !important;
        }

        .val-utusan {
            color: #065f46 !important;
            font-weight: 800 !important;
            text-transform: uppercase;
        }

        /* Card Bottom Section (QR + Signature/Stempel) */
        .card-bottom-row {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
            padding-top: 4px;
            border-top: 1px dashed #cbd5e1;
        }

        .qr-box {
            width: 19mm;
            height: 19mm;
            background: #fff;
            padding: 2px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .signature-box {
            flex: 1;
            text-align: right;
            padding-left: 6px;
        }

        .sig-city {
            font-size: 5.5pt;
            color: #475569;
        }

        .sig-label {
            font-size: 5.8pt;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .sig-stamp {
            height: 16px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin: 1px 0;
        }

        .sig-stamp img {
            max-height: 100%;
            opacity: 0.85;
        }

        .sig-name {
            font-size: 5.8pt;
            font-weight: 800;
            color: #0f172a;
            text-decoration: underline;
        }

        /* Card Footer Logos */
        .card-footer {
            height: 6.5mm;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1px 8px;
        }

        .card-footer img {
            max-height: 5mm;
            max-width: 90%;
            object-fit: contain;
        }

        /* Print Media Styles */
        @media print {
            body {
                background: #ffffff !important;
            }

            .toolbar {
                display: none !important;
            }

            .print-container {
                max-width: 100%;
                margin: 0 !important;
                padding: 0 !important;
            }

            .card-sheet {
                margin: 0 !important;
                padding: 8mm 6mm !important;
                box-shadow: none !important;
                page-break-after: always;
                break-after: page;
            }

            .card-sheet:last-child {
                page-break-after: auto !important;
                break-after: auto !important;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Top Toolbar (Screen Only) -->
    <div class="toolbar no-print">
        <div class="toolbar-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
            </svg>
            <span>Cetak Kartu Peserta MTQ</span>
            <span>Total: {{ $pesertas->count() }} Peserta</span>
        </div>
        <div class="toolbar-actions">
            <a href="{{ url('/admin/pesertas') }}" class="btn btn-secondary">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali ke Admin
            </a>
            <button onclick="window.print()" class="btn btn-gold">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Cetak / Download PDF (Ctrl+P)
            </button>
        </div>
    </div>

    <!-- Cards Container -->
    <div class="print-container">
        @php
            // Group peserta in chunks of 4 cards per A4 sheet
            $chunks = $pesertas->chunk(4);
            $tahunAktif = $tahun->tahun ?? date('Y');
        @endphp

        @foreach($chunks as $chunk)
            <div class="card-sheet">
                @foreach($chunk as $p)
                    @php
                        $isPutri = strtolower($p->jenis_kelamin ?? '') === 'putri';
                        
                        // Check photo path
                        $fotoUrl = null;
                        if (!empty($p->pasfoto)) {
                            if (file_exists(public_path('storage/' . $p->pasfoto))) {
                                $fotoUrl = asset('storage/' . $p->pasfoto);
                            } elseif (file_exists(public_path($p->pasfoto))) {
                                $fotoUrl = asset($p->pasfoto);
                            } elseif (filter_var($p->pasfoto, FILTER_VALIDATE_URL)) {
                                $fotoUrl = $p->pasfoto;
                            }
                        }

                        // Participant QR Code Data
                        $qrData = "MTQ KAB. TRENGGALEK\n"
                                . "No: " . ($p->no_peserta ?? '-') . "\n"
                                . "Nama: " . $p->nama . "\n"
                                . "Cabang: " . ($p->cabang->nama_cabang ?? '-') . "\n"
                                . "Utusan: " . ($p->utusan->kecamatan ?? '-') . "\n"
                                . "Status: SAH";
                        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($qrData);
                    @endphp

                    <div class="card-peserta {{ $isPutri ? 'putri' : 'putra' }}">
                        <!-- Header -->
                        <div class="card-header {{ $isPutri ? 'putri' : '' }}">
                            @if(file_exists(public_path('images/asset_left_arch.png')))
                                <img src="{{ asset('images/asset_left_arch.png') }}" class="card-arch-overlay" alt="Arch">
                            @endif

                            <div class="card-header-top">
                                <div class="logo-box">
                                    <img src="{{ asset('images/logo_trenggalek.png') }}" alt="Pemkab Trenggalek" onerror="this.src='{{ asset('images/logotgxmini.png') }}'">
                                </div>
                                <div class="header-titles">
                                    <div class="instansi-title">LPTQ KABUPATEN TRENGGALEK</div>
                                    <div class="daerah-title">MTQ KABUPATEN TRENGGALEK</div>
                                    <div class="tahun-title">TAHUN {{ $tahunAktif }}</div>
                                </div>
                                <div class="logo-box">
                                    <img src="{{ asset('images/logo_lptq.png') }}" alt="LPTQ" onerror="this.src='{{ asset('images/logo_kemenag.png') }}'">
                                </div>
                            </div>

                            <div class="badge-title-wrap">
                                <span class="badge-card-type">KARTU PESERTA</span>
                            </div>
                        </div>

                        <!-- Body Content -->
                        <div class="card-body">
                            <!-- Photo -->
                            <div class="photo-wrap">
                                @if($fotoUrl)
                                    <img src="{{ $fotoUrl }}" alt="{{ $p->nama }}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="photo-placeholder" style="display: none;">👤</div>
                                @else
                                    <div class="photo-placeholder">👤</div>
                                @endif
                            </div>

                            <!-- Name -->
                            <div class="participant-name">
                                {{ $p->nama }}
                            </div>

                            <!-- Number Badge -->
                            <div class="badge-number">
                                <span>NO :</span>
                                <strong>{{ $p->no_peserta ?: sprintf('P-%03d', $p->id) }}</strong>
                            </div>

                            <!-- Data Table -->
                            <table class="meta-table">
                                <tr>
                                    <td class="lbl">Cabang</td>
                                    <td class="sep">:</td>
                                    <td class="val val-cabang">{{ $p->cabang->nama_cabang ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Kafilah</td>
                                    <td class="sep">:</td>
                                    <td class="val val-utusan">{{ $p->utusan->kecamatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">Golongan</td>
                                    <td class="sep">:</td>
                                    <td class="val">{{ ucfirst($p->jenis_kelamin ?? '-') }}</td>
                                </tr>
                                <tr>
                                    <td class="lbl">TTL</td>
                                    <td class="sep">:</td>
                                    <td class="val">{{ $p->tempat_lahir ? $p->tempat_lahir . ', ' . ($p->tgl_lahir ? $p->tgl_lahir->format('d-m-Y') : '-') : '-' }}</td>
                                </tr>
                            </table>

                            <!-- Bottom Row: QR & Validation -->
                            <div class="card-bottom-row">
                                <div class="qr-box">
                                    <img src="{{ $qrUrl }}" alt="QR Code Validasi" onerror="this.parentElement.innerHTML='<div style=\'font-size:7pt;text-align:center;font-weight:bold;color:#059669;line-height:1.1;\'>VERIFIED<br>OFFICIAL</div>';">
                                </div>
                                <div class="signature-box">
                                    <div class="sig-city">Trenggalek, {{ date('Y') }}</div>
                                    <div class="sig-label">Panitia Pelaksana MTQ</div>
                                    <div class="sig-stamp">
                                        @if(file_exists(public_path('images/asset_seal_trenggalek.png')))
                                            <img src="{{ asset('images/asset_seal_trenggalek.png') }}" alt="Seal">
                                        @endif
                                    </div>
                                    <div class="sig-name">LPTQ TRENGGALEK</div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer logos -->
                        <div class="card-footer">
                            @if(file_exists(public_path('images/asset_footer_logos.png')))
                                <img src="{{ asset('images/asset_footer_logos.png') }}" alt="Sponsor Logos">
                            @elseif(file_exists(public_path('images/asset_logos_footer.png')))
                                <img src="{{ asset('images/asset_logos_footer.png') }}" alt="Sponsor Logos">
                            @else
                                <span style="font-size: 5pt; color: #94a3b8; font-weight: 600; letter-spacing: 0.5px;">LPTQ KABUPATEN TRENGGALEK - MTQ TAHUN {{ $tahunAktif }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

</body>
</html>
