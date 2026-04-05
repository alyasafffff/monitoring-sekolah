<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMONS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* BACKGROUND UTAMA - Kita buat lebih deep dan bertekstur */
        body {
            background: radial-gradient(circle at top left, #1a3c5d 0%, #0a192f 100%);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }

        /* Pola dekoratif halus di background utama agar tidak flat */
        body::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url("https://www.transparenttextures.com/patterns/carbon-fibre.png");
            opacity: 0.1;
            pointer-events: none;
        }

        .login-container {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 950px;
            display: flex;
            min-height: 580px;
            z-index: 1;
        }

        /* Sisi Kiri - Putih Bersih agar Logo Pop Out */
        .login-visual {
            background-color: #ffffff;
            flex: 1.2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px;
            text-align: center;
            position: relative;
        }

        /* Aksen garis orange di antara kiri dan kanan */
        .login-visual::after {
            content: "";
            position: absolute;
            right: 0;
            top: 20%;
            height: 60%;
            width: 1px;
            background: linear-gradient(to bottom, transparent, #ff8c00, transparent);
        }

        .brand-img {
            max-width: 240px;
            margin-bottom: 25px;
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.08));
        }

        .brand-title {
            color: #1a3c5d;
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 0;
        }

        .brand-subtitle {
            color: #ff8c00; /* Gunakan Orange agar lebih hidup */
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Sisi Kanan - Form Login */
        .login-form-section {
            flex: 1;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-color: #fcfcfc;
        }

        .form-label {
            color: #1a3c5d;
            font-size: 0.85rem;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 12px;
            padding: 14px 18px;
            border: 2px solid #edf2f7;
            background-color: #f8fafc;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #266bb0;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(38, 107, 176, 0.1);
        }

        .btn-primary {
            background: #1a3c5d;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            transition: 0.3s;
            margin-top: 10px;
        }

        .btn-primary:hover {
            background: #ff8c00;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(255, 140, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-container { flex-direction: column; max-width: 450px; }
            .login-visual::after { display: none; }
            .login-visual { padding: 40px 20px; border-bottom: 1px solid #eee; }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="login-visual">
            <img src="{{ asset('logo.png') }}" alt="Logo SIMONS" class="brand-img">
            <h2 class="brand-title">SIMONS</h2>
            <p class="brand-subtitle">Sistem Monitoring Sekolah</p>
        </div>

<div class="login-form-section">
    <div class="mb-3">
        <h3 class="fw-bold" style="color: #1a3c5d;">Otentikasi Pengguna</h3>
        <p class="text-muted">Akses dasbor manajemen dan monitoring sekolah</p>
    </div>

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-bold text-uppercase small" style="letter-spacing: 1px;">Nomor Induk Pegawai (NIP)</label>
            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP" required autofocus>
        </div>
        <div class="mb-4">
            <label class="form-label fw-bold text-uppercase small" style="letter-spacing: 1px;">Kata Sandi</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 shadow-sm">MASUK</button>
    </form>
    
    <div class="mt-5 text-center">
        <p class="small text-muted mb-0">Kendala akses? <span href="#" class="text-decoration-none fw-bold" style="color: #ff8c00;">Hubungi Administrator IT</span></p>
    </div>
</div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>