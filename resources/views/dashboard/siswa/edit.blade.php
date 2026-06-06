@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Edit Data Siswa</div>
                <div class="card-body">
                    <form action="{{ route('siswa.update', $siswa->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">NISN</label>
                                <input type="number" name="nisn" class="form-control" value="{{ $siswa->nisn }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_siswa" class="form-control" value="{{ $siswa->nama_siswa }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <select name="kelas_id" class="form-select" required>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }}>
                                        {{ $k->nama_kelas }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L" {{ $siswa->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                    <option value="P" {{ $siswa->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ $siswa->alamat }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. HP Orang Tua</label>
                            <input type="text"
                                id="no_hp_ortu"
                                name="no_hp_ortu"
                                class="form-control"
                                value="{{ $siswa->no_hp_ortu }}"
                                required>
                            <small id="hp-error" class="text-danger" style="display:none;">Nomor HP harus 10-13 digit!</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" id="btn-update" class="btn btn-warning">Update Data</button>
                        </div>

                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
document.getElementById('btn-update').addEventListener('click', function(e) {
    var hpInput = document.getElementById('no_hp_ortu').value;
    var errorMsg = document.getElementById('hp-error');
    
    // Validasi panjang karakter 10-13
    if (hpInput.length < 10 || hpInput.length > 13) {
        e.preventDefault(); // Mencegah form terkirim
        errorMsg.style.display = 'block'; // Munculkan pesan error
        document.getElementById('no_hp_ortu').focus();
        alert('Maaf, Nomor HP harus antara 10 sampai 13 digit.');
    } else {
        errorMsg.style.display = 'none';
    }
});

// Skrip agar inputan otomatis hanya angka (Sesuai kodinganmu sebelumnya)
document.getElementById('no_hp_ortu').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
