@extends('layouts.app') {{-- Sesuaikan dengan nama layout admin kamu --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Rekapitulasi Presensi Siswa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('rekap.index') }}" method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Pilih Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                <option value="{{ $kelas->id }}" {{ $selectedKelas == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Dari Bulan</label>
                            <select name="bulan_awal" id="bulan_awal" class="form-select bulan-select">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulanAwal == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Sampai Bulan</label>
                            <select name="bulan_akhir" id="bulan_akhir" class="form-select bulan-select">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulanAkhir == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tahun</label>
                            <select name="tahun" id="tahun_filter" class="form-select">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filter Data
                            </button>
                        </div>
                    </form>
                    @if($selectedKelas)
                    <div class="d-flex justify-content-between align-items-center mb-4 p-2 bg-white rounded shadow-sm border">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">
                                <i class="fas fa- chalkboard-teacher me-2 text-primary"></i>Data Kelas:
                                <span class="badge bg-primary">{{ $daftarKelas->where('id', $selectedKelas)->first()->nama_kelas }}</span>
                            </h6>
                            <small class="text-muted">
                                Periode: {{ date('F', mktime(0, 0, 0, $bulanAwal, 1)) }} s/d {{ date('F Y', mktime(0, 0, 0, $bulanAkhir, 1, $selectedTahun)) }}
                            </small>
                        </div>

                        <div class="btn-group">
                            {{-- Tombol Langsung Export sesuai Filter --}}
                            <a href="{{ route('rekap.export', [
        'tipe' => 'bulanan', 
        'kelas_id' => $selectedKelas, 
        'bulan_awal' => $bulanAwal, 
        'bulan_akhir' => $bulanAkhir, 
        'tahun' => $selectedTahun
    ]) }}" class="btn btn-success shadow-sm">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th rowspan="2" class="align-middle">No</th>
                                    <th rowspan="2" class="align-middle">Nama Siswa</th>
                                    <th colspan="{{ count($listTanggal) }}">Tanggal / Pertemuan</th>
                                    <th colspan="5">Total</th>
                                </tr>
                                <tr>
                                    @foreach($listTanggal as $tgl)
                                    <th style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}</th>
                                    @endforeach
                                    <th>H</th>
                                    <th>S</th>
                                    <th>I</th>
                                    <th>A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataSiswa as $siswa)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-nowrap">{{ $siswa['nama'] }}</td>

                                    @foreach($siswa['grid'] as $status)
                                    <td class="text-center fw-bold @if($status == 'A') text-danger @elseif($status == 'H') text-success @endif">
                                        {{ $status }}
                                    </td>
                                    @endforeach

                                    <td class="text-center bg-light">{{ $siswa['total']['H'] }}</td>
                                    <td class="text-center bg-light">{{ $siswa['total']['S'] }}</td>
                                    <td class="text-center bg-light">{{ $siswa['total']['I'] }}</td>
                                    <td class="text-center bg-light text-danger">{{ $siswa['total']['A'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <img src="https://illustrations.popsy.co/blue/searching.svg" style="height: 150px;" alt="search">
                        <p class="mt-3 text-secondary">Silahkan pilih filter kelas untuk menampilkan data rekapitulasi.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const bulanSelects = document.querySelectorAll('.bulan-select');
        const tahunSelect = document.getElementById('tahun_filter');
        const currentMonth = new Date().getMonth() + 1; // 1-12
        const currentYear = new Date().getFullYear();

        function lockFutureMonths() {
            const selectedYear = parseInt(tahunSelect.value);

            bulanSelects.forEach(select => {
                Array.from(select.options).forEach(option => {
                    const optionMonth = parseInt(option.value);

                    // Disable bulan jika tahun yang dipilih adalah tahun sekarang DAN bulan > bulan sekarang
                    if (selectedYear === currentYear && optionMonth > currentMonth) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        tahunSelect.addEventListener('change', lockFutureMonths);
        lockFutureMonths(); // Jalankan saat load
    });
</script>
@endsection