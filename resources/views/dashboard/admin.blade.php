@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-0">Dashboard Administrator</h2>
            <p class="text-muted">Selamat datang kembali, <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border p-2">
                <i class="fas fa-calendar-alt me-1 text-primary"></i> 
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 border-0 border-start border-primary border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Siswa Terdaftar</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $totalSiswa }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 border-0 border-start border-success border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Tenaga Pengajar</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $totalGuru }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2 border-0 border-start border-warning border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Jumlah Kelas Aktif</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $totalKelas }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-open fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-envelope-open-text me-2"></i>Pengajuan Izin Terbaru</h6>
                    <a href="#" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($izinTerbaru as $izin)
                                <tr>
                                    <td><span class="fw-bold">{{ $izin->nama_siswa }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $izin->nama_kelas }}</span></td>
                                    <td>
                                        @if($izin->status == 'Sakit')
                                            <span class="text-danger"><i class="fas fa-medkit me-1"></i> Sakit</span>
                                        @else
                                            <span class="text-info"><i class="fas fa-info-circle me-1"></i> Izin</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ \Carbon\Carbon::parse($izin->created_at)->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">Belum ada pengajuan izin masuk.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white py-3 fw-bold">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('siswa.create') }}" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <div class="bg-primary-subtle text-primary p-2 rounded me-3">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span>Input Siswa Baru</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <div class="bg-success-subtle text-success p-2 rounded me-3">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <span>Atur Jadwal Pelajaran</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center">
                            <div class="bg-warning-subtle text-warning p-2 rounded me-3">
                                <i class="fas fa-print"></i>
                            </div>
                            <span>Cetak Laporan Bulanan</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection