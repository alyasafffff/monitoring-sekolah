<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\SiswaPresensiExport;
use Maatwebsite\Excel\Facades\Excel;


class RekapWebController extends Controller
{
    public function index(Request $request)
    {
        $rekapData = $this->getRekapData($request);
        return view('dashboard.rekap.index', $rekapData);
    }

    public function export(Request $request)
    {
        // Ambil data dalam bentuk ARRAY
        $rekapData = $this->getRekapData($request);

        // Pastikan datanya ada
        if (empty($rekapData['dataSiswa'])) {
            return back()->with('error', 'Tidak ada data untuk dieksport');
        }

        $namaFile = 'Rekap_Presensi_' . $rekapData['infoKelas']->nama_kelas . '.xlsx';

        // Kirim $rekapData (yang sudah pasti Array) ke class Export
        return Excel::download(new SiswaPresensiExport($rekapData), $namaFile);
    }

    // ==========================================
    // FUNGSI BARU: UPDATE STATUS OLEH ADMIN
    // ==========================================
    public function updateStatus(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            return back()->with('error', 'Akses ditolak. Hanya Admin yang dapat mengubah data rekapitulasi.');
        }

        $request->validate([
            'siswa_id'   => 'required|integer',
            'tanggal'    => 'required|date',
            'status'     => 'required|in:H,I,S,A',
            'bukti_foto' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' 
        ]);

        $siswaId = $request->siswa_id;
        $tanggal = $request->tanggal;
        $statusBaru = $request->status; // Ini isinya 'H', 'I', 'S', atau 'A'

        // --- TAMBAHAN BARU: Mapping Status untuk Tabel presensi_detail ---
        $mapStatusPresensi = [
            'H' => 'Hadir',
            'I' => 'Izin',
            'S' => 'Sakit',
            'A' => 'Alpha'
        ];
        $statusPresensiFull = $mapStatusPresensi[$statusBaru];

        $pathBukti = null;
        if ($request->hasFile('bukti_foto')) {
            $file = $request->file('bukti_foto');
            $namaFile = time() . '_revisi_' . $file->getClientOriginalName();
            $pathBukti = $file->storeAs('bukti_izin', $namaFile, 'public'); 
        }

        $siswa = DB::table('siswa')->where('id', $siswaId)->first();

        if ($siswa) {
            // --- CARI STATUS LAMA UNTUK LOG HISTORY ---
            $statusLama = '-';
            $cekIzin = DB::table('izin_siswa')->where('siswa_id', $siswaId)->where('tanggal_izin', $tanggal)->first();
            if ($cekIzin) {
                $statusLama = ($cekIzin->status == 'Izin') ? 'I' : 'S';
            } else {
                $cekPresensi = DB::table('presensi_detail')
                    ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                    ->where('presensi_detail.siswa_id', $siswaId)
                    ->where('jurnals.tanggal', $tanggal)
                    ->value('presensi_detail.status');
                
                if ($cekPresensi) {
                    $char = substr($cekPresensi, 0, 1);
                    $statusLama = ($char == 'D') ? 'I' : $char;
                }
            }

            // --- PROSES TIMPA DATA KE PRESENSI_DETAIL ---
            $jurnalIds = DB::table('jurnals')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->where('jadwal_pelajaran.kelas_id', $siswa->kelas_id)
                ->where('jurnals.tanggal', $tanggal)
                ->pluck('jurnals.id');

            if ($jurnalIds->isNotEmpty()) {
                DB::table('presensi_detail')
                    ->whereIn('jurnal_id', $jurnalIds)
                    ->where('siswa_id', $siswaId)
                    // GUNAKAN $statusPresensiFull DI SINI
                    ->update(['status' => $statusPresensiFull]); 
            }

            // --- PROSES TIMPA DATA KE IZIN_SISWA ---
            if (in_array($statusBaru, ['I', 'S'])) {
                $statusEnum = ($statusBaru == 'I') ? 'Izin' : 'Sakit';

                if ($cekIzin) {
                    $updateData = ['status' => $statusEnum];
                    if ($pathBukti) {
                        $updateData['bukti_foto'] = $pathBukti;
                    }
                    DB::table('izin_siswa')->where('id', $cekIzin->id)->update($updateData);
                } else {
                    DB::table('izin_siswa')->insert([
                        'siswa_id'       => $siswaId,
                        'wali_kelas_id'  => $user->id, 
                        'tanggal_izin'   => $tanggal,
                        'status'         => $statusEnum,
                        'keterangan'     => 'Direvisi oleh Admin melalui rekapitulasi',
                        'bukti_foto'     => $pathBukti,
                        'created_at'     => now(),
                        'updated_at'     => now()
                    ]);
                }
            } else {
                DB::table('izin_siswa')
                    ->where('siswa_id', $siswaId)
                    ->where('tanggal_izin', $tanggal)
                    ->delete();
            }

            // --- PROSES INSERT LOG HISTORY ---
            if ($statusLama !== $statusBaru) {
                DB::table('log_revisi_presensi')->insert([
                    'admin_id'         => $user->id,
                    'siswa_id'         => $siswaId,
                    'tanggal_presensi' => $tanggal,
                    'status_lama'      => $statusLama,
                    'status_baru'      => $statusBaru,
                    'keterangan'       => 'Revisi data via halaman rekapitulasi',
                    'bukti_foto'       => $pathBukti, // <--- PASTIKAN BARIS INI ADA
                    'created_at'       => now(),
                    'updated_at'       => now()
                ]);
            }
        }

