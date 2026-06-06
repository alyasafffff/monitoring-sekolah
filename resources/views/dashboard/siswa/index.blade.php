@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Data Siswa</h2>
        <a href="{{ route('siswa.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Siswa
        </a>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body">
            <form action="{{ route('siswa.index') }}" method="GET" class="card shadow-sm border-0 mb-4 d-print-none" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <div class="row g-3">
                        {{-- 1. Filter Kelas --}}
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Filter Kelas</label>
                            <select name="filter_kelas" class="form-select border-primary shadow-sm" onchange="this.form.submit()">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($list_kelas as $k)
                                <option value="{{ $k->id }}" {{ request('filter_kelas') == $k->id ? 'selected' : '' }}>
                                    Kelas {{ $k->nama_kelas }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 2. Input Pencarian --}}
                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Cari Nama / NISN</label>
                            <div class="input-group shadow-sm">
                                <input type="text" name="q" class="form-control border-primary" placeholder="Masukkan kata kunci..." value="{{ request('q') }}">
                                <button class="btn btn-primary px-3" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        {{-- 3. Area Tombol Aksi --}}
                        <div class="col-md-5">
                            {{-- Label kosong agar tinggi kolom tombol sama dengan kolom input --}}
                            <label class="form-label d-block mb-2">&nbsp;</label>
                            <div class="d-flex gap-2">
                                @if(request('filter_kelas'))
                                <a href="{{ route('siswa.cetak_kelas', request('filter_kelas')) }}" target="_blank" class="btn btn-success fw-bold shadow-sm flex-grow-1">
                                    <i class="fas fa-print me-1"></i> Cetak Kartu
                                </a>
                                @endif

                                @if(request('filter_kelas') || request('q'))
                                <a href="{{ route('siswa.index') }}" class="btn btn-outline-secondary fw-bold px-3">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="50">No</th>
                            <th>Info Siswa</th>
                            <th>Kelas</th>
                            <th>QR Code</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa as $index => $s)
                        <tr>
                            {{-- Modifikasi No agar berlanjut di halaman berikutnya --}}
                            <td class="view-detail" data-id="{{ $s->id }}" style="cursor: pointer;">
                                {{ ($siswa->currentPage() - 1) * $siswa->perPage() + $index + 1 }}
                            </td>
                            <td class="view-detail" data-id="{{ $s->id }}" style="cursor: pointer;">
                                <div class="fw-bold text-dark">{{ $s->nama_siswa }}</div>
                                <small class="text-muted">NISN: {{ $s->nisn }}</small>
                            </td>
                            <td class="view-detail" data-id="{{ $s->id }}" style="cursor: pointer;">
                                <span class="badge bg-info text-dark">Kelas {{ $s->nama_kelas }}</span>
                            </td>
                            <td class="view-detail text-center" data-id="{{ $s->id }}" style="cursor: pointer;">
                                {!! QrCode::size(40)->generate($s->qr_token) !!}
                            </td>

                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('siswa.edit', $s->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('siswa.cetak', $s->id) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Cetak">
                                        <i class="fas fa-id-card"></i>
                                    </a>
                                    <form action="{{ route('siswa.destroy', $s->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus siswa ini?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $siswa->firstItem() ?? 0 }}</strong> - <strong>{{ $siswa->lastItem() ?? 0 }}</strong>
                    dari <strong>{{ $siswa->total() }}</strong> Siswa
                </div>
                <div class="pagination-sm">
                    {{ $siswa->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL --}}
@foreach($siswa as $s)
<div class="modal fade" id="detailSiswa{{ $s->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">Detail Data Siswa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3 text-center">
                <div class="mb-4 p-3 bg-light rounded shadow-sm d-inline-block">
                    {!! QrCode::size(150)->generate($s->qr_token) !!}
                    <div class="mt-2 small fw-bold text-secondary">{{ $s->qr_token }}</div>
                </div>

                <div class="list-group list-group-flush text-start">
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block small fw-bold">NAMA LENGKAP</small>
                        <span class="fw-bold text-dark">{{ $s->nama_siswa }}</span>
                    </div>
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block small fw-bold">NISN / KELAS</small>
                        <span>{{ $s->nisn }} / <strong>Kelas {{ $s->nama_kelas }}</strong></span>
                    </div>
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block small fw-bold">JENIS KELAMIN</small>
                        <span>{{ $s->jenis_kelamin == 'L' ? 'Laki-laki' : ($s->jenis_kelamin == 'P' ? 'Perempuan' : '-') }}</span>
                    </div>
                    <div class="list-group-item px-0">
                        <small class="text-muted d-block small fw-bold">NO. HP ORANG TUA</small>
                        <span class="text-primary fw-bold">{{ $s->no_hp_ortu ?? '-' }}</span>
                    </div>
                    <div class="list-group-item px-0 border-0">
                        <small class="text-muted d-block small fw-bold">ALAMAT LENGKAP</small>
                        <span>{{ $s->alamat ?? '-' }}</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0">
                <button type="button" class="btn btn-secondary fw-bold shadow-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const detailCells = document.querySelectorAll('.view-detail');
        detailCells.forEach(cell => {
            cell.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const modalId = 'detailSiswa' + id;
                const myModal = new bootstrap.Modal(document.getElementById(modalId));
                myModal.show();
            });
        });
    });
</script>
@endsection