<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    
    <div class="text-center p-5 bg-white shadow rounded" style="max-width: 500px;">
        <h1 class="display-1 text-warning fw-bold">403</h1>
        <h3 class="mb-3">Akses Khusus Mobile</h3>
        <p class="text-muted mb-4">
            Halo <strong>{{ Auth::user()->name }}</strong>.<br>
            Akun Anda terdaftar sebagai <strong>Guru / Wali Kelas</strong>. 
            Silakan akses sistem melalui <strong>Aplikasi Mobile (Android)</strong> untuk melakukan presensi dan input jurnal.
        </p>
        
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-primary px-4">Logout</button>
        </form>
    </div>

</body>
</html>