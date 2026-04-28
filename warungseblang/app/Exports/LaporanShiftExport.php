<?php

namespace App\Exports;

use App\Models\CashDrawer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanShiftExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return CashDrawer::with(['shift', 'user'])->get();
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Shift',
            'Kasir',
            'Waktu Shift',
            'Cash Awal',
            'Cash Akhir',
            'Selisih'
        ];
    }

    public function map($item): array
    {
        return [
            $item->shift->kode ?? '-',
            $item->shift->nama_shift ?? '-',
            $item->user->nama ?? $item->user->username ?? $item->user->name ?? 'Kasir',
            ($item->shift->waktu_mulai ?? '-') . ' - ' . ($item->shift->waktu_selesai ?? '-'),
            $item->cash_awal ?? 0,
            $item->cash_akhir ?? 0,
            $item->selisih ?? 0,
        ];
    }
}