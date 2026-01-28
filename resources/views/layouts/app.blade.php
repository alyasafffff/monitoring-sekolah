<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Monitoring Sekolah</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { min-height: 100vh; overflow-x: hidden; }
        .wrapper { display: flex; align-items: stretch; }
        #sidebar { min-width: 250px; max-width: 250px; min-height: 100vh; background: #2c3e50; color: #fff; transition: all 0.3s; }
        #sidebar .sidebar-header { padding: 20px; background: #1a252f; }
        #sidebar ul.components { padding: 20px 0; border-bottom: 1px solid #47748b; }
        #sidebar ul li a { padding: 10px 20px; font-size: 1.1em; display: block; color: #fff; text-decoration: none; }
        #sidebar ul li a:hover { color: #2c3e50; background: #fff; }
        #content { width: 100%; padding: 20px; min-height: 100vh; background-color: #f8f9fa; }
        .active-menu { background: #34495e; }
    </style>
</head>
<body>
    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><i class="fas fa-school me-2"></i>SiMon</h3>
                <small class="text-muted">Sistem Monitoring</small>
            </div>

            <ul class="list-unstyled components">
                <li>
                    <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active-menu' : '' }}">
                        <i class="fas fa-home me-2"></i>Dashboard
                    </a>
                </li>

                @if(Auth::user()->role == 'admin')
                <li class="mt-3 px-3 text-muted text-uppercase small">Master Data</li>
                <li><a href="{{ route('kelas.index') }}"><i class="fas fa-users me-2"></i>Data Kelas</a></li>
                <li><a href="{{ route('siswa.index') }}"><i class="fas fa-users me-2"></i>Data Siswa</a></li>
                <li><a href="{{ route('users.index') }}"><i class="fas fa-chalkboard-teacher me-2"></i>Data User</a></li>
                <li><a href="{{ route('mapel.index') }}"><i class="fas fa-calendar-alt me-2"></i>Mata Pelajaran</a></li>
                <li><a href="{{ route('jadwal.index') }}"><i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran</a></li>
                @endif

                @if(Auth::user()->role == 'bk')
                <li class="mt-3 px-3 text-muted text-uppercase small">Monitoring</li>
                <li><a href="#"><i class="fas fa-exclamation-triangle me-2"></i>Pelanggaran Realtime</a></li>
                <li><a href="#"><i class="fas fa-history me-2"></i>Riwayat Kasus</a></li>
                @endif

                @if(Auth::user()->role == 'kepsek')
                <li class="mt-3 px-3 text-muted text-uppercase small">Laporan</li>
                <li><a href="#"><i class="fas fa-chart-line me-2"></i>Statistik Kehadiran</a></li>
                <li><a href="#"><i class="fas fa-clipboard-check me-2"></i>Kinerja Guru</a></li>
                @endif
            </ul>

            <div class="mt-auto p-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                    </button>
                </form>
            </div>
        </nav>

        <div id="content">
            <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4 rounded">
                <div class="container-fluid">
                    <span class="navbar-text ms-auto">
                        Halo, <strong>{{ Auth::user()->name }}</strong> 
                        <span class="badge bg-secondary ms-2 text-uppercase">{{ Auth::user()->role }}</span>
                    </span>
                </div>
            </nav>

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>