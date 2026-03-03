@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Dashboard --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-danger mb-0">Dashboard Monitoring BK</h2>
            <p class="text-muted">Pantau kehadiran 15 kelas secara real-time, <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border p-2">
                <i class="fas fa-calendar-alt me-1 text-danger"></i> 
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    {{-- Statistik Cards --}}
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 border-0 border-start border-danger border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Siswa Bolos (Alpha) Hari Ini</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $totalBolosHariIni }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 border-0 border-start border-warning border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Siswa Izin / Sakit</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $totalIzinSakit }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-notes-medical fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100 py-2 border-0 border-start border-info border-5">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Monitoring Input Guru</div>
                            <div class="h2 mb-0 fw-bold text-gray-800">{{ $kelasSudahPresensi }} <small class="fs-6 text-muted">/ 15 Kelas</small></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-check fa-2x text-gray-300 opacity-25"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        {{-- Live Feed Alpha --}}
        <div class="col-lg-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between border-0">
                    <h6 class="m-0 fw-bold text-danger"><i class="fas fa-satellite-dish me-2"></i>Live Feed: Deteksi Bolos Terkini</h6>
                    <span class="badge bg-danger-subtle text-danger px-3">Real-time</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Waktu Pelajaran</th>
                                    <th>Input</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($liveBolos as $bolos)
                                <tr>
                                    <td><span class="fw-bold text-dark">{{ $bolos->nama_siswa }}</span></td>
                                    <td><span class="badge bg-secondary-subtle text-secondary border">{{ $bolos->nama_kelas }}</span></td>
                                    <td><span class="text-muted">{{ $bolos->nama_mapel }}</span></td>
                                    <td>
                                        <small class="badge bg-light text-dark border fw-normal">
                                            {{ substr($bolos->jam_mulai, 0, 5) }} - {{ substr($bolos->jam_selesai, 0, 5) }}
                                        </small>
                                    </td>
                                    <td><small class="text-muted">{{ \Carbon\Carbon::parse($bolos->created_at)->diffForHumans() }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted small">
                                        <i class="fas fa-check-circle fa-2x mb-3 d-block opacity-25"></i>
                                        Belum ada laporan Alpha masuk hari ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Top 5 Siswa Bermasalah --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-danger text-white py-3 fw-bold border-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Top 5 Sering Bolos
                </div>
                <div class="card-body">
                    <p class="small text-muted mb-4">Daftar siswa dengan akumulasi Alpha terbanyak bulan ini.</p>
                    <div class="list-group list-group-flush">
                        @forelse($siswaBermasalah as $bad)
                        <div class="list-group-item border-0 px-0 d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-danger-subtle text-danger p-2 rounded me-3 text-center" style="width: 40px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $bad->nama_siswa }}</h6>
                                    <small class="text-muted">Kelas {{ $bad->nama_kelas }}</small>
                                </div>
                            </div>
                            <span class="badge bg-danger rounded-pill">{{ $bad->total_alpha }} Kali</span>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted small">Data belum tersedia.</div>
                        @endforelse
                    </div>
                    <hr>
                    <div class="d-grid mt-3">
                        <a href="#" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-print me-1"></i> Cetak Rekap Pelanggaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection