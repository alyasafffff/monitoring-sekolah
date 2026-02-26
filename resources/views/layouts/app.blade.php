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
            --bg-main: #f8fafc;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-main);
            height: 100vh;
            /* Kunci tinggi body */
            overflow: hidden;
            /* Matikan scroll body utama */
        }

        /* Layout Structure */
        .wrapper {
            display: flex;
            height: 100vh;
            /* Wrapper harus setinggi layar */
            width: 100vw;
        }

        /* Sidebar Styling */
        #sidebar {
            min-width: 260px;
            max-width: 260px;
            background: var(--sidebar-bg);
            color: #94a3b8;
            display: flex;
            flex-direction: column;
            /* Biar bisa bagi area menu & footer */
            height: 100vh;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        #sidebar .sidebar-header {
            padding: 20px;
            background: var(--sidebar-bg);
            border-bottom: 1px solid #1e293b;
            flex-shrink: 0;
            /* Header jangan ikutan mengecil */
        }

        /* AREA MENU: Dikasih Scroll Sendiri */
        #sidebar ul.components {
            padding: 15px 0;
            flex-grow: 1;
            overflow-y: auto;
            /* Munculkan scroll jika menu kepanjangan */
            scrollbar-width: none;
            /* Sembunyikan scrollbar di Firefox */
        }

        #sidebar ul.components::-webkit-scrollbar {
            display: none;
        }

        /* Sembunyikan scrollbar di Chrome/Safari */

        #sidebar .nav-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 20px 25px 10px;
            color: #64748b;
            letter-spacing: 1px;
        }

        #sidebar ul li a {
            padding: 12px 25px;
            display: block;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        #sidebar ul li.active>a {
            background: var(--sidebar-hover);
            border-right: 4px solid var(--accent-color);
            color: var(--accent-color);
        }

        /* FOOTER SIDEBAR: Tetap Diam di Bawah */
        .sidebar-footer {
            padding: 15px;
            background: #151f33;
            /* Warna agak beda biar tegas */
            border-top: 1px solid #1e293b;
            flex-shrink: 0;
            /* Footer jangan ikutan mengecil */
        }

        /* AREA KONTEN: Dikasih Scroll Sendiri */
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
            /* Konten utama bisa di-scroll */
            flex-grow: 1;
            background: var(--bg-main);
        }
    </style>
</head>

<body>

    <div class="wrapper">
        <nav id="sidebar">
            <div class="sidebar-header">
                <i class="fa-solid fa-school text-primary fs-3 me-3"></i>
                <span class="fw-bold text-white fs-5 tracking-wide">SiMon</span>
            </div>

            <ul class="list-unstyled components">
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
                @endif

                @if(Auth::user()->role == 'bk')
                <div class="nav-label">Monitoring</div>
                <li><a href="#"><i class="fa-solid fa-triangle-exclamation"></i> Pelanggaran</a></li>
                @endif
            </ul>

            <div class="sidebar-footer">
                <div class="d-flex align-items-center">
                    {{-- Foto Profil Dinamis --}}
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

                        {{-- Role dengan Visual Bagus --}}
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
                </div>
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