@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- KONDISI 1: JIKA MENAMPILKAN DETAIL HISTORI (Fungsi Show) --}}
    @if(isset($historiAlpha))
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <a href="{{ route('bk.siswa.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                </a>
                <h2 class="fw-bold text-danger mb-0">Histori Alpha Siswa</h2>
            </div>
            <div class="text-end">
                <span class="badge bg-danger p-2 shadow-sm">Total: {{ count($historiAlpha) }} Alpha</span>
            </div>
        </div>

        <div class="row">
            {{-- Kartu Profil Singkat --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-0 text-center p-4">
                    <div class="mb-3">
                        <div class="bg-danger-subtle text-danger d-inline-block rounded-circle p-4 shadow-sm" style="background-color: #fceaea;">
                            <i class="fas fa-user-graduate fa-4x"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $siswa->nama_siswa }}</h4>
                    <p class="text-muted small">NISN: {{ $siswa->nisn }} | Kelas {{ $siswa->nama_kelas }}</p>
                    <div class="d-grid mt-3">
                        <a href="https://wa.me/{{ $siswa->no_hp_ortu }}" target="_blank" class="btn btn-success fw-bold">
                            <i class="fab fa-whatsapp me-2"></i>Hubungi Orang Tua
                        </a>
                    </div>
                </div>
            </div>

            {{-- Tabel Histori --}}
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 fw-bold text-danger"><i class="fas fa-history me-2"></i>Log Kehadiran Alpha</h6>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Mata Pelajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($historiAlpha as $h)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('d F Y') }}</div>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($h->tanggal)->translatedFormat('l') }}</small>
                                    </td>
                                    <td>{{ $h->nama_mapel }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="2" class="text-center py-5 text-muted">Belum ada riwayat Alpha.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    {{-- KONDISI 2: JIKA MENAMPILKAN DAFTAR SISWA (Fungsi Index) --}}
    @else
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-danger mb-0">Direktori Siswa</h2>
                <p class="text-muted small mb-0">Klik pada baris siswa untuk melihat detail informasi</p>
            </div>
            <div class="text-end">
                <span class="badge bg-white text-dark border p-2 shadow-sm">
                    <i class="fas fa-calendar-alt me-1 text-danger"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        {{-- Filter Box --}}
        <div class="card shadow border-0 mb-4">
            <div class="card-body">
                <form action="{{ route('bk.siswa.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">FILTER KELAS</label>
                        <select name="kelas_id" class="form-select border-danger-subtle" onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($list_kelas as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>Kelas {{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold small text-muted">CARI SISWA</label>
                        <input type="text" name="search" class="form-control border-danger-subtle" placeholder="Nama / NISN..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-danger w-100 fw-bold">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Tabel Utama --}}
        <div class="card shadow border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-uppercase small">
                                <th class="ps-4 py-3">No</th>
                                <th>Info Siswa</th>
                                <th>Kelas</th>
                                <th>Token</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $index => $s)
                            <tr class="view-detail" data-id="{{ $s->id }}" style="cursor: pointer;">
                                <td class="ps-4">{{ ($siswa->currentPage() - 1) * $siswa->perPage() + $index + 1 }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $s->nama_siswa }}</div>
                                    <small class="text-muted">{{ $s->nisn }}</small>
                                </td>
                                <td><span class="badge bg-danger-subtle text-danger border">Kelas {{ $s->nama_kelas }}</span></td>
                                <td><code class="small">{{ $s->qr_token }}</code></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-top">
                    {{ $siswa->appends(request()->input())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>

        {{-- MODAL DETAIL --}}
        @foreach($siswa as $s)
        <div class="modal fade" id="detailSiswa{{ $s->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0">
                    <div class="modal-header border-0 pb-0"><h5 class="fw-bold text-danger">Profil Detail</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body text-center">
                        <div class="mb-3 p-3 bg-light rounded d-inline-block border">{!! QrCode::size(120)->generate($s->qr_token) !!}</div>
                        <div class="list-group list-group-flush text-start border rounded mb-3">
                            <div class="list-group-item px-3"><small class="text-muted d-block fw-bold">NAMA</small><strong>{{ $s->nama_siswa }}</strong></div>
                            <div class="list-group-item px-3"><small class="text-muted d-block fw-bold">HP ORTU</small><span class="text-success fw-bold">{{ $s->no_hp_ortu }}</span></div>
                        </div>
                        <div class="d-grid gap-2">
                            <a href="https://wa.me/{{ $s->no_hp_ortu }}" target="_blank" class="btn btn-success fw-bold">WhatsApp Ortu</a>
                            <a href="{{ route('bk.siswa.show', $s->id) }}" class="btn btn-outline-danger btn-sm">Buka Histori Alpha</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.view-detail');
        rows.forEach(row => {
            row.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const modal = new bootstrap.Modal(document.getElementById('detailSiswa' + id));
                modal.show();
            });
        });
    });
</script>
@endsection