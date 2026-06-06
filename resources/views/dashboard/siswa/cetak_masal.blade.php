<!DOCTYPE html>
<html>

<head>
    <title>Cetak Kartu Kelas</title>
    <style>
        * {
            box-sizing: border-box;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
        }

        /* GRID UNTUK BANYAK KARTU */
        .grid-container {
            display: grid;
            /* Kita pakai ukuran fix 54mm agar tidak melar */
            grid-template-columns: repeat(3, 54mm);
            /* Gap vertikal dipersempit jadi 4mm saja */
            grid-row-gap: 4mm;
            grid-column-gap: 8mm;
            justify-content: center;
            padding-top: 10mm;
        }

        /* CONTAINER KARTU (Ukuran Standar ID Card) */
        .kartu {
            width: 54mm;
            height: 86mm;
            background-color: #ffffff;
            background-image: radial-gradient(#e5e7eb 1.5px, transparent 1.5px);
            background-size: 6px 6px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            page-break-inside: avoid;
        }

        /* AKSESORIS KARTU */
        .top-bar {
            width: 100%;
            height: 6mm;
            background: #1e3a8a;
            margin-bottom: 4mm;
            position: relative;
        }

        .top-bar::after {
            content: '';
            position: absolute;
            bottom: -3mm;
            left: 0;
            width: 50%;
            height: 3mm;
            background: #f59e0b;
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
        }

        .school-title {
            font-size: 8pt;
            font-weight: 900;
            color: #1e3a8a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .qr-frame {
            background: white;
            padding: 2mm;
            border: 1.5px solid #1e3a8a;
            border-radius: 6px;
            margin-bottom: 2mm;
        }

        .student-name {
            font-size: 9pt;
            font-weight: 800;
            color: #111;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 1mm;
            padding: 0 2mm;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .student-meta {
            font-size: 7pt;
            color: #4b5563;
            background: #f3f4f6;
            padding: 1mm 2mm;
            border-radius: 4px;
            margin-top: 1mm;
            font-weight: 600;
        }

        .footer-strip {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 7mm;
            background: #1e3a8a;
            color: white;
            font-size: 6pt;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .scan-instruction {
            font-size: 5pt;
            color: #6b7280;
            position: absolute;
            bottom: 9mm;
        }

        @media print {
            @page {
                size: A4;
                /* Kita kasih margin atas bawah di sini biar tiap halaman baru punya space */
                margin: 10mm 0 !important;
            }

            body {
                background-color: white;
                margin: 0;
                padding: 0;
            }

            .grid-container {
                /* Padding atas dihilangkan karena sudah dihandle oleh @page margin */
                padding: 0 !important;
                /* Gunakan gap yang stabil */
                grid-row-gap: 5mm !important;
                grid-column-gap: 8mm !important;
                display: grid !important;
            }

            .kartu {
                border: 0.5pt solid #ccc;
                /* Mencegah kartu kepotong antar halaman */
                page-break-inside: avoid;
                /* Hilangkan margin-bottom manual agar tidak dobel dengan grid-gap */
                margin-bottom: 0;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="grid-container">
        @foreach($siswa as $s)
        <div class="kartu">
            <div class="top-bar"></div>
            <div class="school-title">SMPN 2 PRIGEN</div>

            <div class="qr-frame">
                {!! QrCode::size(100)->color(30, 58, 138)->generate($s->qr_token) !!}
            </div>

            <div class="student-name">{{ $s->nama_siswa }}</div>

            <div class="student-meta">
                {{ $s->nama_kelas }} &bull; {{ $s->nisn }}
            </div>

            <div class="scan-instruction">SCAN UNTUK PRESENSI</div>
            <div class="footer-strip">KARTU IDENTITAS SISWA</div>
        </div>
        @endforeach
    </div>

</body>

</html>