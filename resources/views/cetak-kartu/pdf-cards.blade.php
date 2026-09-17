<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu {{ ucfirst(str_replace('_', ' ', $role ?? 'MTQ')) }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #ffffff;
            color: #000000;
        }

        @if(!empty($isSingle))
            /* ===== SINGLE CARD MODE (A6: 105mm x 148mm) ===== */
            @page {
                margin: 0;
                size: 105mm 148mm;
            }

            .card-box-single {
                position: relative;
                width: 105mm;
                height: 148mm;
                overflow: hidden;
            }

            .bg-img-single {
                position: absolute;
                top: 0;
                left: 0;
                width: 105mm;
                height: 148mm;
                z-index: 1;
            }

            .subrole-wrap-single {
                position: absolute;
                top: 57mm;
                left: 6mm;
                right: 6mm;
                text-align: center;
                z-index: 5;
            }

            .subrole-text-single {
                font-size: 9.5pt;
                font-weight: 800;
                color: #000000;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .photo-wrap-single {
                position: absolute;
                left: 37.5mm;
                top: 71mm;
                width: 30mm;
                height: 39.5mm;
                overflow: hidden;
                background-color: #ffffff;
                text-align: center;
                z-index: 3;
            }

            .photo-img-single {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .photo-ph-single {
                width: 100%;
                height: 100%;
                line-height: 39.5mm;
                font-size: 26pt;
                color: #94a3b8;
                text-align: center;
                background-color: #f1f5f9;
            }

            .number-wrap-single {
                position: absolute;
                top: 116.5mm;
                left: 4mm;
                right: 4mm;
                text-align: center;
                z-index: 5;
            }

            .number-text-single {
                font-size: 16pt;
                font-weight: 900;
                color: #000000;
                letter-spacing: 1px;
                line-height: 1;
            }

            .name-wrap-single-peserta {
                position: absolute;
                top: 125.5mm;
                left: 4mm;
                right: 4mm;
                text-align: center;
                z-index: 5;
                height: 12mm;
                overflow: hidden;
            }

            .name-wrap-single-official {
                position: absolute;
                top: 121mm;
                left: 4mm;
                right: 4mm;
                text-align: center;
                z-index: 5;
                height: 15mm;
                overflow: hidden;
            }

            .name-text-single {
                font-size: 10pt;
                font-weight: 800;
                color: #000000;
                text-transform: uppercase;
                line-height: 1.2;
            }

        @else
            /* ===== BULK CARDS MODE (A4: 4 Cards per Page with Cut Lines) ===== */
            @page {
                margin: 8mm 6mm 8mm 6mm;
                size: a4 portrait;
            }

            table.sheet-table {
                width: 100%;
                border-collapse: collapse;
                table-layout: fixed;
            }

            td.card-cell {
                width: 49%;
                vertical-align: top;
                padding: 0;
            }

            td.spacer-col {
                width: 2%;
            }

            tr.spacer-row td {
                height: 10mm;
            }

            .page-break {
                page-break-after: always;
                clear: both;
            }

            .card-box {
                position: relative;
                width: 92mm;
                height: 126mm;
                overflow: hidden;
                margin: 0 auto;
                border: 0.5px dashed #94a3b8;
            }

            .bg-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 92mm;
                height: 126mm;
                z-index: 1;
            }

            .subrole-wrap {
                position: absolute;
                top: 48.2mm;
                left: 5mm;
                right: 5mm;
                text-align: center;
                z-index: 5;
                height: 6.5mm;
                overflow: hidden;
            }

            .subrole-text {
                font-size: 8pt;
                font-weight: 800;
                color: #000000;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                line-height: 1.1;
            }

            .photo-wrap {
                position: absolute;
                left: 32.9mm;
                top: 60.5mm;
                width: 26.3mm;
                height: 33.9mm;
                overflow: hidden;
                background-color: #ffffff;
                text-align: center;
                z-index: 3;
            }

            .photo-img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .photo-ph {
                width: 100%;
                height: 100%;
                line-height: 33.9mm;
                font-size: 22pt;
                color: #94a3b8;
                text-align: center;
                background-color: #f1f5f9;
            }

            .number-wrap {
                position: absolute;
                top: 99.2mm;
                left: 4mm;
                right: 4mm;
                text-align: center;
                z-index: 5;
            }

            .number-text {
                font-size: 13pt;
                font-weight: 900;
                color: #000000;
                letter-spacing: 0.8px;
                line-height: 1;
            }

            .name-wrap-peserta {
                position: absolute;
                top: 106.8mm;
                left: 4mm;
                right: 4mm;
                text-align: center;
                z-index: 5;
                height: 8.5mm;
                overflow: hidden;
            }

            .name-wrap-official {
                position: absolute;
                top: 102.5mm;
                left: 4mm;
                right: 4mm;
                text-align: center;
                z-index: 5;
                height: 11mm;
                overflow: hidden;
            }

            .name-text {
                font-size: 8.5pt;
                font-weight: 800;
                color: #000000;
                text-transform: uppercase;
                line-height: 1.15;
            }
        @endif
    </style>
