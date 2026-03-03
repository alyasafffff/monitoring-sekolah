<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaPresensiExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        // $data ini nanti isinya: dataSiswa, listTanggal, tipe, infoKelas, dll.
        $this->data = $data;
    }

    public function view(): View
    {
        return view('dashboard.rekap.excel', $this->data);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Membuat baris header (1-3) menjadi tebal (Bold)
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
        ];
    }
}