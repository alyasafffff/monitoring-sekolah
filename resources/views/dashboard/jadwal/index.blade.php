@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="text-primary fw-bold mb-4">Manajemen Jadwal Pelajaran</h2>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Form Pilih Kelas --}}
    <div class="card shadow border-0 mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('jadwal.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="fw-bold mb-2 small text-uppercase">Pilih Kelas:</label>
                        <select name="kelas_id" class="form-select border-primary shadow-sm" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelaslist as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                Kelas {{ $k->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($kelas_terpilih)
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3 fw-bold">
            <i class="fas fa-calendar-alt me-2 text-primary"></i> Jadwal: <span class="text-primary">{{ $kelas_terpilih->nama_kelas }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th width="80">Jam</th>
                            @php $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; @endphp
                            @foreach($hariList as $hari)
                            <th>{{ $hari }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 0; $i <= $maxJamKe; $i++)
                            <tr>
                            <td class="bg-light fw-bold text-secondary small">Ke-{{ $i }}</td>
                            @foreach($hariList as $hari)
                            @php
    $grup = ($hari == 'Senin') ? 'Senin' : (($hari == 'Jumat') ? 'Jumat' : 'Reguler');
    $waktu = $configJam[$grup][$i] ?? null;

    if (!$waktu) {
        echo '<td class="bg-secondary bg-opacity-10" style="height: 70px;"></td>';
        continue;
    }

    // LOGIKA PENTING: Cari jadwal menggunakan matrix yang sudah kita petakan di Controller
    $jadwal = $jadwalMatrix[$hari][$waktu['mulai']] ?? null;
@endphp

                            @if($waktu['tipe'] == 'istirahat' || $waktu['tipe'] == 'kegiatan')
                            <td class="{{ $waktu['tipe'] == 'istirahat' ? 'bg-warning' : 'bg-info' }} bg-opacity-10">
                                <div class="fw-bold small" style="font-size: 9px;">{{ $waktu['keterangan'] }}</div>
                                <div class="text-muted" style="font-size: 8px;">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</div>
                            </td>
                            @elseif($jadwal)
                            <td class="bg-primary bg-opacity-10 align-top p-2 border border-primary position-relative">
                                <div class="fw-bold text-primary small" style="line-height: 1.2;">{{ $jadwal->nama_mapel }}</div>
                                <div style="font-size: 10px;" class="text-muted">{{ Str::limit($jadwal->nama_guru, 12) }}</div>
                                <div class="badge bg-white text-dark border shadow-sm mt-1" style="font-size: 9px;">
                                    {{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}
                                </div>
                                <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Hapus?')" class="position-absolute top-0 end-0 m-1">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link text-danger p-0" style="font-size: 10px;"><i class="fas fa-times"></i></button>
                                </form>
                            </td>
                            @else
                            <td class="cell-hover py-3 text-center btn-buka-modal"
                                style="cursor: pointer;"
                                data-hari="{{ $hari }}"
                                data-mulai="{{ $waktu['mulai'] }}"
                                data-selesai="{{ $waktu['selesai'] }}"
                                data-hari="{{ $hari }}"
                                data-configid="{{ $waktu['id'] }}">
                                <span class="text-muted opacity-25 small" style="font-size: 10px;">{{ $waktu['mulai'] }}</span>
                            </td>
                            @endif
                            @endforeach
                            </tr>
                            @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5 text-muted shadow-sm bg-white rounded">
        <i class="fas fa-arrow-up d-block mb-3 fa-2x"></i>
        Silakan pilih kelas terlebih dahulu untuk mengatur jadwal.
    </div>
    @endif
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalJadwal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">
                <input type="hidden" name="jam_pelajaran_config_id" id="inputConfigId">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i> Isi Jadwal</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="hari" id="inputHari">
                    <div class="alert alert-primary bg-opacity-10 border-primary text-primary py-2 small fw-bold mb-3">
                        <i class="fas fa-clock me-1"></i> <span id="labelHari"></span> | <span id="labelWaktu"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select border-primary" required>
                            <option value="">-- Pilih Mapel --</option>
                            @foreach($mapel as $m)
                            {{-- Gunakan id untuk value, dan nama_mapel untuk teks --}}
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Guru Pengajar</label>
                        <select name="guru_id" class="form-select border-primary" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($guru as $g)
                            {{-- User dengan role guru dikirim lewat variabel $guru --}}
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light text-center d-block">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menangkap klik pada cell tabel yang punya class btn-buka-modal
    const cells = document.querySelectorAll('.btn-buka-modal');
    
    cells.forEach(cell => {
        cell.addEventListener('click', function() {
            // Ambil data dari atribut data-* yang ada di <td>
            const hari = this.getAttribute('data-hari');
            const mulai = this.getAttribute('data-mulai');
            const selesai = this.getAttribute('data-selesai');
            const configId = this.getAttribute('data-configid');


            // Isi data ke dalam Modal
            document.getElementById('labelHari').innerText = hari;
            document.getElementById('labelWaktu').innerText = mulai + ' - ' + selesai;
            document.getElementById('inputConfigId').value = configId;
            document.getElementById('inputHari').value = hari;

            // Munculkan Modal
            const modalJadwal = new bootstrap.Modal(document.getElementById('modalJadwal'));
            modalJadwal.show();
        });
    });
});
</script>

<style>
    .cell-hover:hover {
        background-color: #f0f7ff;
        color: #0d6efd !important;
    }

    .cell-hover:hover span {
        opacity: 1 !important;
    }

    .table th {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
</style>
@endsection