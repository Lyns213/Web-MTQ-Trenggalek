@php
    $isPutri = strtolower($p->jenis_kelamin ?? '') === 'putri';
@endphp

<div class="card {{ $isPutri ? 'putri' : '' }}">
    <!-- Header -->
    <div class="card-header {{ $isPutri ? 'putri' : '' }}">
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="26" align="center" valign="middle">
                    @if(!empty($assets['logo_trenggalek']))
                        <img src="{{ $assets['logo_trenggalek'] }}" width="24" height="24">
                    @endif
                </td>
                <td align="center" valign="middle">
                    <div class="header-title-instansi">LPTQ KABUPATEN TRENGGALEK</div>
                    <div class="header-title-daerah">MTQ KABUPATEN TRENGGALEK</div>
                    <div class="header-title-tahun">TAHUN {{ $tahunAktif }}</div>
                </td>
                <td width="26" align="center" valign="middle">
                    @if(!empty($assets['logo_lptq']))
                        <img src="{{ $assets['logo_lptq'] }}" width="22" height="24">
                    @endif
                </td>
            </tr>
        </table>
        <div>
            <span class="badge-type">KARTU PESERTA</span>
        </div>
    </div>

    <!-- Body -->
    <div class="card-body">
        <!-- Photo -->
        <div class="photo-box">
            @if(!empty($p->photo_base64))
                <img src="{{ $p->photo_base64 }}" class="photo-img">
            @else
                <div class="photo-fallback">&#128100;</div>
            @endif
        </div>

        <!-- Name -->
        <div class="participant-name">
            {{ $p->nama }}
        </div>

        <!-- No Peserta -->
        <div>
            <span class="badge-number {{ $isPutri ? 'putri' : '' }}">
                NO : {{ $p->no_peserta ?: sprintf('P-%03d', $p->id) }}
            </span>
        </div>

        <!-- Meta Table -->
        <table class="meta-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="meta-lbl">Cabang</td>
                <td class="meta-sep">:</td>
                <td class="meta-val val-cabang">{{ $p->cabang->nama_cabang ?? '-' }}</td>
            </tr>
            <tr>
                <td class="meta-lbl">Kafilah</td>
                <td class="meta-sep">:</td>
                <td class="meta-val val-utusan">{{ $p->utusan->kecamatan ?? '-' }}</td>
            </tr>
            <tr>
                <td class="meta-lbl">Golongan</td>
                <td class="meta-sep">:</td>
                <td class="meta-val">{{ ucfirst($p->jenis_kelamin ?? '-') }}</td>
            </tr>
            <tr>
                <td class="meta-lbl">TTL</td>
                <td class="meta-sep">:</td>
                <td class="meta-val">{{ $p->tempat_lahir ? $p->tempat_lahir . ', ' . ($p->tgl_lahir ? $p->tgl_lahir->format('d-m-Y') : '-') : '-' }}</td>
            </tr>
        </table>

        <!-- Bottom QR & Signature -->
        <table class="bottom-table" cellpadding="0" cellspacing="0">
            <tr>
                <td width="55" align="left" valign="middle">
                    @if(!empty($p->qr_base64))
                        <img src="{{ $p->qr_base64 }}" width="48" height="48" style="display: block;">
                    @endif
                </td>
                <td align="right" valign="middle" style="text-align: right;">
                    <div class="sig-city">Trenggalek, {{ $tahunAktif }}</div>
                    <div class="sig-title">Panitia Pelaksana MTQ</div>
                    <div style="height: 12px; margin: 1px 0;">
                        @if(!empty($assets['asset_seal']))
                            <img src="{{ $assets['asset_seal'] }}" height="12">
                        @endif
                    </div>
                    <div class="sig-name">LPTQ TRENGGALEK</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer Logos -->
    <div class="card-footer">
        @if(!empty($assets['asset_footer_logos']))
            <img src="{{ $assets['asset_footer_logos'] }}" height="12">
        @else
            <span style="font-size: 4.5pt; color: #94a3b8; font-weight: bold;">LPTQ KABUPATEN TRENGGALEK</span>
        @endif
    </div>
</div>