</head>
<body>
    @if(!empty($isSingle))
        @php
            $item = $cards->first();
            $role = $item['role'] ?? 'peserta';
            $bg = $bgTemplates[$role] ?? $bgTemplates['peserta'];
        @endphp

        <div class="card-box-single">
            <!-- Background Image -->
            <img src="{{ $bg }}" class="bg-img-single">

            <!-- Subrole (e.g. TARTIL PUTRA) -->
            @if(!empty($item['subrole']))
                <div class="subrole-wrap-single">
                    <span class="subrole-text-single">{{ $item['subrole'] }}</span>
                </div>
            @endif

            <!-- Pasfoto -->
            <div class="photo-wrap-single">
                @if(!empty($item['photo_base64']))
                    <img src="{{ $item['photo_base64'] }}" class="photo-img-single">
                @else
                    <div class="photo-ph-single">&#128100;</div>
                @endif
            </div>

            @if($role === 'peserta')
                <!-- Nomor Peserta -->
                <div class="number-wrap-single">
                    <span class="number-text-single">{{ $item['nomor'] ?? '-' }}</span>
                </div>

                <!-- Nama Peserta -->
                <div class="name-wrap-single-peserta">
                    <span class="name-text-single">{{ $item['nama'] ?? '-' }}</span>
                </div>
            @else
                <!-- Nama Official -->
                <div class="name-wrap-single-official">
                    <span class="name-text-single">{{ $item['nama'] ?? '-' }}</span>
                </div>
            @endif
        </div>

    @else
        @php
            $chunks = $cards->chunk(4);
        @endphp

        @foreach($chunks as $chunkIndex => $chunk)
            <table class="sheet-table" cellpadding="0" cellspacing="0">
                <tr>
                    @php $c1 = $chunk->get(0); @endphp
                    <td class="card-cell">
                        @if($c1)
                            @include('cetak-kartu.partials.card-item', ['item' => $c1, 'bgTemplates' => $bgTemplates])
                        @endif
                    </td>

                    <td class="spacer-col">&nbsp;</td>

                    @php $c2 = $chunk->get(1); @endphp
                    <td class="card-cell">
                        @if($c2)
                            @include('cetak-kartu.partials.card-item', ['item' => $c2, 'bgTemplates' => $bgTemplates])
                        @endif
                    </td>
                </tr>

                <tr class="spacer-row">
                    <td colspan="3">&nbsp;</td>
                </tr>

                <tr>
                    @php $c3 = $chunk->get(2); @endphp
                    <td class="card-cell">
                        @if($c3)
                            @include('cetak-kartu.partials.card-item', ['item' => $c3, 'bgTemplates' => $bgTemplates])
                        @endif
                    </td>

                    <td class="spacer-col">&nbsp;</td>

                    @php $c4 = $chunk->get(3); @endphp
                    <td class="card-cell">
                        @if($c4)
                            @include('cetak-kartu.partials.card-item', ['item' => $c4, 'bgTemplates' => $bgTemplates])
                        @endif
                    </td>
                </tr>
            </table>

            @if(!$loop->last)
                <div class="page-break"></div>
            @endif
        @endforeach
    @endif
</body>
</html>
