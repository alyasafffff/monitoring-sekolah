@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Monitoring Kedisiplinan</h2>
        <span class="badge bg-danger p-2"><i class="fas fa-broadcast-tower me-1"></i> Mode Realtime Aktif</span>
    </div>

    <div class="alert alert-danger shadow-sm border-danger" role="alert">
        <h4 class="alert-heading"><i class="fas fa-bell me-2"></i>Peringatan Dini!</h4>
        <p>Sistem mendeteksi <strong>3 Siswa</strong> berstatus ALPHA pada jam pelajaran ke-4 yang sedang berlangsung saat ini.</p>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-white fw-bold text-danger">
            Daftar Siswa Bolos (Hari Ini)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Jam Ke</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>4 (09:40)</td>
                            <td>Ahmad Fulan</td>
                            <td>9A</td>
                            <td>Matematika</td>
                            <td><span class="badge bg-danger">ALPHA (Terdeteksi Sistem)</span></td>
                            <td>
                                <button class="btn btn-sm btn-primary">Panggil</button>
                                <button class="btn btn-sm btn-warning">Hubungi Ortu</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection