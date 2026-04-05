@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold text-danger mb-4">Laporan Pelanggaran (Alpha)</h2>

    {{-- Card Filter --}}
    <div class="card shadow border-0 mb-4 d-print-none"> {{-- d-print-none agar filter tidak ikut tercetak --}}
        <div class="card-body">
            <form action="{{ route('bk.laporan.alpha') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Kelas</label>
                    <select name="kelas_id" class="form-select border-danger-subtle">
                        <option value="">Semua Kelas</option>
                        @foreach($daftarKelas as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control border-danger-subtle" value="{{ $tglMulai }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control border-danger-subtle" value="{{ $tglSelesai }}">
                </div>
                
                {{-- Area Tombol Aksi --}}
                <div class="col-md-5 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-danger flex-grow-1 fw-bold">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                    
                    {{-- Tombol Export Excel --}}
                    <a href="{{ route('bk.laporan.export', request()->all()) }}" class="btn btn-success fw-bold">
                        <i class="fas fa-file-excel me-2"></i>Excel
                    </a>

                    {{-- Tombol Print (PDF) --}}
                    <button type="button" onclick="window.print()" class="btn btn-dark fw-bold">
                        <i class="fas fa-print me-2"></i>Cetak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card shadow border-0">
        <div class="card-body">
            {{-- Header Laporan saat di-Print --}}
            <div class="d-none d-print-block text-center mb-4">
                <h3 class="fw-bold">LAPORAN PRESENSI SISWA (ALPHA)</h3>
                <p class="mb-0">Periode: {{ \Carbon\Carbon::parse($tglMulai)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tglSelesai)->format('d/m/Y') }}</p>
                <hr>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light text-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Waktu Pelajaran</th>
                            <th>Guru Mapel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataAlpha as $d)
                        <tr>
                            <td class="small">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d/m/Y') }}</td>
                            <td class="fw-bold text-danger">{{ $d->nama_siswa }}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary border">{{ $d->nama_kelas }}</span></td>
                            <td>{{ $d->nama_mapel }}</td>
                            <td>
                                <small class="badge bg-light text-dark border fw-normal">
                                    {{ substr($d->jam_mulai_gabung, 0, 5) }} - {{ substr($d->jam_selesai_gabung, 0, 5) }}
                                </small>
                            </td>
                            <td class="text-muted"><small>{{ $d->nama_guru }}</small></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted small">
                                <i class="fas fa-search fa-2x mb-3 d-block opacity-25"></i>
                                Tidak ada data Alpha ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer Tanda Tangan saat di-Print --}}
            <div class="d-none d-print-block mt-5">
                <div class="row">
                    <div class="col-8"></div>
                    <div class="col-4 text-center">
                        <p class="mb-5">Malang, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>Guru BK,</p>
                        <br><br>
                        <p class="fw-bold">( ____________________ )</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- CSS Khusus Print --}}
<style>
@media print {
    .main-container { padding: 0 !important; }
    .card { box-shadow: none !important; border: none !important; }
    body { background-color: white !important; }
    .table-light { background-color: #f8f9fa !important; }
    /* Menghilangkan elemen dashboard saat print */
    #sidebar, .top-navbar, .d-print-none { display: none !important; }
}
</style>
@endsection