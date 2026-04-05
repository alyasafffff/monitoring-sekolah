@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1">Pengaturan Akun</h2>
        <p class="text-muted small">Kelola informasi profil dan keamanan akun Anda</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('profile.update.all') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            {{-- Kolom Kiri: Foto Profil --}}
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-0 text-center p-4">
                    <div class="card-body">
                        <div class="position-relative d-inline-block mb-4">
                            <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}" 
                                 class="rounded-circle border p-1" 
                                 width="150" height="150" style="object-fit: cover;"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=150&background=3b82f6&color=fff'">
                        </div>
                        
                        <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                        <p class="badge bg-primary-subtle text-primary text-uppercase px-3 mb-4" style="font-size: 10px;">{{ Auth::user()->role }}</p>
                        
                        <div class="text-start">
                            <label class="form-label small fw-bold text-muted">GANTI FOTO PROFIL</label>
                            <input type="file" name="foto_profil" class="form-control form-control-sm @error('foto_profil') is-invalid @enderror">
                            @error('foto_profil') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                {{-- Alert Info Administrator --}}
                <div class="alert alert-info border-0 shadow-sm mt-3" style="font-size: 12px;">
                    <i class="fas fa-info-circle me-2"></i> Ingin mengubah <strong>NIP</strong> atau <strong>Nama</strong>? Silakan hubungi Administrator sekolah.
                </div>
            </div>

            {{-- Kolom Kanan: Detail & Keamanan --}}
            <div class="col-lg-8">
                {{-- Detail Akun --}}
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-id-card me-2 text-primary"></i>Informasi Identitas</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NIP</label>
                                <input type="text" class="form-control bg-secondary-subtle border-0" value="{{ Auth::user()->nip }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                                <input type="text" class="form-control bg-secondary-subtle border-0" value="{{ Auth::user()->name }}" readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">NOMOR HANDPHONE (WHATSAPP)</label>
                                <input type="text" name="no_hp" class="form-control bg-light border-0 @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', Auth::user()->no_hp) }}" placeholder="Contoh: 08123456789">
                                @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Keamanan --}}
                <div class="card shadow border-0 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-lock me-2 text-danger"></i>Ganti Password</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">PASSWORD SAAT INI</label>
                                <input type="password" name="current_password" class="form-control bg-light border-0 @error('current_password') is-invalid @enderror" placeholder="Masukkan password sekarang">
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">PASSWORD BARU</label>
                                <input type="password" name="password" class="form-control bg-light border-0 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">KONFIRMASI PASSWORD BARU</label>
                                <input type="password" name="password_confirmation" class="form-control bg-light border-0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary fw-bold px-5 py-2 shadow">
                        <i class="fas fa-save me-2"></i>SIMPAN PERUBAHAN
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection