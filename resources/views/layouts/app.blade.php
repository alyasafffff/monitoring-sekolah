<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiMon - Sistem Monitoring Sekolah</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --sidebar-bg: #0f172a;
            --sidebar-hover: #1e293b;
            --accent-color: #3b82f6;
            /* Biru Terang */
            --bg-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            height: 100vh;
            overflow: hidden;
            margin: 0;
        }

        /* Layout Structure */
        .wrapper {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            height: 100vh;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s;
        }

        #sidebar .sidebar-header {
            padding: 20px 25px;
            background: var(--sidebar-bg);
            /* Tetap Gelap */
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
        }

        .logo-sidebar {
            width: 45px;
            height: 45px;
            object-fit: contain;
            /* Hanya 0.5px atau pakai blur minimal */
            filter: drop-shadow(0 1px 1px white);
        }

        #sidebar .sidebar-header:hover .logo-sidebar {
            transform: rotate(5deg) scale(1.1);
            /* Sedikit efek interaktif */
        }

        /* AREA MENU */
        #sidebar ul.components {
            padding: 15px 0;
            flex-grow: 1;
            overflow-y: auto;
            scrollbar-width: none;
            /* Firefox */
        }

        #sidebar ul.components::-webkit-scrollbar {
            display: none;
            /* Chrome/Safari */
        }

        #sidebar .nav-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 20px 25px 10px;
            color: #64748b;
            letter-spacing: 1px;
        }

        #sidebar ul li {
            position: relative;
            list-style: none;
        }

        #sidebar ul li a {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        #sidebar ul li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* HOVER STATE */
        #sidebar ul li a:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        #sidebar ul li.active,
        #sidebar ul li:has(a.active) {
            background: var(--sidebar-hover) !important;
            position: relative;
        }

        /* Garis Biru di Kanan */
        #sidebar ul li.active::after,
        #sidebar ul li:has(a.active)::after {
            content: "";
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: var(--accent-color);
            box-shadow: -2px 0 10px rgba(59, 130, 246, 0.8);
            z-index: 5;
        }

        /* Teks dan Icon Menyala Putih & Biru */
        #sidebar ul li.active>a,
        #sidebar ul li a.active {
            color: #ffffff !important;
            font-weight: 700 !important;
        }

        #sidebar ul li.active>a i,
        #sidebar ul li a.active i {
            color: var(--accent-color) !important;
        }

        /* FOOTER SIDEBAR */
        .sidebar-footer {
            padding: 15px;
            background: #151f33;
            border-top: 1px solid #1e293b;
            flex-shrink: 0;
        }

        /* CONTENT AREA */
        #content {
            flex-grow: 1;
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .top-navbar {
            background: #fff;
            height: 65px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            z-index: 999;
        }

        .main-container {
            padding: 30px;
            overflow-y: auto;
            flex-grow: 1;
            background: var(--bg-main);
        }

        /* Badge Styling in Sidebar Footer */
        .badge {
            letter-spacing: 0.5px;
        }

        .profile-link:hover {
            background: rgba(255, 255, 255, 0.05);
            cursor: pointer;
        }

        .profile-link {
            transition: all 0.2s ease;
        }

        /* Biar teks nama nggak kegeser kalau link diklik */
        .sidebar-footer a {
            color: inherit;
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                {{-- Logo dengan efek garis tepi (outline) lewat CSS --}}
                <img src="{{ asset('logo.png') }}" alt="Logo SIMONS" class="logo-sidebar me-3">

                {{-- Teks Brand --}}
                <div class="d-flex flex-column">
                    <span class="fw-bold text-white fs-5" style="line-height: 1.1; letter-spacing: 1px;">SIMONS</span>
                    <small style="font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #3b82f6; font-weight: 700;">Sistem Monitoring Sekolah</small>
                </div>
            </div>

            <ul class="list-unstyled components">
                {{-- Dashboard - Universal --}}
                <li class="{{ request()->is('dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}"><i class="fa-solid fa-house"></i> Dashboard</a>
                </li>

                @if(Auth::user()->role == 'admin')
                <div class="nav-label">Data Master</div>
                <li class="{{ request()->is('siswa*') ? 'active' : '' }}">
                    <a href="{{ route('siswa.index') }}"><i class="fa-solid fa-users"></i> Data Siswa</a>
                </li>
                <li class="{{ request()->is('kelas*') ? 'active' : '' }}">
                    <a href="{{ route('kelas.index') }}"><i class="fa-solid fa-door-open"></i> Data Kelas</a>
                </li>
                <li class="{{ request()->is('users*') ? 'active' : '' }}">
                    <a href="{{ route('users.index') }}"><i class="fa-solid fa-user-shield"></i> Data User</a>
                </li>
                <li class="{{ request()->is('mapel*') ? 'active' : '' }}">
                    <a href="{{ route('mapel.index') }}"><i class="fa-solid fa-book"></i> Mata Pelajaran</a>
                </li>
                <li class="{{ request()->is('jam-config*') ? 'active' : '' }}">
                    <a href="{{ route('jam-config.index') }}"><i class="fa-solid fa-clock"></i> Pengaturan Jam</a>
                </li>
                <li class="{{ request()->is('jadwal*') ? 'active' : '' }}">
                    <a href="{{ route('jadwal.index') }}"><i class="fa-solid fa-calendar-day"></i> Jadwal Pelajaran</a>
                </li>

                <div class="nav-label">Transaksi & Laporan</div>
                <li class="{{ request()->is('admin/rekap*') ? 'active' : '' }}">
                    <a href="{{ route('rekap.index') }}">
                        <i class="fa-solid fa-file-invoice"></i> Rekapitulasi Presensi
                    </a>
                </li>
                @endif

                @if(Auth::user()->role == 'bk')
                <div class="nav-label">Monitoring & Pembinaan</div>
                <li class="{{ Request::is('bk/laporan*') ? 'active' : '' }}">
                    <a href="{{ route('bk.laporan.alpha') }}">
                        <i class="fa-solid fa-triangle-exclamation"></i> Laporan Alpha
                    </a>
                </li>
                <li class="{{ Request::is('bk/siswa*') ? 'active' : '' }}">
                    <a href="{{ route('bk.siswa.index') }}">
                        <i class="fa-solid fa-users"></i> Profil Siswa
                    </a>
                </li>
                @endif
                @if(Auth::user()->role == 'kepsek')
                <div class="nav-label">Laporan Eksekutif</div>
                <li class="{{ request()->is('kepsek/monitoring-jurnal*') ? 'active' : '' }}">
                    <a href="{{ route('kepsek.monitoring.jurnal') }}">
                        <i class="fa-solid fa-book-open-reader"></i> Keselarasan Materi
                    </a>
                </li>
                <li class="{{ request()->is('kepsek/monitoring-presensi*') ? 'active' : '' }}">
                    <a href="{{ route('kepsek.monitoring.presensi') }}">
                        <i class="fa-solid fa-chart-line"></i> Analisis Presensi
                    </a>
                </li>
                @endif
            </ul>

            <div class="sidebar-footer">
                {{-- Kita bungkus dengan link ke profile.edit --}}
                <a href="{{ route('profile.edit') }}" class="text-decoration-none d-block">
                    <div class="d-flex align-items-center profile-link p-2 rounded transition-all">

                        {{-- Foto Profil --}}
                        <img src="{{ asset('storage/' . Auth::user()->foto_profil) }}"
                            class="rounded-circle me-3 border border-secondary shadow-sm"
                            width="40"
                            height="40"
                            style="object-fit: cover;"
                            onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff';">

                        <div class="overflow-hidden">
                            {{-- Nama User --}}
                            <p class="text-white mb-0 small fw-bold text-truncate" title="{{ Auth::user()->name }}">
                                {{ Auth::user()->name }}
                            </p>

                            {{-- Role --}}
                            @php
                            $roleLabels = [
                            'admin' => ['label' => 'Administrator', 'color' => 'bg-primary'],
                            'bk' => ['label' => 'Guru BK', 'color' => 'bg-danger'],
                            'guru' => ['label' => 'Tenaga Pengajar', 'color' => 'bg-success'],
                            'kepsek' => ['label' => 'Kepala Sekolah', 'color' => 'bg-info text-dark'],
                            ];
                            $currentRole = $roleLabels[Auth::user()->role] ?? ['label' => Auth::user()->role, 'color' => 'bg-secondary'];
                            @endphp

                            <span class="badge {{ $currentRole['color'] }} p-1 mt-1" style="font-size: 9px; letter-spacing: 0.5px; font-weight: 800; text-transform: uppercase;">
                                {{ $currentRole['label'] }}
                            </span>
                        </div>

                        {{-- Icon Indikator (Opsional biar user tau ini bisa diklik) --}}
                        <div class="ms-auto text-muted small opacity-50">
                            <i class="fa-solid fa-chevron-right ms-2"></i>
                        </div>
                    </div>
                </a>
            </div>
        </nav>

        <div id="content">
            <header class="top-navbar">
                <div class="text-muted small">
                    <i class="fa-solid fa-calendar-check text-primary me-2"></i>
                    {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                </div>

                <div class="d-flex align-items-center gap-4">
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger text-decoration-none fw-bold small p-0">
                            <i class="fa-solid fa-power-off me-2"></i>Logout
                        </button>
                    </form>
                </div>
            </header>

            <main class="main-container">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>