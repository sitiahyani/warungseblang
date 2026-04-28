<?php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanPenjualanExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data->map(function ($p) {
            return [
                'Tanggal'     => $p->tanggal,
                'Invoice'     => $p->kode_transaksi,
                'Pelanggan'   => $p->pelangganRel->nama_pelanggan ?? '-',
                'Kategori'    => $p->kategoriRel->nama_kategori ?? '-',
                'Total'       => $p->total,
                'Metode'      => ucfirst($p->metode_bayar),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Invoice',
            'Pelanggan',
            'Kategori',
            'Total',
            'Metode',
        ];
    }
}