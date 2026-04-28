<?php

namespace App\Services;

use App\Models\JurnalUmum;
use App\Models\DetailJurnal;

class JurnalService
{
    public static function simpan($tanggal, $keterangan, $sumber, $ref_id, $detail)
    {
        $jurnal = JurnalUmum::create([
            'tanggal'    => $tanggal,
            'keterangan' => $keterangan,
            'sumber'     => $sumber,
            'ref_id'     => $ref_id,
        ]);

        foreach ($detail as $row) {

            DetailJurnal::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'id_akun'   => $row['id_akun'],
                'debit'     => $row['debit'] ?? 0,
                'kredit'    => $row['kredit'] ?? 0,
            ]);
        }
    }
}
