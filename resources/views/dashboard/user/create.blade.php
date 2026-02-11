@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Tambah User Baru</div>
                <div class="card-body">
                    {{--  --}}
                    {{-- IMPORTANT: Added enctype="multipart/form-data" to allow file uploads --}}
                    <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Role / Jabatan</label>
                                <select name="role" class="form-select bg-light" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="guru">Guru Pengajar</option>
                                    <option value="bk">Guru BK</option>
                                    
                                    {{-- Logic to show/hide Principal option --}}
                                    @if(!$sudahAdaKepsek) 
                                        <option value="kepsek">Kepala Sekolah</option>
                                    @endif

                                    <option value="admin">Administrator</option>
                                </select>

                                @if($sudahAdaKepsek)
                                    <small class="text-danger fst-italic mt-1 d-block" style="font-size: 11px;">
                                        *Opsi Kepala Sekolah disembunyikan karena akun Kepsek sudah ada.
                                    </small>
                                @endif
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">NIP / Username Login</label>
                                <input type="text" name="nip" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" placeholder="Nama beserta gelar" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                            </div>
                        </div>

                        {{-- NEW: Profile Picture Input --}}
                        <div class="mb-3">
                            <label class="form-label">Foto Profil <small class="text-muted">(Opsional, Max 2MB)</small></label>
                            <input type="file" name="foto_profil" class="form-control" accept="image/*">
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection