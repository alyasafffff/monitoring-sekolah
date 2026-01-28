@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="mb-4">Dashboard Administrator</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-users me-2"></i>Total Siswa</h5>
                    <p class="card-text display-4 fw-bold">1,250</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-chalkboard-teacher me-2"></i>Total Guru</h5>
                    <p class="card-text display-4 fw-bold">45</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3 shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-door-open me-2"></i>Total Kelas</h5>
                    <p class="card-text display-4 fw-bold">24</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white fw-bold">Aksi Cepat</div>
                <div class="card-body">
                    <a href="#" class="btn btn-outline-primary me-2"><i class="fas fa-plus me-1"></i> Tambah Siswa Baru</a>
                    <a href="#" class="btn btn-outline-success"><i class="fas fa-plus me-1"></i> Tambah Jadwal Pelajaran</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection