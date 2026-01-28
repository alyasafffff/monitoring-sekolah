<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class JadwalWebController extends Controller
{
    // KITA DEFINISIKAN JAM PELAJARAN (Slot 40 Menit)
    // Nanti bisa kamu taruh di database/config jika mau dinamis
    private $jamPelajaran = [
        '07:00', '07:40', '08:20', '09:00', '09:40', // Istirahat biasanya di sela-sela ini
        '10:20', '11:00', '11:40', '12:20', '13:00'
    ];

    public function index(Request $request)
    {
        $kelaslist = DB::table('kelas')->orderBy('nama_kelas', 'asc')->get();
        
        $jadwalMatrix = [];
        $kelas_terpilih = null;
        $mapel = [];
        $guru = [];

        if ($request->has('kelas_id')) {
            $kelas_terpilih = DB::table('kelas')->where('id', $request->kelas_id)->first();
            
            // Ambil data untuk Modal Dropdown
            $mapel = DB::table('mata_pelajaran')->orderBy('nama_mapel')->get();
            $guru = DB::table('users')->where('role', 'guru')->orderBy('name')->get();

            // Ambil Jadwal Raw
            $rawJadwal = DB::table('jadwal_pelajaran')
                ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
                ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
                ->where('jadwal_pelajaran.kelas_id', $request->kelas_id)
                ->select(
                    'jadwal_pelajaran.*', 
                    'mata_pelajaran.nama_mapel', 
                    'mata_pelajaran.kode_mapel',
                    'users.name as nama_guru'
                )
                ->get();

            // --- LOGIKA MAPPING KE MATRIKS ---
            // Kita susun data biar mudah dipanggil di View: $matrix['Senin']['07:00']
            foreach ($rawJadwal as $j) {
                // Format jam biar pas pencocokannya (H:i)
                $jamMulai = Carbon::parse($j->jam_mulai)->format('H:i');
                $jadwalMatrix[$j->hari][$jamMulai] = $j;
            }
        }

        return view('dashboard.jadwal.index', [
            'kelaslist' => $kelaslist,
            'kelas_terpilih' => $kelas_terpilih,
            'jamPelajaran' => $this->jamPelajaran, // Kirim list jam ke view
            'jadwalMatrix' => $jadwalMatrix,
            'mapel' => $mapel, // Untuk Modal
            'guru' => $guru    // Untuk Modal
        ]);
    }

    // Fungsi Store & Destroy tetap sama seperti sebelumnya
    // Cuma nanti redirect-nya pastikan bawa parameter kelas_id
    public function store(Request $request)
    {
        // ... (Validasi & Insert Code sama seperti sebelumnya) ...
        // ... Copy dari jawaban sebelumnya ...
        
        // Simpan (Saya tulis ulang intinya)
        $request->validate([
             'kelas_id' => 'required',
             'mapel_id' => 'required',
             'guru_id' => 'required',
             'hari' => 'required',
             'jam_mulai' => 'required',
             'jam_selesai' => 'required|after:jam_mulai',
        ]);

         // VALIDASI BENTROK (SAMA SEPERTI SEBELUMNYA - PASTIKAN ADA)
         // ...

        DB::table('jadwal_pelajaran')->insert([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('jadwal.index', ['kelas_id' => $request->kelas_id])->with('success', 'Jadwal Disimpan');
    }

    public function destroy($id)
    {
        $jadwal = DB::table('jadwal_pelajaran')->where('id', $id)->first();
        DB::table('jadwal_pelajaran')->where('id', $id)->delete();
        return redirect()->route('jadwal.index', ['kelas_id' => $jadwal->kelas_id])->with('success', 'Jadwal Dihapus');
    }
}