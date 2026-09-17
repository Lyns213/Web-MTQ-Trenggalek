<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Peserta MTQ Trenggalek</title>
    <style>
        @page {
            margin: 6mm 6mm 6mm 6mm;
            size: a4 portrait;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #ffffff;
            color: #1e293b;
            font-size: 7pt;
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
            height: 6mm;
        }

        .page-break {
            page-break-after: always;
            clear: both;
        }

        /* Card Container */
        .card {
            width: 100%;
            height: 135mm;
            border: 2.5px solid #581c87;
            border-radius: 8px;
            background-color: #ffffff;
            overflow: hidden;
            position: relative;
        }

        .card.putri {
            border-color: #831843;
        }

        /* Card Header */
        .card-header {
            background-color: #3b0764;
            color: #ffffff;
            padding: 4px 6px 3px 6px;
            text-align: center;
        }

        .card-header.putri {
            background-color: #701a75;
        }

        .header-title-instansi {
            font-size: 5.5pt;
            font-weight: bold;
            color: #fef08a;
            letter-spacing: 0.5px;
            line-height: 1.1;
        }

        .header-title-daerah {
            font-size: 7.5pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 1px;
            line-height: 1.1;
        }

        .header-title-tahun {
            font-size: 5.5pt;
            color: #fde68a;
            line-height: 1.1;
        }

        .badge-type {
            display: inline-block;
            background-color: #d97706;
            color: #ffffff;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1.5px 12px;
            border-radius: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border: 0.75px solid #fef08a;
            margin-top: 2px;
        }

        /* Card Body */
        .card-body {
            padding: 5px 8px 3px 8px;
            text-align: center;
        }

        .photo-box {
            width: 25mm;
            height: 33mm;
            margin: 0 auto;
            border: 2px solid #d97706;
            background-color: #f8fafc;
            border-radius: 4px;
            overflow: hidden;
            text-align: center;
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-fallback {
            font-size: 24pt;
            color: #94a3b8;
            line-height: 33mm;
        }

        .participant-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            margin-top: 4px;
            line-height: 1.15;
            height: 2.3em;
            overflow: hidden;
        }

        .badge-number {
            display: inline-block;
            background-color: #3b0764;
            color: #fde68a;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1.5px 8px;
            border-radius: 3px;
            border: 1px solid #d97706;
            margin-top: 2px;
            letter-spacing: 0.5px;
        }

        .badge-number.putri {
            background-color: #831843;
            border-color: #f472b6;
        }

        /* Meta table */
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 6.2pt;
            margin-top: 4px;
            text-align: left;
        }

        table.meta-table td {
            padding: 1.5px 2px;
            vertical-align: top;
        }

        .meta-lbl {
            width: 26%;
            color: #475569;
            font-weight: bold;
        }

        .meta-sep {
            width: 4%;
            color: #94a3b8;
            text-align: center;
        }

        .meta-val {
            width: 70%;
            color: #0f172a;
            font-weight: bold;
        }

        .val-cabang {
            color: #1e3a8a;
        }

        .val-utusan {
            color: #065f46;
            text-transform: uppercase;
        }

        /* Bottom Row */
        table.bottom-table {
            width: 100%;
            border-collapse: collapse;
            border-top: 1px dashed #cbd5e1;
            margin-top: 4px;
            padding-top: 2px;
        }

        table.bottom-table td {
            vertical-align: middle;
        }

        .sig-city {
            font-size: 4.8pt;
            color: #475569;
        }

        .sig-title {
            font-size: 5.2pt;
            font-weight: bold;
            color: #0f172a;
        }

        .sig-name {
            font-size: 5.2pt;
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }

        /* Footer */
        .card-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5.5mm;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            padding-top: 1mm;
        }
    </style>
</head>
<body>
    @php
        $chunks = $pesertas->chunk(4);
        $tahunAktif = $tahun->tahun ?? date('Y');
    @endphp

    @foreach($chunks as $chunkIndex => $chunk)
        <table class="sheet-table" cellpadding="0" cellspacing="0">
            <tr>
                @php $p1 = $chunk->get(0); @endphp
                <td class="card-cell">
                    @if($p1)
                        @include('cetak-kartu.partials.single-card', ['p' => $p1, 'tahunAktif' => $tahunAktif, 'assets' => $assets])
                    @endif
                </td>

                <td class="spacer-col">&nbsp;</td>

                @php $p2 = $chunk->get(1); @endphp
                <td class="card-cell">
                    @if($p2)
                        @include('cetak-kartu.partials.single-card', ['p' => $p2, 'tahunAktif' => $tahunAktif, 'assets' => $assets])
                    @endif
                </td>
            </tr>

            <tr class="spacer-row">
                <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
                @php $p3 = $chunk->get(2); @endphp
                <td class="card-cell">
                    @if($p3)
                        @include('cetak-kartu.partials.single-card', ['p' => $p3, 'tahunAktif' => $tahunAktif, 'assets' => $assets])
                    @endif
                </td>

                <td class="spacer-col">&nbsp;</td>

                @php $p4 = $chunk->get(3); @endphp
                <td class="card-cell">
                    @if($p4)
                        @include('cetak-kartu.partials.single-card', ['p' => $p4, 'tahunAktif' => $tahunAktif, 'assets' => $assets])
                    @endif
                </td>
            </tr>
        </table>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach
</body>
</html>
