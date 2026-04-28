<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KodeAkun;

class KodeAkunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $data = [
        ['1101','Kas','Aset'],
        ['1102','Piutang Usaha','Aset'],
        ['2101','Hutang Usaha','Kewajiban'],
        ['3101','Modal Pemilik','Modal'],
        ['3102','Prive','Modal'],
        ['4101','Pendapatan Barang','Pendapatan'],
        ['4102','Pendapatan Jasa','Pendapatan'],
        ['5101','Beban Pembelian','Beban'],
        ['5102','Beban Listrik','Beban'],
        ['5103','Beban Gaji','Beban'],
    ];

    foreach($data as $akun){
        KodeAkun::create([
            'kode' => $akun[0],
            'nama_akun' => $akun[1],
            'kategori' => $akun[2]
        ]);
    }
    }
}