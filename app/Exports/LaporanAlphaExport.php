<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanAlphaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function collection()
{
    $query = DB::table('presensi_detail')
        ->join('jurnals', 'presensi_detail.jurnal_id', '=', 'jurnals.id')
        ->join('siswa', 'presensi_detail.siswa_id', '=', 'siswa.id')
        ->join('kelas', 'siswa.kelas_id', '=', 'kelas.id')
        ->join('jadwal_pelajaran', 'jurnals.jadwal_id', '=', 'jadwal_pelajaran.id')
        ->join('mata_pelajaran', 'jadwal_pelajaran.mapel_id', '=', 'mata_pelajaran.id')
        // GURU ada di tabel jadwal_pelajaran, bukan di jurnals
        ->join('users', 'jadwal_pelajaran.guru_id', '=', 'users.id')
        ->join('jam_pelajaran_config', 'jadwal_pelajaran.jam_pelajaran_config_id', '=', 'jam_pelajaran_config.id') 
        ->where('presensi_detail.status', 'Alpha');

    // Filter dari Request
    if (!empty($this->params['kelas_id'])) {
        $query->where('siswa.kelas_id', $this->params['kelas_id']);
    }
    if (!empty($this->params['tgl_mulai'])) {
        $query->where('jurnals.tanggal', '>=', $this->params['tgl_mulai']);
    }
    if (!empty($this->params['tgl_selesai'])) {
        $query->where('jurnals.tanggal', '<=', $this->params['tgl_selesai']);
    }

    return $query->select(
            'jurnals.tanggal',
            'siswa.nama_siswa',
            'kelas.nama_kelas',
            'mata_pelajaran.nama_mapel',
            'users.name as nama_guru',
            // Gunakan MIN dan MAX untuk menangani mapel berdampingan
            DB::raw('MIN(jam_pelajaran_config.jam_mulai) as jam_mulai_gabung'),
            DB::raw('MAX(jam_pelajaran_config.jam_selesai) as jam_selesai_gabung')
        )
        // GroupBy agar mapel yang sama di hari yang sama jadi 1 baris
        ->groupBy(
            'jurnals.tanggal', 
            'siswa.id', 
            'mata_pelajaran.id', 
            'users.id', 
            'kelas.id'
        )
        ->orderBy('jurnals.tanggal', 'desc')
        ->get();
}

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Siswa',
            'Kelas',
            'Mata Pelajaran',
            'Waktu Pelajaran',
            'Guru Pengajar'
        ];
    }

    public function map($row): array
    {
        return [
            \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y'),
            $row->nama_siswa,
            $row->nama_kelas,
            $row->nama_mapel,
            substr($row->jam_mulai_gabung, 0, 5) . ' - ' . substr($row->jam_selesai_gabung, 0, 5),
            $row->nama_guru,
        ];
    }
}
