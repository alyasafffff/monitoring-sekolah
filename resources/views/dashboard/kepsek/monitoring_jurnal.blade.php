@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header Section --}}
    <div class="row align-items-end mb-4">
        <div class="col-lg-6">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item small"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item small active" aria-current="page">Monitoring Kurikulum</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">Matriks Keselarasan Materi</h2>
            <p class="text-muted small mb-0">Memantau kemajuan materi pengajaran antar kelas jenjang {{ $jenjang }}</p>
        </div>
        
        <div class="col-lg-6 text-end">
            <div class="btn-group shadow-sm bg-white p-1 rounded-3">
                <a href="?jenjang=7&rel_date={{ $startOfWeek->toDateString() }}" class="btn btn-{{ $jenjang == '7' ? 'primary' : 'light' }} border-0 px-3 fw-bold btn-sm">Kelas 7</a>
                <a href="?jenjang=8&rel_date={{ $startOfWeek->toDateString() }}" class="btn btn-{{ $jenjang == '8' ? 'primary' : 'light' }} border-0 px-3 fw-bold btn-sm">Kelas 8</a>
                <a href="?jenjang=9&rel_date={{ $startOfWeek->toDateString() }}" class="btn btn-{{ $jenjang == '9' ? 'primary' : 'light' }} border-0 px-3 fw-bold btn-sm">Kelas 9</a>
            </div>
        </div>
    </div>

    {{-- Navigasi Minggu --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-2 px-3">
            <div class="row align-items-center text-center text-md-start">
                <div class="col-md-4">
                    <span class="small fw-bold text-muted text-uppercase">Periode Pantauan:</span>
                    <div class="text-dark fw-bold">{{ $startOfWeek->translatedFormat('d M Y') }} — {{ $endOfWeek->translatedFormat('d M Y') }}</div>
                </div>
                <div class="col-md-4 text-center my-3 my-md-0">
                    <div class="btn-group border rounded-pill overflow-hidden p-0 bg-light shadow-xs">
                        <a href="?jenjang={{ $jenjang }}&rel_date={{ $startOfWeek->copy()->subWeek()->toDateString() }}" class="btn btn-light border-0 px-3 btn-sm">
                            <i class="fas fa-chevron-left text-primary"></i>
                        </a>
                        <div class="btn btn-light border-0 fw-bold px-4 btn-sm" style="pointer-events: none; min-width: 160px;">
                            {{ $startOfWeek->translatedFormat('M Y') }}
                        </div>
                        <a href="?jenjang={{ $jenjang }}&rel_date={{ $startOfWeek->copy()->addWeek()->toDateString() }}" class="btn btn-light border-0 px-3 btn-sm">
                            <i class="fas fa-chevron-right text-primary"></i>
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if(!$startOfWeek->isCurrentWeek())
                        <a href="?jenjang={{ $jenjang }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="fas fa-undo-alt me-1"></i> Kembali ke Minggu Ini
                        </a>
                    @else
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill">
                            <i class="fas fa-calendar-check me-1"></i> Minggu Berjalan
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Matriks --}}
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive custom-scrollbar" style="max-height: 75vh;">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="bg-primary text-white">
                            <th class="sticky-col py-3 ps-4" style="min-width: 240px; position: sticky; left: 0; z-index: 10; background: #3b82f6;">
                                MATA PELAJARAN
                            </th>
                            @foreach($kelasList as $k)
                                <th class="py-3 text-center border-start border-white-50" style="min-width: 280px;">
                                    KELAS {{ $k->nama_kelas }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @forelse($mapels as $mapel)
                        <tr>
                            <td class="sticky-col fw-bold text-dark ps-4 shadow-sm" style="position: sticky; left: 0; z-index: 5; background: #f8fafc; border-right: 2px solid #e2e8f0;">
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary-subtle text-primary rounded-2 p-2 me-2">
                                        <i class="fas fa-book small"></i>
                                    </div>
                                    {{ $mapel->nama_mapel }}
                                </div>
                            </td>
                            @foreach($kelasList as $k)
                                @php
                                    $item = $dataJurnal->where('mapel_id', $mapel->id)->where('kelas_id', $k->id)->first();
                                @endphp
                                <td class="p-3 border-start h-100" style="vertical-align: top;">
                                    @if($item)
                                        <div class="p-3 rounded-3 border h-100 bg-white shadow-xs materi-card">
                                            <p class="fw-semibold text-dark mb-3" style="font-size: 13.5px; line-height: 1.5; min-height: 40px;">
                                                {{ $item->materi ?: '—' }}
                                            </p>
                                            <div class="d-flex justify-content-between align-items-center mt-auto pt-2 border-top">
                                                <small class="text-muted text-truncate me-2" title="{{ $item->nama_guru }}">
                                                    <i class="fas fa-user-tie me-1 opacity-50"></i>{{ Str::words($item->nama_guru, 1) }}
                                                </small>
                                                <span class="badge bg-light text-dark border-0 fw-medium px-2 py-1" style="font-size: 10px;">
                                                    {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d M') }}
                                                </span>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4 bg-light-subtle rounded-3 border border-dashed d-flex align-items-center justify-content-center h-100" style="min-height: 100px;">
                                            <div class="text-muted opacity-50">
                                                <span class="small italic">Belum Ada Laporan</span>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $kelasList->count() + 1 }}" class="text-center py-5">
                                <p class="text-muted mb-0">Data tidak ditemukan untuk jenjang ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Shadows & Effects */
    .shadow-xs { box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
    .materi-card { transition: transform 0.2s; }
    .materi-card:hover { transform: translateY(-3px); border-color: #3b82f6 !important; }
    .table-hover tbody tr:hover td { background-color: rgba(59, 130, 246, 0.01); }
    
    /* Sticky Fix */
    thead th { position: sticky; top: 0; z-index: 20; }
    
    /* Scrollbar */
    .custom-scrollbar::-webkit-scrollbar { height: 8px; width: 8px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
    
    .border-dashed { border-style: dashed !important; border-width: 2px; }
</style>
@endsection