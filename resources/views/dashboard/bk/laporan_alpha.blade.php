@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-danger m-0">Laporan Pelanggaran (Alpha)</h2>
            <p class="text-muted small m-0">Data ketidakhadiran siswa yang terdeteksi otomatis.</p>
        </div>
        {{-- Tombol Cetak Membuka Tab Baru --}}
        <a href="{{ route('bk.laporan.print', request()->all()) }}" target="_blank" class="btn btn-dark fw-bold px-4 shadow-sm">
            <i class="fas fa-print me-2"></i>Cetak PDF
        </a>
    </div>

    {{-- Card Filter --}}
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('bk.laporan.alpha') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Kelas</label>
                    <select name="kelas_id" class="form-select border-secondary-subtle">
                        <option value="">Semua Kelas</option>
                        @foreach($daftarKelas as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Mulai Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $tglMulai }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="{{ $tglSelesai }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-danger w-100 fw-bold">Filter Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tampilan Tabel di Dashboard --}}
    <div class="card shadow-sm border-0" style="border-radius: 12px;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">No</th>
                            <th>Tanggal</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Waktu</th>
                            <th>Guru</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dataAlpha as $index => $d)
                        <tr>
                            <td class="text-center text-muted small">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($d->tanggal)->format('d/m/Y') }}</td>
                            <td class="fw-bold">{{ $d->nama_siswa }}</td>
                            <td><span class="badge bg-secondary-subtle text-secondary border">{{ $d->nama_kelas }}</span></td>
                            <td>{{ $d->nama_mapel }}</td>
                            <td class="small text-muted">{{ substr($d->jam_mulai_gabung, 0, 5) }} - {{ substr($d->jam_selesai_gabung, 0, 5) }}</td>
                            <td class="small italic">{{ $d->nama_guru }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">Data tidak ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection