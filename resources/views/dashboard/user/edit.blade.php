@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-white fw-bold">Edit User: {{ $user->name }}</div>
                <div class="card-body">
                    {{--  --}}
                    {{-- IMPORTANT: Added enctype="multipart/form-data" --}}
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="guru" {{ $user->role == 'guru' ? 'selected' : '' }}>Guru Pengajar</option>
                                    <option value="bk" {{ $user->role == 'bk' ? 'selected' : '' }}>Guru BK</option>
                                    <option value="kepsek" {{ $user->role == 'kepsek' ? 'selected' : '' }}>Kepala Sekolah</option>
                                    <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Administrator</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">NIP / Username</label>
                                <input type="text" name="nip" class="form-control" value="{{ $user->nip }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ $user->name }}" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control" value="{{ $user->no_hp }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password Baru <small class="text-muted">(Opsional)</small></label>
                                <input type="password" name="password" class="form-control" placeholder="Isi jika ingin ganti password">
                            </div>
                        </div>

                        {{-- NEW: Profile Picture Input & Preview --}}
                        <div class="mb-3">
                            <label class="form-label">Foto Profil <small class="text-muted">(Opsional)</small></label>
                            
                            @if($user->foto_profil)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Current Profile" class="rounded-circle border" width="80" height="80" style="object-fit: cover;">
                                    <small class="d-block text-muted mt-1">Foto saat ini</small>
                                </div>
                            @endif

                            <input type="file" name="foto_profil" class="form-control" accept="image/*">
                            <div class="form-text text-muted">Biarkan kosong jika tidak ingin mengubah foto.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="act" name="is_active" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="act">Akun Aktif</label>
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-warning">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection