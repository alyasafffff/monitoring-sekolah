@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="fw-bold text-danger mb-4">Laporan Pelanggaran (Alpha)</h2>

    {{-- Card Filter --}}
    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('bk.laporan.alpha') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Pilih Kelas</label>
                    <select name="kelas_id" class="form-select">
                        <option value="">Semua Kelas</option>
                        @foreach($daftarKelas as $k)
                        <option value="{{ $k->id }}" {{ $selectedKelas == $k->id ? 'selected' : '' }}>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control" value="{{ $tglMulai }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control" value="{{ $tglSelesai }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-danger w-100"><i class="fas fa-search me-2"></i>Filter Data</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data --}}
    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-light text-dark">
                    <tr>
                        <th>Tanggal</th>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Mata Pelajaran</th>
                        <th>Waktu Pelajaran</th> {{-- Kolom baru hasil gabungan --}}
                        <th>Guru Mapel</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataAlpha as $d)
                    <tr>
                        {{-- 1. Tanggal --}}
                        <td class="small">{{ \Carbon\Carbon::parse($d->tanggal)->translatedFormat('d/m/Y') }}</td>

                        {{-- 2. Nama Siswa --}}
                        <td class="fw-bold text-danger">{{ $d->nama_siswa }}</td>

                        {{-- 3. Kelas --}}
                        <td><span class="badge bg-secondary-subtle text-secondary border">{{ $d->nama_kelas }}</span></td>

                        {{-- 4. Mata Pelajaran --}}
                        <td>{{ $d->nama_mapel }}</td>

                        {{-- 5. Waktu (Sudah digabung dari Controller: MIN jam_mulai - MAX jam_selesai) --}}
                        <td>
                            <small class="badge bg-light text-dark border fw-normal">
                                {{ substr($d->jam_mulai_gabung, 0, 5) }} - {{ substr($d->jam_selesai_gabung, 0, 5) }}
                            </small>
                        </td>

                        {{-- 6. Nama Guru --}}
                        <td class="text-muted"><small>{{ $d->nama_guru }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        {{-- Colspan ganti jadi 6 karena kolom aksi sudah dihapus dan diganti kolom waktu --}}
                        <td colspan="6" class="text-center py-5 text-muted small">
                            <i class="fas fa-search fa-2x mb-3 d-block opacity-25"></i>
                            Tidak ada data Alpha ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection