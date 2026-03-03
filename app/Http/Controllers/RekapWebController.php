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

    // FUNGSI INI UNTUK MENGAMBIL DATA AGAR TIDAK DUPLIKAT
private function getRekapData(Request $request)
    {
        // 1. Inisialisasi Parameter Filter
        $daftarKelas = DB::table('kelas')->get();
        $selectedKelas = $request->get('kelas_id');
        $tipe = $request->get('tipe', 'bulanan');
        $tahun = $request->get('tahun', date('Y'));
        $selectedBulan = $request->get('bulan', date('m'));
        $selectedTahun = $tahun;
        $semester = $request->get('semester', '1');

        $dataSiswa = [];
        $listTanggal = [];
        $infoKelas = null;

        if ($selectedKelas) {
            $infoKelas = DB::table('kelas')->where('id', $selectedKelas)->first();

            // 2. Logika Penentuan Rentang Tanggal
            if ($tipe == 'bulanan') {
                $start = Carbon::createFromDate($tahun, $selectedBulan, 1)->startOfMonth();
                $end = $start->copy()->endOfMonth();
                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    $listTanggal[] = $date->format('Y-m-d');
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

            // 5. Mapping & Penentuan Prioritas Status Harian (Hierarki: S > I > H > A)
            $tempLookup = [];
            
            // Masukkan data presensi per mapel ke penampung sementara
            foreach ($presensiRaw as $p) {
                $char = substr($p->status, 0, 1);
                $finalStatus = ($char == 'D') ? 'I' : $char; // Dispensasi lebur ke Izin
                $tempLookup[$p->siswa_id][$p->tanggal][] = $finalStatus;
            }

            // Masukkan data izin harian ke penampung sementara
            foreach ($izinRaw as $i) {
                $char = substr($i->status, 0, 1);
                $finalStatus = ($char == 'D') ? 'I' : $char; // Dispensasi lebur ke Izin
                $tempLookup[$i->siswa_id][$i->tanggal][] = $finalStatus;
            }

            // Tentukan satu status final per hari
            $lookup = [];
            foreach ($tempLookup as $sId => $dates) {
                foreach ($dates as $tgl => $statuses) {
                    if (in_array('S', $statuses)) {
                        $lookup[$sId][$tgl] = 'S';
                    } elseif (in_array('I', $statuses)) {
                        $lookup[$sId][$tgl] = 'I';
                    } elseif (in_array('H', $statuses)) {
                        // Jika ada 1 saja mapel yang hadir, rekap harian dianggap Hadir (H)
                        $lookup[$sId][$tgl] = 'H';
                    } else {
                        // Jika semua sesi/mapel statusnya Alpha (A)
                        $lookup[$sId][$tgl] = 'A';
                    }
                }
            }

            // 6. Mapping Akhir & Hitung Persentase untuk View/Excel
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
                    'nama' => $s->nama_siswa,
                    'nisn' => $s->nisn,
                    'grid' => $kehadiran,
                    'total' => $total,
                    'persen' => $persen
                ];
            });
        }

        return [
            'dataSiswa' => $dataSiswa,
            'listTanggal' => $listTanggal,
            'daftarKelas' => $daftarKelas,
            'tipe' => $tipe,
            'selectedKelas' => $selectedKelas,
            'selectedBulan' => $selectedBulan,
            'selectedTahun' => $selectedTahun,
            'infoKelas' => $infoKelas,
            'semester' => $semester
        ];
    }
}
