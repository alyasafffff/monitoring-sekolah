@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Edit User: {{ $user->name }}</div>
                <div class="card-body">



                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="guru" {{ (old('role') ?? $user->role) == 'guru' ? 'selected' : '' }}>Guru Pengajar</option>
                                    <option value="bk" {{ (old('role') ?? $user->role) == 'bk' ? 'selected' : '' }}>Guru BK</option>
                                    <option value="kepsek" {{ (old('role') ?? $user->role) == 'kepsek' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="admin" {{ (old('role') ?? $user->role) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                                @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">NIP / Username</label>
                                <input type="text"
                                    name="nip"
                                    id="nip_edit"
                                    class="form-control @error('nip') is-invalid @enderror"
                                    value="{{ old('nip') ?? $user->nip }}"
                                    required>
                                @error('nip')
                                <div class="invalid-feedback">NIP sudah terdaftar oleh pengguna lain.</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') ?? $user->name }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" id="no_hp_edit" class="form-control @error('no_hp') is-invalid @enderror"
                                    value="{{ old('no_hp') ?? $user->no_hp }}">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak ganti)</small></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Isi jika ingin ganti password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto Profil</label>
                            @if($user->foto_profil)
                            <div class="mb-2 d-flex align-items-center">
                                <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Current Profile"
                                    class="rounded-circle border me-3" width="60" height="60" style="object-fit: cover;">
                                <div class="badge bg-light text-dark border">Foto Saat Ini</div>
                            </div>
                            @endif
                            <input type="file" name="foto_profil" class="form-control @error('foto_profil') is-invalid @enderror" accept="image/*">
                            @error('foto_profil') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="act" name="is_active" {{ (old('is_active') ?? $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="act">Akun Aktif</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-warning px-4">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Hanya angka untuk No HP
    document.getElementById('no_hp_edit').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    // Hanya angka untuk NIP
    document.getElementById('nip_edit').addEventListener('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endsection