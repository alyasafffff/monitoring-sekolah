@extends('layouts.app') {{-- Sesuaikan dengan nama layout admin kamu --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i> Rekapitulasi Presensi Siswa</h5>
                </div>
                <div class="card-body">

                    {{-- Alert Notifikasi Sukses / Error --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <form action="{{ route('rekap.index') }}" method="GET" class="row g-3 mb-4 p-3 bg-light rounded border">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Pilih Kelas</label>
                            <select name="kelas_id" class="form-select" required>
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($daftarKelas as $kelas)
                                <option value="{{ $kelas->id }}" {{ $selectedKelas == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Dari Bulan</label>
                            <select name="bulan_awal" id="bulan_awal" class="form-select bulan-select">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulanAwal == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Sampai Bulan</label>
                            <select name="bulan_akhir" id="bulan_akhir" class="form-select bulan-select">
                                @foreach(range(1, 12) as $m)
                                <option value="{{ sprintf('%02d', $m) }}" {{ $bulanAkhir == sprintf('%02d', $m) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label fw-bold">Tahun</label>
                            <select name="tahun" id="tahun_filter" class="form-select">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                <option value="{{ $y }}" {{ $selectedTahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i> Filter Data
                            </button>
                        </div>
                    </form>

                    @if($selectedKelas)
                    <div class="d-flex justify-content-between align-items-center mb-4 p-2 bg-white rounded shadow-sm border">
                        <div>
                            <h6 class="fw-bold mb-1 text-dark">
                                <i class="fas fa-chalkboard-teacher me-2 text-primary"></i>Data Kelas:
                                <span class="badge bg-primary">{{ $daftarKelas->where('id', $selectedKelas)->first()->nama_kelas }}</span>
                            </h6>
                            <small class="text-muted">
                                Periode: {{ date('F', mktime(0, 0, 0, $bulanAwal, 1)) }} s/d {{ date('F Y', mktime(0, 0, 0, $bulanAkhir, 1, $selectedTahun)) }}
                            </small>
                        </div>

                        <div class="btn-group gap-2">
                            {{-- Tombol Pemicu Modal Riwayat Perubahan --}}
                            <button type="button" class="btn btn-outline-secondary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalHistoryRevisi">
                                <i class="fas fa-history me-1"></i> Riwayat Perubahan
                            </button>

                            {{-- Tombol Langsung Export sesuai Filter --}}
                            <a href="{{ route('rekap.export', [
                                'tipe' => 'bulanan', 
                                'kelas_id' => $selectedKelas, 
                                'bulan_awal' => $bulanAwal, 
                                'bulan_akhir' => $bulanAkhir, 
                                'tahun' => $selectedTahun
                            ]) }}" class="btn btn-success shadow-sm">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th rowspan="2" class="align-middle">No</th>
                                    <th rowspan="2" class="align-middle">Nama Siswa</th>
                                    <th colspan="{{ count($listTanggal) }}">Tanggal / Pertemuan</th>
                                    <th colspan="5">Total</th>
                                </tr>
                                <tr>
                                    @foreach($listTanggal as $tgl)
                                    <th style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($tgl)->format('d/m') }}</th>
                                    @endforeach
                                    <th>H</th>
                                    <th>S</th>
                                    <th>I</th>
                                    <th>A</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dataSiswa as $siswa)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-nowrap">{{ $siswa['nama'] }}</td>

                                    @foreach($siswa['grid'] as $tgl => $status)
                                    <td class="text-center align-middle p-1">
                                        @if ($status == '-')
                                            <span class="text-muted">-</span>
                                        @else
                                            @if(auth()->check() && auth()->user()->role === 'admin')
                                                {{-- Tombol pemicu modal update status --}}
                                                <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold p-0 
                                                    @if($status == 'A') text-danger 
                                                    @elseif($status == 'H') text-success 
                                                    @elseif($status == 'I' || $status == 'S') text-warning 
                                                    @endif"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalUpdateStatus"
                                                    data-siswa-id="{{ $siswa['id'] }}"
                                                    data-siswa-nama="{{ $siswa['nama'] }}"
                                                    data-tanggal="{{ $tgl }}"
                                                    data-status-awal="{{ $status }}">
                                                    {{ $status }}
                                                </button>
                                            @else
                                                {{-- Tampilan untuk non-admin --}}
                                                <span class="fw-bold 
                                                    @if($status == 'A') text-danger 
                                                    @elseif($status == 'H') text-success 
                                                    @elseif($status == 'I' || $status == 'S') text-warning 
                                                    @endif">
                                                    {{ $status }}
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                    @endforeach

                                    <td class="text-center bg-light">{{ $siswa['total']['H'] }}</td>
                                    <td class="text-center bg-light">{{ $siswa['total']['S'] }}</td>
                                    <td class="text-center bg-light">{{ $siswa['total']['I'] }}</td>
                                    <td class="text-center bg-light text-danger">{{ $siswa['total']['A'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <img src="https://illustrations.popsy.co/blue/searching.svg" style="height: 150px;" alt="search">
                        <p class="mt-3 text-secondary">Silahkan pilih filter kelas untuk menampilkan data rekapitulasi.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 1: HISTORY REVISI -->
        <!-- ============================================== -->
        <div class="modal fade" id="modalHistoryRevisi" tabindex="-1" aria-labelledby="modalHistoryLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-secondary text-white">
                        <h5 class="modal-title" id="modalHistoryLabel">
                            <i class="fas fa-history me-2"></i>Riwayat Perubahan Data Kelas {{ $infoKelas->nama_kelas ?? '' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-0">
                        @if(isset($historyRevisi) && count($historyRevisi) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0 text-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Waktu Diubah</th>
                                        <th>Oleh (Admin)</th>
                                        <th>Nama Siswa</th>
                                        <th>Tgl Presensi</th>
                                        <th class="text-center">Perubahan</th>
                                        <th class="text-center">Bukti</th> {{-- Kolom Tambahan --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyRevisi as $log)
                                    <tr>
                                        <td class="ps-3 text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y, H:i') }}</td>
                                        <td>{{ $log->nama_admin }}</td>
                                        <td><span class="fw-bold">{{ $log->nama_siswa }}</span></td>
                                        <td>{{ \Carbon\Carbon::parse($log->tanggal_presensi)->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">{{ $log->status_lama }}</span>
                                            <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                            <span class="badge bg-primary">{{ $log->status_baru }}</span>
                                        </td>
                                        <td class="text-center">
                                            {{-- Menampilkan tombol lihat foto jika data bukti_foto ada --}}
                                            @if($log->bukti_foto)
                                                <a href="{{ asset('storage/' . $log->bukti_foto) }}" target="_blank" class="btn btn-sm btn-info text-white shadow-sm" title="Lihat Bukti Foto">
                                                    <i class="fas fa-image"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                            <h6>Belum ada riwayat perubahan</h6>
                            <p class="small">Data presensi di kelas ini belum pernah direvisi oleh Admin.</p>
                        </div>
                        @endif
                    </div>

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- MODAL 2: UPDATE STATUS (UPLOAD BUKTI) -->
        <!-- ============================================== -->
        <div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-labelledby="modalUpdateLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <form action="{{ route('rekap.update_status') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="modalUpdateLabel"><i class="fas fa-edit me-2"></i>Revisi Kehadiran</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            {{-- Hidden Inputs --}}
                            <input type="hidden" name="siswa_id" id="modal_siswa_id">
                            <input type="hidden" name="tanggal" id="modal_tanggal">
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Nama Siswa</label>
                                <input type="text" class="form-control bg-light fw-bold" id="modal_siswa_nama" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small mb-1">Tanggal Presensi</label>
                                <input type="text" class="form-control bg-light fw-bold" id="modal_tanggal_text" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Ubah Status Menjadi <span class="text-danger">*</span></label>
                                <select name="status" id="modal_status" class="form-select" required>
                                    <option value="H">Hadir (H)</option>
                                    <option value="I">Izin (I)</option>
                                    <option value="S">Sakit (S)</option>
                                    <option value="A">Alpha (A)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Lampirkan Bukti/Foto (Opsional)</label>
                                <input type="file" name="bukti_foto" class="form-control @error('bukti_foto') is-invalid @enderror" accept=".jpg,.jpeg,.png,.pdf">
                                @error('bukti_foto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text text-muted">
                                    <i class="fas fa-info-circle me-1"></i>Format JPG, PNG, atau PDF. Maksimal 2MB. Disarankan jika status Sakit/Izin.
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Revisi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Script untuk Disable Bulan Masa Depan ---
        const bulanSelects = document.querySelectorAll('.bulan-select');
        const tahunSelect = document.getElementById('tahun_filter');
        const currentMonth = new Date().getMonth() + 1; 
        const currentYear = new Date().getFullYear();

        function lockFutureMonths() {
            const selectedYear = parseInt(tahunSelect.value);
            bulanSelects.forEach(select => {
                Array.from(select.options).forEach(option => {
                    const optionMonth = parseInt(option.value);
                    if (selectedYear === currentYear && optionMonth > currentMonth) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        if(tahunSelect) {
            tahunSelect.addEventListener('change', lockFutureMonths);
            lockFutureMonths(); 
        }

        // --- 2. Script untuk Transfer Data ke Modal Update Status ---
        const modalUpdateStatus = document.getElementById('modalUpdateStatus');
        if (modalUpdateStatus) {
            modalUpdateStatus.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                
                // Ambil data dari atribut tombol yang diklik
                const siswaId = button.getAttribute('data-siswa-id');
                const siswaNama = button.getAttribute('data-siswa-nama');
                const tanggal = button.getAttribute('data-tanggal');
                const statusAwal = button.getAttribute('data-status-awal');
                
                // Masukkan ke input form dalam modal
                modalUpdateStatus.querySelector('#modal_siswa_id').value = siswaId;
                modalUpdateStatus.querySelector('#modal_tanggal').value = tanggal;
                modalUpdateStatus.querySelector('#modal_siswa_nama').value = siswaNama;
                
                // Format tanggal untuk tampilan teks biasa (DD/MM/YYYY)
                const dateObj = new Date(tanggal);
                const formattedDate = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
                modalUpdateStatus.querySelector('#modal_tanggal_text').value = formattedDate;
                
                // Set default dropdown sesuai status yang sedang berjalan saat ini
                modalUpdateStatus.querySelector('#modal_status').value = statusAwal !== '-' ? statusAwal : 'H';
            });
        }
    });
</script>
@endsection