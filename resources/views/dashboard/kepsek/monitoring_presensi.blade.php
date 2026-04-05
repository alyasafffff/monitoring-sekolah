@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-0">Analis Presensi</h2>
            <p class="text-muted small">Laporan ketidakhadiran siswa bulan {{ \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F') }}</p>
        </div>
        <div class="col-md-6 text-md-end">
            <form action="" method="GET" class="d-flex justify-content-md-end gap-2">
                <select name="bulan" class="form-select form-select-sm w-auto">
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ $i }}" {{ (int)$bulan == $i ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}</option>
                    @endfor
                </select>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </form>
        </div>
    </div>

    <div class="row">
        {{-- Tabel Ranking Kelas --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-4">Urutan Alpha per Kelas</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr class="small">
                                    <th>KELAS</th>
                                    <th>GRAFIK ALPHA</th>
                                    <th class="text-center">ALPHA</th>
                                    <th class="text-center">IZIN/SAKIT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rekapKelas as $rk)
                                @php 
                                    $maxAlpha = $rekapKelas->max('total_alpha') ?: 1;
                                    $persen = ($rk->total_alpha / $maxAlpha) * 100;
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $rk->nama_kelas }}</td>
                                    <td width="40%">
                                        <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                            <div class="progress-bar bg-danger rounded-pill" 
                                                 style="--width: {{ $persen }}%; width: var(--width);">
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">{{ $rk->total_alpha }}</span>
                                    </td>
                                    <td class="text-center small text-muted">
                                        {{ $rk->total_izin }} I / {{ $rk->total_sakit }} S
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- List Siswa Bermasalah --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="fw-bold m-0 text-danger">Top 10 Siswa Bolos</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($siswaBermasalah as $s)
                        <div class="list-group-item py-3 px-4 border-0 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold">{{ $s->nama_siswa }}</h6>
                                    <p class="mb-0 small text-muted">Kelas {{ $s->nama_kelas }}</p>
                                    
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-danger rounded-circle p-2" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                        {{ $s->jumlah_alpha }}
                                    </span>
                                    <small class="d-block text-muted" style="font-size: 10px;">Alpha</small>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted small">Data tidak ditemukan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .progress-bar { transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1); }
</style>
@endsection