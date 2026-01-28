<!DOCTYPE html>
<html>
<head>
    <title>Kartu Pelajar - {{ $siswa->nama_siswa }}</title>
    <style>
        /* RESET & DASAR */
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

        .page-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* CONTAINER KARTU */
        .kartu {
            width: 54mm;
            height: 86mm;
            background-color: #ffffff;
            /* Pola titik-titik halus agar tidak polos */
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
        }

        /* AKSEN DEKORATIF */
        .top-bar {
            width: 100%;
            height: 6mm;
            background: #1e3a8a; /* Navy Blue */
            margin-bottom: 4mm;
            position: relative;
        }
        
        /* Aksen kuning miring */
        .top-bar::after {
            content: '';
            position: absolute;
            bottom: -3mm;
            left: 0;
            width: 50%;
            height: 3mm;
            background: #f59e0b; /* Amber/Emas */
            clip-path: polygon(0 0, 100% 0, 85% 100%, 0% 100%);
        }

        /* KONTEN UTAMA */
        .school-title {
            font-size: 9pt;
            font-weight: 900;
            color: #1e3a8a;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .qr-frame {
            background: white;
            padding: 3mm;
            border: 2px solid #1e3a8a;
            border-radius: 8px;
            margin-bottom: 3mm;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .student-name {
            font-size: 11pt;
            font-weight: 800;
            color: #111;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 1mm;
            padding: 0 2mm;
            /* Batasi max 2 baris nama */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .student-meta {
            font-size: 8pt;
            color: #4b5563;
            background: #f3f4f6;
            padding: 1mm 3mm;
            border-radius: 4px;
            margin-top: 1mm;
            font-weight: 600;
        }

        /* FOOTER */
        .footer-strip {
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 8mm;
            background: #1e3a8a;
            color: white;
            font-size: 6pt;
            display: flex;
            justify-content: center;
            align-items: center;
            letter-spacing: 0.5px;
        }

        .scan-instruction {
            font-size: 6pt;
            color: #6b7280;
            position: absolute;
            bottom: 10mm;
        }

        /* PRINT SETTINGS */
        @media print {
            @page {
                size: A4;
                margin: 0;
            }
            body {
                background-color: white;
            }
            .page-container {
                display: block;
                padding-top: 10mm;
                padding-left: 10mm;
            }
            .kartu {
                border: 1px solid #000;
                page-break-inside: avoid;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body onload="window.print()">
    
    <div class="page-container">
        <div class="kartu">
            <div class="top-bar"></div>

            <div class="school-title">SMPN 2 PRIGEN</div>

            <div class="qr-frame">
                {!! QrCode::size(110)->color(30, 58, 138)->generate($siswa->qr_token) !!}
            </div>

            <div class="student-name">
                {{ $siswa->nama_siswa }}
            </div>

            <div class="student-meta">
                {{ $siswa->nama_kelas }} &bull; {{ $siswa->nisn }}
            </div>

            <div class="scan-instruction">
                SCAN UNTUK PRESENSI
            </div>

            <div class="footer-strip">
                KARTU IDENTITAS SISWA
            </div>
        </div>
    </div>

</body>
</html>