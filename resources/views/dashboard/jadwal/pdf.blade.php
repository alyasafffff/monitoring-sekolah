<!DOCTYPE html>
<html>

<head>
    <title>Jadwal Pelajaran Kelas {{ $kelas_terpilih->nama_kelas }}</title>
    <style>
        @page {
            margin: 1cm;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            table-layout: auto;
        }

        th,
        td {
            border: 1px solid #444;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
        }

        .col-jam {
            width: 30px;
            background-color: #f9f9f9;
            font-weight: bold;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .jam-text {
            color: #666;
            font-size: 8px;
            display: block;
            margin-top: 2px;
            border-top: 0.5px solid #eee;
            padding-top: 2px;
        }

        .mapel-text {
            font-weight: bold;
            font-size: 9px;
            display: block;
            margin-bottom: 2px;
        }

        .guru-text {
            font-style: italic;
            color: #555;
            font-size: 8px;
        }

        .istirahat {
            background-color: #fff3cd;
            font-weight: bold;
            color: #856404;
        }

        .empty {
            color: #ccc;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        .jam-text {
            color: #666;
            font-size: 8px;
            display: block;
            margin-top: 2px;
            border-top: 0.5px solid #eee;
            padding-top: 2px;
        }

        .mapel-text {
            font-weight: bold;
            font-size: 9px;
            display: block;
            margin-bottom: 2px;
        }

        .guru-text {
            font-style: italic;
            color: #555;
            font-size: 8px;
        }

        .istirahat {
            background-color: #fff3cd;
            font-weight: bold;
            color: #856404;
        }

        .empty {
            color: #ccc;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2 style="margin: 0;">JADWAL PELAJARAN SMPN 2 PRIGEN</h2>
        <h3 style="margin: 5px 0;">TAHUN AJARAN 2025/2026</h3>
        <p style="margin: 0;">Kelas: <strong>{{ $kelas_terpilih->nama_kelas }}</strong> | Wali Kelas: <strong>{{ $kelas_terpilih->nama_wali ?? '-' }}</strong></p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-jam">Jam</th>
                <th>Senin</th>
                <th>Selasa</th>
                <th>Rabu</th>
                <th>Kamis</th>
                <th>Jumat</th>
                <th>Sabtu</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 0; $i <= $maxJamKe; $i++)
                <tr>
                <td style="background-color: #f9f9f9; font-weight: bold;">{{ $i }}</td>
                @foreach(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'] as $hari)
                @php
                $grup = ($hari == 'Senin') ? 'Senin' : (($hari == 'Jumat') ? 'Jumat' : 'Reguler');
                $waktu = $configJam[$grup][$i] ?? null;
                $jadwal = ($waktu) ? ($jadwalMatrix[$hari][$waktu['mulai']] ?? null) : null;
                @endphp

                @if($waktu && $waktu['tipe'] == 'istirahat')
                <td class="istirahat">
                    <div>ISTIRAHAT</div>
                    <span class="jam-text">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</span>
                </td>
                @elseif($jadwal)
                <td>
                    <span class="mapel-text">{{ $jadwal->nama_mapel ?? $jadwal->nama_kegiatan }}</span>
                    <span class="guru-text">{{ Str::limit($jadwal->nama_guru, 20) }}</span>
                    <span class="jam-text">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</span>
                </td>
                @elseif($waktu)
                {{-- Slot ada di config tapi belum diisi jadwal --}}
                <td>
                    <span class="empty">-</span>
                    <span class="jam-text">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</span>
                </td>
                @else
                {{-- Slot tidak terdaftar di config hari tersebut --}}
                <td style="background-color: #f0f0f0;"></td>
                @endif
                @endforeach
                </tr>
                @endfor
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 8px; color: #888;">
        * Dicetak secara otomatis melalui Sistem Monitoring Sekolah (SIMONS) pada {{ now()->translatedFormat('d F Y H:i') }}
    </div>
</body>

</html>