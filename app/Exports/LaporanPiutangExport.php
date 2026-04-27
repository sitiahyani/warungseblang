<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class LaporanPiutangExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data)->map(function ($d) {
            return [
                'Tanggal' => $d->tanggal,
                'Invoice' => $d->kode_transaksi,
                'Pelanggan' => $d->pelanggan ?? '-',
                'Total' => $d->total,
                'Terbayar' => $d->terbayar ?? 0,
                'Sisa' => $d->sisa,
                'Status' => $d->status,
            ];
        });
    }
}