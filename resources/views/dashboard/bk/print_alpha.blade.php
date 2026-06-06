<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Alpha - SMPN 2 PRIGEN</title>
    <style>
        @page { size: A4; margin: 1.5cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
            color: #000;
            background: #fff;
            margin: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 16px; text-transform: uppercase; }
        .header h3 { margin: 5px 0; font-size: 14px; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px 6px;
            vertical-align: middle;
        }
        th {
            background-color: #f2f2f2;
            text-transform: uppercase;
            font-weight: bold;
        }
        .text-start { text-align: left; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        .italic { font-style: italic; }

        .footer-ttd {
            margin-top: 40px;
            width: 100%;
        }
        .footer-ttd td { border: none; }
        
        .timestamp {
            margin-top: 30px;
            font-size: 8px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body onload="window.print()"> {{-- Otomatis muncul dialog print saat tab dibuka --}}
    
    <div class="header">
        <h2>DAFTAR SISWA ALPHA</h2>
        <h3>SMP NEGERI 2 PRIGEN</h3>
        <p>Periode: {{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30">No</th>
                <th width="80">Tanggal</th>
                <th>Nama Siswa</th>
                <th width="60">Kelas</th>
                <th>Mata Pelajaran</th>
                <th width="100">Waktu</th>
                <th>Guru Pengampu</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dataAlpha as $index => $d)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                <td class="fw-bold text-start">{{ $d->nama_siswa }}</td>
                <td class="text-center">{{ $d->nama_kelas }}</td>
                <td class="text-start">{{ $d->nama_mapel }}</td>
                <td class="text-center">
                    {{ substr($d->jam_mulai_gabung, 0, 5) }} - {{ substr($d->jam_selesai_gabung, 0, 5) }}
                </td>
                <td class="italic text-start">{{ $d->nama_guru }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="footer-ttd">
        <tr>
            <td width="65%"></td>
            <td class="text-center">
                <p>Prigen, {{ now()->translatedFormat('d F Y') }}</p>
                <p style="margin-bottom: 60px;">Guru Bimbingan Konseling,</p>
                <p class="fw-bold"><u>( ____________________ )</u></p>
                <p>NIP. ...........................</p>
            </td>
        </tr>
    </table>

    <div class="timestamp">
        * Dicetak secara otomatis melalui Sistem Monitoring Sekolah (SIMONS) pada {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>