@php
    // Kelompokkan listTanggal berdasarkan Nama Bulan
    $groupedDates = collect($listTanggal)->groupBy(function($date) {
        return \Carbon\Carbon::parse($date)->translatedFormat('F Y');
    });
    
    // Hitung total kolom tambahan di kanan:
    // 4 Kolom Total (H, S, I, A) + 4 Kolom Persentase (%H, %S, %I, %A) = 8 Kolom
    $kolomTambahan = 8; 
@endphp

<table>
    <thead>
        <tr>
            <th colspan="{{ count($listTanggal) + 3 + $kolomTambahan }}" style="text-align: center; font-size: 14pt; font-weight: bold;">
                REKAP PRESENSI SISWA  - {{ $infoKelas->nama_kelas ?? '' }}
            </th>
        </tr>

        <tr>
            <th rowspan="2" style="background-color: #f2f2f2; border: 1px solid #000; text-align: center; vertical-align: middle;">No</th>
            <th rowspan="2" style="background-color: #f2f2f2; border: 1px solid #000; text-align: center; vertical-align: middle;">NISN</th>
            <th rowspan="2" style="background-color: #f2f2f2; border: 1px solid #000; text-align: center; vertical-align: middle;">Nama Siswa</th>
            
            @foreach($groupedDates as $bulan => $dates)
                <th colspan="{{ count($dates) }}" style="background-color: #e2efda; border: 1px solid #000; text-align: center;">
                    {{ strtoupper($bulan) }}
                </th>
            @endforeach
            
            <th colspan="4" style="background-color: #f2f2f2; border: 1px solid #000; text-align: center;">Total Kehadiran</th>
            <th colspan="4" style="background-color: #d9e1f2; border: 1px solid #000; text-align: center;">Persentase Kehadiran</th>
        </tr>

        <tr>
            @foreach($listTanggal as $tgl)
                <th style="background-color: #f2f2f2; border: 1px solid #000; text-align: center;">
                    {{ \Carbon\Carbon::parse($tgl)->format('d') }}
                </th>
            @endforeach
            <th style="background-color: #d1e7dd; border: 1px solid #000;">H</th>
            <th style="background-color: #fff3cd; border: 1px solid #000;">S</th>
            <th style="background-color: #cff4fc; border: 1px solid #000;">I</th>
            <th style="background-color: #f8d7da; border: 1px solid #000;">A</th>
            
            <th style="background-color: #d9e1f2; border: 1px solid #000;">% H</th>
            <th style="background-color: #d9e1f2; border: 1px solid #000;">% S</th>
            <th style="background-color: #d9e1f2; border: 1px solid #000;">% I</th>
            <th style="background-color: #d9e1f2; border: 1px solid #000;">% A</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dataSiswa as $siswa)
        <tr>
            <td style="border: 1px solid #000; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #000;">{{ $siswa['nisn'] }}</td>
            <td style="border: 1px solid #000;">{{ $siswa['nama'] }}</td>
            
            @foreach($siswa['grid'] as $status)
                <td style="border: 1px solid #000; text-align: center; @if($status == 'A') color: red; @endif">
                    {{ $status != '-' ? $status : '' }}
                </td>
            @endforeach

            <td style="border: 1px solid #000; text-align: center; background-color: #f9f9f9;">{{ $siswa['total']['H'] }}</td>
            <td style="border: 1px solid #000; text-align: center; background-color: #f9f9f9;">{{ $siswa['total']['S'] }}</td>
            <td style="border: 1px solid #000; text-align: center; background-color: #f9f9f9;">{{ $siswa['total']['I'] }}</td>
            <td style="border: 1px solid #000; text-align: center; background-color: #f9f9f9; color: red;">{{ $siswa['total']['A'] }}</td>

            <td style="border: 1px solid #000; text-align: center;">{{ $siswa['persen']['H'] }}%</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $siswa['persen']['S'] }}%</td>
            <td style="border: 1px solid #000; text-align: center;">{{ $siswa['persen']['I'] }}%</td>
            <td style="border: 1px solid #000; text-align: center; color: red;">{{ $siswa['persen']['A'] }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>