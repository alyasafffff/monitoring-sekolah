@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="text-primary fw-bold mb-4">Pengaturan Master Jam Pelajaran</h2>

    {{-- Alert Messages --}}
    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow border-0 mb-4">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-plus-circle me-2"></i> Tambah Slot Jam
                </div>
                <div class="card-body">
                    <form action="{{ route('jam-config.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Grup Hari</label>
                            <select name="hari_grup" class="form-select border-primary" required>
                                <option value="">-- Pilih Grup Hari --</option>
                                <option value="Senin">Senin (Pola Upacara)</option>
                                <option value="Reguler">Reguler (Selasa, Rabu, Kamis, Sabtu)</option>
                                <option value="Jumat">Jumat (Pola Jumat Berseri)</option>
                            </select>
                            <div class="form-text" style="font-size: 11px;">
                                Pilih grup hari sesuai jadwal standar sekolah.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Tipe Slot</label>
                            <select name="tipe" class="form-select border-primary" required>
                                <option value="mapel">Mata Pelajaran (Presensi Aktif)</option>
                                <option value="istirahat">Istirahat</option>
                                <option value="kegiatan">Kegiatan (Upacara/Shalat/Literasi)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Jam Ke-</label>
                            <input type="number" name="jam_ke" class="form-control border-primary" placeholder="0, 1, 2, dst..." required>
                        </div>

                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label fw-bold small">Mulai</label>
                                <input type="time" name="jam_mulai" class="form-control border-primary" required>
                            </div>
                            <div class="col">
                                <label class="form-label fw-bold small">Selesai</label>
                                <input type="time" name="jam_selesai" class="form-control border-primary" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold small">Keterangan Label (Opsional)</label>
                            <input type="text" name="keterangan" class="form-control border-primary" placeholder="Contoh: Shalat Duhur, Istirahat 1">
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Slot Jam</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold py-3">
                    <i class="fas fa-list me-2"></i> Daftar Konfigurasi Waktu
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-center">
                            <thead class="bg-light">
                                <tr class="small text-uppercase">
                                    <th>Grup</th>
                                    <th>Tipe</th>
                                    <th>Jam Ke</th>
                                    <th>Waktu</th>
                                    <th>Keterangan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($configs as $c)
                                <tr>
                                    <td>
                                        <span class="badge bg-outline-primary border border-primary text-primary px-2">
                                            {{ $c->hari_grup }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($c->tipe == 'mapel')
                                        <span class="badge bg-primary px-2">MAPEL</span>
                                        @elseif($c->tipe == 'istirahat')
                                        <span class="badge bg-warning text-dark px-2">ISTIRAHAT</span>
                                        @else
                                        <span class="badge bg-info px-2">KEGIATAN</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">#{{ $c->jam_ke }}</td>
                                    <td>{{ \Carbon\Carbon::parse($c->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($c->jam_selesai)->format('H:i') }}</td>
                                    <td><small class="text-secondary">{{ $c->keterangan ?? '-' }}</small></td>
                                                                        <td>
                                        <form action="{{ route('jam-config.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Hapus slot ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm text-danger border-0">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted italic">Belum ada data waktu.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3 small text-muted">
                <i class="fas fa-info-circle me-1"></i> <strong>Tipe MAPEL</strong> akan muncul di rekap presensi sebagai jam efektif belajar.
            </div>
        </div>
    </div>
</div>
@endsection