        return redirect()->back()->with('success', 'Status siswa berhasil direvisi dan dicatat di riwayat.');
    }

    // FUNGSI INI UNTUK MENGAMBIL DATA AGAR TIDAK DUPLIKAT
    // FUNGSI INI UNTUK MENGAMBIL DATA AGAR TIDAK DUPLIKAT
    private function getRekapData(Request $request)
    {
        // 1. Inisialisasi Parameter Filter
        $daftarKelas = DB::table('kelas')->get();
        $selectedKelas = $request->get('kelas_id');
        $tipe = $request->get('tipe', 'bulanan');
        $tahun = $request->get('tahun', date('Y'));

        // Kita ambil bulan awal dan akhir. Jika tidak ada, default ke bulan sekarang
        $bulanAwal = $request->get('bulan_awal', date('m'));
        $bulanAkhir = $request->get('bulan_akhir', date('m'));
        $semester = $request->get('semester', '1');

        $dataSiswa = [];
        $listTanggal = [];
        $infoKelas = null;
        $historyRevisi = []; // <-- Inisialisasi array kosong untuk history

        if ($selectedKelas) {
            $infoKelas = DB::table('kelas')->where('id', $selectedKelas)->first();

            // 2. Logika Penentuan Rentang Tanggal
            if ($tipe == 'bulanan') {
                $start = Carbon::createFromDate($tahun, $bulanAwal, 1)->startOfMonth();
                $end = Carbon::createFromDate($tahun, $bulanAkhir, 1)->endOfMonth();

                // Keamanan rentang waktu
                if ($end->lt($start)) {
                    $end = $start->copy()->endOfMonth();
                }

                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    if (!$date->isSunday()) {
                        $listTanggal[] = $date->format('Y-m-d');
                    }
                }
            } else {
                // Mode Semester
                $startMonth = ($semester == '1') ? 7 : 1;
                $endMonth = ($semester == '1') ? 12 : 6;
                $start = Carbon::createFromDate($tahun, $startMonth, 1)->startOfMonth();
                $end = Carbon::createFromDate($tahun, $endMonth, 1)->endOfMonth();

                $listTanggal = DB::table('jurnals')
                    ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                    ->where('jadwal_pelajaran.kelas_id', $selectedKelas)
                    ->whereBetween('jurnals.tanggal', [$start, $end])
                    ->orderBy('jurnals.tanggal')
                    ->distinct()
                    ->pluck('jurnals.tanggal')
                    ->toArray();
            }

            // 3. Ambil Data Dasar (Siswa)
            $siswa = DB::table('siswa')->where('kelas_id', $selectedKelas)->orderBy('nama_siswa')->get();

            // 4. Ambil Data Transaksi (Presensi & Izin)
            $presensiRaw = DB::table('presensi_detail')
                ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
                ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
                ->whereIn('jurnals.tanggal', $listTanggal)
                ->where('jadwal_pelajaran.kelas_id', $selectedKelas)
                ->select('presensi_detail.siswa_id', 'jurnals.tanggal', 'presensi_detail.status')
                ->get();

            $izinRaw = DB::table('izin_siswa')
                ->whereIn('tanggal_izin', $listTanggal)
                ->select('siswa_id', 'tanggal_izin as tanggal', 'status')
                ->get();

            // --- Logika mapping tempLookup ---
            $tempLookup = [];
            foreach ($presensiRaw as $p) {
                $char = substr($p->status, 0, 1);
                $finalStatus = ($char == 'D') ? 'I' : $char;
                $tempLookup[$p->siswa_id][$p->tanggal][] = $finalStatus;
            }

            foreach ($izinRaw as $i) {
                $char = substr($i->status, 0, 1);
                $finalStatus = ($char == 'D') ? 'I' : $char;
                $tempLookup[$i->siswa_id][$i->tanggal][] = $finalStatus;
            }

            $lookup = [];
            foreach ($tempLookup as $sId => $dates) {
                foreach ($dates as $tgl => $statuses) {
                    if (in_array('S', $statuses)) {
                        $lookup[$sId][$tgl] = 'S';
                    } elseif (in_array('I', $statuses)) {
                        $lookup[$sId][$tgl] = 'I';
                    } elseif (in_array('H', $statuses)) {
                        $lookup[$sId][$tgl] = 'H';
                    } else {
                        $lookup[$sId][$tgl] = 'A';
                    }
                }
            }

            // 6. Mapping Akhir & Hitung Persentase
            $dataSiswa = $siswa->map(function ($s) use ($listTanggal, $lookup) {
                $kehadiran = [];
                $total = ['H' => 0, 'S' => 0, 'I' => 0, 'A' => 0];

                foreach ($listTanggal as $tgl) {
                    $status = $lookup[$s->id][$tgl] ?? '-';
                    $kehadiran[$tgl] = $status;
                    if (isset($total[$status])) {
                        $total[$status]++;
                    }
                }

                $totalPertemuan = count($listTanggal);
                $persen = [
                    'H' => $totalPertemuan > 0 ? round(($total['H'] / $totalPertemuan) * 100, 1) : 0,
                    'S' => $totalPertemuan > 0 ? round(($total['S'] / $totalPertemuan) * 100, 1) : 0,
                    'I' => $totalPertemuan > 0 ? round(($total['I'] / $totalPertemuan) * 100, 1) : 0,
                    'A' => $totalPertemuan > 0 ? round(($total['A'] / $totalPertemuan) * 100, 1) : 0,
                ];

                return [
                    'id'     => $s->id,
                    'nama'   => $s->nama_siswa,
                    'nisn'   => $s->nisn,
                    'grid'   => $kehadiran,
                    'total'  => $total,
                    'persen' => $persen
                ];
            });

            // --- 7. AMBIL DATA HISTORY REVISI ---
            $historyRevisi = DB::table('log_revisi_presensi')
                ->join('siswa', 'log_revisi_presensi.siswa_id', '=', 'siswa.id')
                ->join('users', 'log_revisi_presensi.admin_id', '=', 'users.id')
                ->where('siswa.kelas_id', $selectedKelas) // Ambil yang sekelas saja
                ->select(
                    'log_revisi_presensi.*',
                    'siswa.nama_siswa',
                    'users.name as nama_admin' 
                )
                ->orderBy('log_revisi_presensi.created_at', 'desc') // Urutkan dari yang terbaru
                ->get();
        }

        // Return semua data ke View
        return [
            'dataSiswa' => $dataSiswa,
            'listTanggal' => $listTanggal,
            'daftarKelas' => $daftarKelas,
            'tipe' => $tipe,
            'selectedKelas' => $selectedKelas,
            'bulanAwal' => $bulanAwal,
            'bulanAkhir' => $bulanAkhir,
            'selectedTahun' => $tahun,
            'infoKelas' => $infoKelas,
            'semester' => $semester,
            'historyRevisi' => $historyRevisi // <-- Data dilempar ke view
        ];
    }
}