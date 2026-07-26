@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Tambah User Baru</div>
                <div class="card-body">

                    {{-- Alert Umum jika ada error --}}
                    @if ($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm py-2">
                        <small class="fw-bold">Gagal simpan! Silakan periksa kembali kolom yang bertanda merah.</small>
                    </div>
                    @endif

                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Role / Jabatan</label>
                                <select name="role" class="form-select bg-light @error('role') is-invalid @enderror" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="guru" {{ old('role') == 'guru' ? 'selected' : '' }}>Guru Pengajar</option>
                                    <option value="bk" {{ old('role') == 'bk' ? 'selected' : '' }}>Guru BK</option>

                                    @if(!$sudahAdaKepsek)
                                    <option value="kepsek" {{ old('role') == 'kepsek' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    @endif

                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror

                                @if($sudahAdaKepsek)
                                <small class="text-danger fst-italic mt-1 d-block" style="font-size: 11px;">
                                    *Opsi Kepala Sekolah disembunyikan karena akun Kepsek sudah ada.
                                </small>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIP</label>
                                <input type="text"
                                    name="nip"
                                    id="nip_user"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip') }}"
                                    minlength="10"
                                    maxlength="18"
                                    placeholder="Masukkan NIP unik"
                                    required>

                                {{-- UBAH BAGIAN INI --}}
                                @error('nip')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Nama beserta gelar" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp') }}" placeholder="08xxxx">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 6 karakter" required>
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Profil <small class="text-muted">(Opsional, Max 2MB)</small></label>
                            <input type="file" name="foto_profil" class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
                            @error('foto_profil') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary px-4">Simpan User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Pastikan No HP hanya angka
    document.getElementById('no_hp').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Pastikan NIP hanya angka
    document.getElementById('nip_user').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Validasi saat form disubmit
    document.querySelector('form').addEventListener('submit', function(e) {
        let nipValue = document.getElementById('nip_user').value;

        if (nipValue.length < 10) {
            e.preventDefault(); // Mencegah form dikirim
            alert('Gagal! NIP harus berisi minimal 10 angka.');
        } else if (nipValue.length > 18) {
            e.preventDefault(); // Mencegah form dikirim
            alert('Gagal! NIP tidak boleh lebih dari 18 angka.');
        }
    });
</script>
@endsection