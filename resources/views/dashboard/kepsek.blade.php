@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Summary Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div><small class="opacity-75">Siswa Terdaftar</small>
                        <h3 class="fw-bold mb-0">{{ $totalSiswa }}</h3>
                    </div>
                    <i class="fas fa-user-graduate fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div><small class="opacity-75">Total Guru</small>
                        <h3 class="fw-bold mb-0">{{ $totalGuru }}</h3>
                    </div>
                    <i class="fas fa-chalkboard-teacher fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-danger text-white p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div><small class="opacity-75">Siswa Alpha Hari Ini</small>
                        <h3 class="fw-bold mb-0">{{ $alphaHariIni }}</h3>
                    </div>
                    <i class="fas fa-user-slash fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Grafik Tren --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold m-0">Tren Kedisiplinan (7 Hari)</h6>
                </div>
                <div class="card-body">
                    <canvas id="alphaChart"
                        data-labels="{{ json_encode(collect($chartData)->pluck('label')) }}"
                        data-values="{{ json_encode(collect($chartData)->pluck('value')) }}"
                        height="250">
                    </canvas>
                </div>
            </div>
        </div>

        {{-- Monitoring Keselarasan Materi --}}
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between">
                    <h6 class="fw-bold m-0">Keselarasan Materi Antar Kelas</h6>
                    <span class="badge bg-primary-subtle text-primary">Live Monitor</span>
                </div>
                <div class="card-body p-0 overflow-auto" style="max-height: 400px;">
                    <table class="table table-hover align-middle mb-0" style="font-size: 13px;">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th>Jenjang</th>
                                <th>Mata Pelajaran</th>
                                <th>Kelas</th>
                                <th>Materi Terakhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monitorMateri as $m)
                            <tr>
                                <td><span class="badge bg-dark">{{ $m->jenjang }}</span></td>
                                <td class="fw-bold">{{ $m->nama_mapel }}</td>
                                <td><span class="badge bg-info-subtle text-info border border-info">{{ $m->nama_kelas }}</span></td>
                                <td>
                                    <div class="text-dark fw-medium text-truncate" style="max-width: 180px;" title="{{ $m->materi }}">
                                        {{ $m->materi ?: 'Belum diisi' }}
                                    </div>
                                    <small class="text-muted d-block" style="font-size: 10px;">{{ $m->nama_guru }}</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('alphaChart');
    
    // Ambil data dari atribut HTML tadi
    const labels = JSON.parse(ctx.getAttribute('data-labels'));
    const values = JSON.parse(ctx.getAttribute('data-values'));

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels, // Pakai variabel
            datasets: [{
                label: 'Jumlah Alpha',
                data: values, // Pakai variabel
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        }
    });
</script>
@endsection