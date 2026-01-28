@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2 class="text-primary fw-bold mb-4">Manajemen Jadwal Pelajaran</h2>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }} <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="card shadow border-0 mb-4">
        <div class="card-body bg-light">
            <form action="{{ route('jadwal.index') }}" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="fw-bold mb-2">Pilih Kelas:</label>
                        <select name="kelas_id" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelaslist as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                Kelas {{ $k->nama_kelas }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($kelas_terpilih)
    <div class="card shadow border-0">
        <div class="card-header bg-white fw-bold">
            Jadwal: <span class="text-primary">{{ $kelas_terpilih->nama_kelas }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle mb-0 table-hover">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="10%">Jam</th>
                            <th width="15%">Senin</th>
                            <th width="15%">Selasa</th>
                            <th width="15%">Rabu</th>
                            <th width="15%">Kamis</th>
                            <th width="15%">Jumat</th>
                            <th width="15%">Sabtu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                        // Array untuk melacak cell mana yang harus di-skip karena merge
                        $skipCells = [];
                        @endphp

                        @foreach($jamPelajaran as $indexJam => $jam)
                        <tr>
                            <td class="bg-light fw-bold">{{ $jam }}</td>

                            @foreach($hariList as $hari)
                            @php
                            // 1. CEK APAKAH CELL INI HARUS DI-SKIP (Hidden)
                            // Karena sudah tertutup oleh mapel diatasnya yang durasinya panjang
                            if (isset($skipCells[$hari][$jam])) {
                            continue;
                            }

                            // 2. CEK APAKAH ADA JADWAL DI JAM & HARI INI
                            $jadwal = $jadwalMatrix[$hari][$jam] ?? null;
                            @endphp

                            @if($jadwal)
                            @php
                            // --- LOGIKA BARU MENGHITUNG ROWSPAN (LEBIH AKURAT) ---
                            $rowspan = 1;
                            $jamSelesaiJadwal = \Carbon\Carbon::parse($jadwal->jam_selesai);

                            // Kita intip jam-jam berikutnya di array $jamPelajaran
                            // Mulai dari index slot berikutnya
                            for ($i = $indexJam + 1; $i < count($jamPelajaran); $i++) {
                                $nextSlot=$jamPelajaran[$i];
                                $waktuNextSlot=\Carbon\Carbon::parse($nextSlot);

                                // Jika slot berikutnya masih KURANG DARI jam selesai jadwal...
                                // Berarti slot itu dimakan oleh jadwal ini.
                                if ($waktuNextSlot->lt($jamSelesaiJadwal)) {
                                $rowspan++; // Tambah tinggi sel
                                $skipCells[$hari][$nextSlot] = true; // Tandai slot itu biar ga dirender nanti
                                } else {
                                break; // Stop jika sudah lewat jam selesai
                                }
                                }
                                @endphp

                                <td rowspan="{{ $rowspan }}" class="bg-info bg-opacity-10 position-relative p-2 align-top border-bottom border-info">
                                    <div class="fw-bold text-primary">{{ $jadwal->nama_mapel }}</div>
                                    <div style="font-size: 11px;" class="text-muted mb-2"><i class="fas fa-user-tie me-1"></i> {{ Str::limit($jadwal->nama_guru, 15) }}</div>

                                    <div class="badge bg-white text-dark border shadow-sm">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </div>

                                    <form action="{{ route('jadwal.destroy', $jadwal->id) }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')" class="position-absolute top-0 end-0 m-1">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-link text-danger p-0" style="font-size: 10px;"><i class="fas fa-times"></i></button>
                                    </form>
                                </td>

                                @else
                                <td class="cell-hover text-center" onclick="bukaModal('{{ $hari }}', '{{ $jam }}')" style="cursor: pointer;">
                                    <span class="text-muted opacity-25"><i class="fas fa-plus"></i></span>
                                </td>
                                @endif

                                @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5 text-muted">Silakan pilih kelas terlebih dahulu.</div>
    @endif
</div>

<div class="modal fade" id="modalJadwal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('jadwal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ request('kelas_id') }}">

                <div class="modal-header">
                    <h5 class="modal-title">Isi Jadwal: <span id="labelHari"></span>, <span id="labelJam"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="hari" id="inputHari">
                    <input type="hidden" name="jam_mulai" id="inputJamMulai">

                    <div class="mb-3">
                        <label class="form-label">Mata Pelajaran</label>
                        <select name="mapel_id" class="form-select" required>
                            <option value="">-- Pilih Mapel --</option>
                            @if(isset($mapel))
                            @foreach($mapel as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Guru Pengajar</label>
                        <select name="guru_id" class="form-select" required>
                            <option value="">-- Pilih Guru --</option>
                            @if(isset($guru))
                            @foreach($guru as $g)
                            <option value="{{ $g->id }}">{{ $g->name }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selesai Jam Berapa?</label>
                        <input type="time" name="jam_selesai" class="form-control" id="inputJamSelesai" required>
                        <small class="text-muted">Jam Mulai otomatis: <span id="textJamMulai"></span></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function bukaModal(hari, jam) {
        // Isi Label di Modal
        document.getElementById('labelHari').innerText = hari;
        document.getElementById('labelJam').innerText = jam;
        document.getElementById('textJamMulai').innerText = jam;

        // Isi Input Hidden
        document.getElementById('inputHari').value = hari;
        document.getElementById('inputJamMulai').value = jam;

        // Otomatis set jam selesai (+40 menit biar cepat)
        // Ini butuh sedikit logika string manipulation sederhana
        let [h, m] = jam.split(':');
        let date = new Date();
        date.setHours(h);
        date.setMinutes(parseInt(m) + 40); // Tambah 40 menit default

        let hEnd = String(date.getHours()).padStart(2, '0');
        let mEnd = String(date.getMinutes()).padStart(2, '0');
        document.getElementById('inputJamSelesai').value = hEnd + ":" + mEnd;

        // Tampilkan Modal
        new bootstrap.Modal(document.getElementById('modalJadwal')).show();
    }
</script>

<style>
    /* Efek hover biar enak dilihat pas mau klik tambah */
    .cell-hover:hover {
        background-color: #f8f9fa;
        color: var(--bs-primary) !important;
    }

    .cell-hover:hover span {
        opacity: 1 !important;
    }
</style>

@endsection