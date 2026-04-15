@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold text-primary py-3">
                    <i class="fas fa-edit me-2"></i> Atur Jadwal: {{ $kelas->nama_kelas }}
                </div>
                <div class="card-body p-4">

                    {{-- Notifikasi Error (Jika Bentrok atau Validasi Gagal) --}}
                    @if(session('error') || $errors->any())
                        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show mb-4">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-circle me-3 mt-1 fa-lg"></i>
                                <div>
                                    <span class="fw-bold">Gagal Menyimpan!</span>
                                    <ul class="mb-0 mt-1 small">
                                        @if(session('error')) <li>{{ session('error') }}</li> @endif
                                        @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Info Slot Waktu --}}
                    <div class="alert alert-primary bg-opacity-10 border-0 text-primary d-flex justify-content-between mb-4 shadow-sm">
                        <span><i class="fas fa-calendar-day me-1"></i> <strong>{{ $hari }}</strong></span>
                        <span><i class="fas fa-clock me-1"></i> <strong>Jam Ke-{{ $jam_terpilih->jam_ke }} ({{ $jam_terpilih->jam_mulai }})</strong></span>
                    </div>

                    <form action="{{ route('jadwal.store') }}" method="POST">
                        @csrf
                        
                        {{-- Data Hidden --}}
                        <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                        <input type="hidden" name="hari" value="{{ $hari }}">
                        <input type="hidden" name="jam_pelajaran_config_id" value="{{ $jam_terpilih->id }}">

                        {{-- Pilihan Tipe Jadwal --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase text-secondary">Tipe Jadwal</label>
                            <select id="selectTipe" class="form-select border-primary shadow-sm" onchange="toggleForm()">
                                @if($jam_terpilih->tipe == 'kegiatan')
                                    <option value="kegiatan" {{ old('kegiatan_id') ? 'selected' : '' }}>Kegiatan Sekolah</option>
                                    <option value="mapel" {{ old('mapel_id') ? 'selected' : '' }}>Mata Pelajaran</option>
                                @else
                                    <option value="mapel" {{ old('mapel_id') ? 'selected' : '' }}>Mata Pelajaran</option>
                                    <option value="kegiatan" {{ old('kegiatan_id') ? 'selected' : '' }}>Kegiatan Sekolah</option>
                                @endif
                            </select>
                        </div>

                        <hr class="text-muted opacity-25">

                        {{-- Group Input Mata Pelajaran --}}
                        <div id="divMapel" class="{{ (old('kegiatan_id') || $jam_terpilih->tipe == 'kegiatan') && !old('mapel_id') ? 'd-none' : '' }}">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Mata Pelajaran</label>
                                <select name="mapel_id" id="inputMapel" class="form-select border-primary shadow-sm">
                                    <option value="">-- Pilih Mata Pelajaran --</option>
                                    @foreach($mapel as $m)
                                        <option value="{{ $m->id }}" {{ old('mapel_id') == $m->id ? 'selected' : '' }}>
                                            {{ $m->nama_mapel }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Guru Pengampu</label>
                                <select name="guru_id" id="inputGuru" class="form-select border-primary shadow-sm">
                                    <option value="">-- Pilih Guru --</option>
                                    @foreach($guru as $g)
                                        <option value="{{ $g->id }}" {{ old('guru_id') == $g->id ? 'selected' : '' }}>
                                            {{ $g->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Group Input Kegiatan --}}
                        <div id="divKegiatan" class="{{ ($jam_terpilih->tipe == 'kegiatan' || old('kegiatan_id')) && !old('mapel_id') ? '' : 'd-none' }}">
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-secondary">Jenis Kegiatan</label>
                                <select name="kegiatan_id" id="inputKegiatan" class="form-select border-primary shadow-sm">
                                    <option value="">-- Pilih Jenis Kegiatan --</option>
                                    @foreach($kegiatan as $k)
                                        <option value="{{ $k->id }}" {{ old('kegiatan_id') == $k->id ? 'selected' : '' }}>
                                            {{ $k->nama_kegiatan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="alert alert-info py-2 px-3 small border-0 shadow-sm d-flex align-items-center mt-3">
                                <i class="fas fa-info-circle me-2"></i> 
                                <div>Penanggung Jawab Otomatis: <strong>Wali Kelas</strong></div>
                            </div>
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="d-flex justify-content-end mt-5 pt-3 border-top">
                            <a href="{{ route('jadwal.index', ['kelas_id' => $kelas->id]) }}" class="btn btn-light px-4 me-2 fw-bold text-secondary">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow">
                                <i class="fas fa-save me-1"></i> Simpan Jadwal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk menyembunyikan/menampilkan input sesuai tipe
    function toggleForm() {
        const tipe = document.getElementById('selectTipe').value;
        const divMapel = document.getElementById('divMapel');
        const divKegiatan = document.getElementById('divKegiatan');
        
        // Reset input required agar tidak bentrok saat submit
        const inputMapel = document.getElementById('inputMapel');
        const inputGuru = document.getElementById('inputGuru');
        const inputKegiatan = document.getElementById('inputKegiatan');

        if (tipe === 'mapel') {
            divMapel.classList.remove('d-none');
            divKegiatan.classList.add('d-none');
            
            // Tambahkan required di Mapel
            inputMapel.setAttribute('required', 'required');
            inputGuru.setAttribute('required', 'required');
            inputKegiatan.removeAttribute('required');
        } else {
            divMapel.classList.add('d-none');
            divKegiatan.classList.remove('d-none');
            
            // Tambahkan required di Kegiatan
            inputKegiatan.setAttribute('required', 'required');
            inputMapel.removeAttribute('required');
            inputGuru.removeAttribute('required');
        }
    }

    // Jalankan fungsi saat halaman pertama kali dimuat (untuk old input)
    document.addEventListener('DOMContentLoaded', function() {
        toggleForm();
    });
</script>
@endsection