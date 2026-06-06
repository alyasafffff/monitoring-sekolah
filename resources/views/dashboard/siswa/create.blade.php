@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Tambah Siswa Baru</div>
                <div class="card-body">
                    

                    <form action="{{ route('siswa.store') }}" method="POST" id="form-tambah-siswa">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">NISN</label>
                                {{-- Tambahkan class 'is-invalid' jika ada error nisn, dan gunakan 'old()' agar input tidak hilang --}}
                                <input type="number" name="nisn" class="form-control @error('nisn') is-invalid @enderror" value="{{ old('nisn') }}" required>
                                @error('nisn')
                                <div class="invalid-feedback">NISN telah terdaftar</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama_siswa" class="form-control" value="{{ old('nama_siswa') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kelas</label>
                                <select name="kelas_id" class="form-select" required>
                                    <option value="">-- Pilih Kelas --</option>
                                    @foreach($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Jenis Kelamin</label>
                                <select name="jenis_kelamin" class="form-select" required>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. HP Orang Tua</label>
                            <input type="text"
                                id="no_hp_ortu"
                                name="no_hp_ortu"
                                class="form-control"
                                placeholder="08xxxxxxxx"
                                required>
                            <small id="hp-error" class="text-danger" style="display:none;">Nomor HP harus antara 10 sampai 13 digit!</small>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('siswa.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" id="btn-simpan" class="btn btn-primary">Simpan Siswa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tambahkan script validasi di bawah ini --}}
<script>
    document.getElementById('form-tambah-siswa').addEventListener('submit', function(e) {
        var hpInput = document.getElementById('no_hp_ortu').value;
        var errorMsg = document.getElementById('hp-error');

        // Validasi panjang karakter 10-13 digit
        if (hpInput.length < 10 || hpInput.length > 13) {
            e.preventDefault(); // Menghentikan form agar tidak tersimpan
            errorMsg.style.display = 'block';
            document.getElementById('no_hp_ortu').focus();
        } else {
            errorMsg.style.display = 'none';
        }
    });

    // Otomatis hapus karakter jika user mencoba mengetik selain angka
    document.getElementById('no_hp_ortu').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection