@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold">Manajemen Jadwal Pelajaran</h2>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        {{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i> Jadwal: <span class="text-primary">{{ $kelas_terpilih->nama_kelas }}</span></h6>


            <a href="{{ route('jadwal.export', ['kelas_id' => $kelas_terpilih->id]) }}" class="btn btn-danger btn-sm shadow-sm">
                <i class="fas fa-file-pdf me-1"></i> Export PDF
            </a>
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
                            $jadwal = ($waktu) ? ($jadwalMatrix[$hari][$waktu['mulai']] ?? null) : null;
                            @endphp

                            @if(!$waktu)
                            {{-- Slot tidak tersedia di config --}}
                            <td class="bg-secondary bg-opacity-10" style="height: 80px;"></td>
                            @elseif($waktu['tipe'] == 'istirahat')
                            {{-- Slot Istirahat (Statis) --}}
                            <td class="bg-warning bg-opacity-10">
                                <div class="fw-bold small" style="font-size: 10px;">{{ $waktu['keterangan'] }}</div>
                                <div class="text-muted small" style="font-size: 9px;">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</div>
                            </td>
                            @elseif($jadwal)
                            {{-- Slot SUDAH TERISI Jadwal --}}
                            <td class="bg-primary bg-opacity-10 align-top p-2 border border-primary position-relative">
                                <div class="fw-bold text-primary small mb-1">{{ $jadwal->nama_mapel ?? $jadwal->nama_kegiatan }}</div>
                                <div style="font-size: 10px;" class="text-dark mb-1">{{ Str::limit($jadwal->nama_guru, 15) }}</div>
                                <div class="text-muted fw-bold" style="font-size: 9px;">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</div>

                                <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')" class="position-absolute top-0 end-0 m-1">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link text-danger p-0 shadow-none"><i class="fas fa-times-circle"></i></button>
                                </form>
                            </td>
                            @else
                            {{-- Slot KOSONG (Bisa diklik untuk tambah) --}}
                            <td class="cell-hover py-3 text-center btn-tambah-jadwal"
                                style="cursor: pointer;"
                                data-url="{{ route('jadwal.create', ['kelas_id' => $kelas_terpilih->id, 'hari' => $hari, 'config_id' => $waktu['id']]) }}">

                                @if($waktu['tipe'] == 'kegiatan')
                                <div class="text-info fw-bold mb-1" style="font-size: 10px;">{{ $waktu['keterangan'] }}</div>
                                @endif
                                <span class="text-muted opacity-25 small" style="font-size: 9px;">{{ $waktu['mulai'] }} - {{ $waktu['selesai'] }}</span>
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
    <div class="text-center py-5 text-muted shadow-sm bg-white rounded border">
        <i class="fas fa-arrow-up d-block mb-3 fa-2x text-primary opacity-50"></i>
        Silakan pilih kelas terlebih dahulu untuk mengatur jadwal.
    </div>
    @endif
</div>

<style>
    .cell-hover:hover {
        background-color: #f0f7ff !important;
        transition: 0.3s;
    }

    .cell-hover:hover span {
        opacity: 1 !important;
        color: #0d6efd;
        font-weight: bold;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil semua cell yang punya class btn-tambah-jadwal
        const clickableCells = document.querySelectorAll('.btn-tambah-jadwal');

        clickableCells.forEach(cell => {
            cell.addEventListener('click', function() {
                // Ambil URL dari atribut data-url
                const url = this.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil semua cell yang punya class btn-tambah-jadwal
        const clickableCells = document.querySelectorAll('.btn-tambah-jadwal');

        clickableCells.forEach(cell => {
            cell.addEventListener('click', function() {
                // Ambil URL dari atribut data-url
                const url = this.getAttribute('data-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
@endsection