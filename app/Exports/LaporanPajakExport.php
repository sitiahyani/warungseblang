<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class LaporanPajakExport implements FromArray
{
    protected $data;
    protected $persen;

    public function __construct($data, $persen)
    {
        $this->data = $data;
        $this->persen = $persen;
    }

    public function array(): array
    {
        $result[] = [
            'Tanggal',
            'Invoice',
            'Total',
            'Pajak ('.$this->persen.'%)'
        ];

        foreach ($this->data as $d) {
            $result[] = [
                $d->tanggal,
                $d->kode_transaksi,
                $d->total,
                $d->nilai_pajak
            ];
        }

        $result[] = ['', '', 'TOTAL PAJAK', $this->data->sum('nilai_pajak')];

        return $result;
    }
}