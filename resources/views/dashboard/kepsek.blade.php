@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Laporan Eksekutif</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-white fw-bold">
                    Tren Kehadiran Siswa (Minggu Ini)
                </div>
                <div class="card-body">
                    <div style="height: 300px; background: #f1f2f6; display: flex; align-items: center; justify-content: center; color: #aaa;">
                        [Area Grafik Garis Statistik Kehadiran]
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-white fw-bold">
                    Kepatuhan Pengisian Jurnal
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Guru Matematika
                        <span class="badge bg-success rounded-pill">100%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Guru Penjas
                        <span class="badge bg-warning rounded-pill">85%</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Guru Kesenian
                        <span class="badge bg-danger rounded-pill">40%</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection