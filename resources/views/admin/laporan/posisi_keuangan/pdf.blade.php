<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Posisi Keuangan</title>
    <style>
        body { font-family: serif; font-size: 12px; }
        .container { border:1px solid #000; padding:10px; }
        .header-box { border:1px solid #000; padding:6px; margin-bottom:10px; }
        .center { text-align:center; }
        table { width:100%; border-collapse:collapse; }
        td { padding:4px; vertical-align:top; }
        .right { text-align:right; }
        .bold { font-weight:bold; }
        .indent { padding-left:22px; }
        .line { border-top:1px solid #000; }
        .double { border-top:2px solid #000; }
        .catatan { text-align:center; width:12%; }
        .w-ket { width:58%; }
        .w-yr { width:15%; }
    </style>
</head>
<body>

@php
function rupiah($n){
    if ($n < 0) return '(' . number_format(abs($n),0,',','.') . ')';
    return number_format($n,0,',','.');
}
@endphp

<div class="container">

    <div class="header-box">
        <div class="bold">ENTITAS</div>
        <div class="bold">LAPORAN POSISI KEUANGAN</div>
        <div class="bold">31 DESEMBER {{ $tahun }} DAN {{ $tahun_lalu }}</div>
    </div>

    <table>
        <tr>
            <td class="bold w-ket">ASET</td>
            <td class="bold catatan">Catatan</td>
            <td class="bold right w-yr">{{ $tahun }}</td>
            <td class="bold right w-yr">{{ $tahun_lalu }}</td>
        </tr>

        <!-- KAS & SETARA KAS -->
        <tr>
            <td>Kas dan setara kas</td>
            <td></td><td></td><td></td>
        </tr>
        <tr>
            <td class="indent">Kas</td>
            <td class="catatan">3</td>
            <td class="right">{{ rupiah($kas ?? 0) }}</td>
            <td class="right">{{ rupiah($kas_lalu ?? 0) }}</td>
        </tr>
        <tr>
            <td class="indent">Giro</td>
            <td class="catatan">4</td>
            <td class="right">{{ rupiah($giro ?? 0) }}</td>
            <td class="right">{{ rupiah($giro_lalu ?? 0) }}</td>
        </tr>
        <tr>
            <td class="indent">Deposito</td>
            <td class="catatan">5</td>
            <td class="right">{{ rupiah($deposito ?? 0) }}</td>
            <td class="right">{{ rupiah($deposito_lalu ?? 0) }}</td>
        </tr>
        <tr class="bold">
            <td class="indent">Jumlah kas dan setara kas</td>
            <td></td>
            <td class="right">
                {{ rupiah(($kas ?? 0)+($giro ?? 0)+($deposito ?? 0)) }}
            </td>
            <td class="right">
                {{ rupiah(($kas_lalu ?? 0)+($giro_lalu ?? 0)+($deposito_lalu ?? 0)) }}
            </td>
        </tr>

        <!-- PIUTANG -->
        <tr>
            <td>Piutang usaha</td>
            <td class="catatan">6</td>
            <td class="right">{{ rupiah($piutang ?? 0) }}</td>
            <td class="right">{{ rupiah($piutang_lalu ?? 0) }}</td>
        </tr>

        <!-- PERSEDIAAN -->
        <tr>
            <td>Persediaan</td>
            <td></td>
            <td class="right">{{ rupiah($persediaan ?? 0) }}</td>
            <td class="right">{{ rupiah($persediaan_lalu ?? 0) }}</td>
        </tr>

        <!-- BEBAN DIBAYAR DIMUKA -->
        <tr>
            <td>Beban dibayar di muka</td>
            <td class="catatan">7</td>
            <td class="right">{{ rupiah($beban_dimuka ?? 0) }}</td>
            <td class="right">{{ rupiah($beban_dimuka_lalu ?? 0) }}</td>
        </tr>

        <!-- ASET TETAP -->
        <tr>
            <td>Aset tetap</td>
            <td></td>
            <td class="right">{{ rupiah($aset_tetap ?? 0) }}</td>
            <td class="right">{{ rupiah($aset_tetap_lalu ?? 0) }}</td>
        </tr>
        <tr>
            <td class="indent">Akumulasi penyusutan</td>
            <td></td>
            <td class="right">{{ rupiah($akumulasi_penyusutan ?? 0) }}</td>
            <td class="right">{{ rupiah($akumulasi_penyusutan_lalu ?? 0) }}</td>
        </tr>

        <!-- TOTAL ASET -->
        <tr class="line bold">
            <td>JUMLAH ASET</td>
            <td></td>
            <td class="right">
                {{ rupiah(
                    ($kas ?? 0)+($giro ?? 0)+($deposito ?? 0)
                    + ($piutang ?? 0)
                    + ($persediaan ?? 0)
                    + ($beban_dimuka ?? 0)
                    + ($aset_tetap ?? 0)
                    + ($akumulasi_penyusutan ?? 0)
                ) }}
            </td>
            <td class="right">
                {{ rupiah(
                    ($kas_lalu ?? 0)+($giro_lalu ?? 0)+($deposito_lalu ?? 0)
                    + ($piutang_lalu ?? 0)
                    + ($persediaan_lalu ?? 0)
                    + ($beban_dimuka_lalu ?? 0)
                    + ($aset_tetap_lalu ?? 0)
                    + ($akumulasi_penyusutan_lalu ?? 0)
                ) }}
            </td>
        </tr>

        <tr><td colspan="4"><br></td></tr>

        <!-- LIABILITAS -->
        <tr>
            <td class="bold">LIABILITAS</td>
            <td></td><td></td><td></td>
        </tr>
        <tr>
            <td>Utang usaha</td>
            <td></td>
            <td class="right">{{ rupiah($utang_usaha ?? 0) }}</td>
            <td class="right">{{ rupiah($utang_usaha_lalu ?? 0) }}</td>
        </tr>
        <tr>
            <td>Utang bank</td>
            <td class="catatan">8</td>
            <td class="right">{{ rupiah($utang_bank ?? 0) }}</td>
            <td class="right">{{ rupiah($utang_bank_lalu ?? 0) }}</td>
        </tr>
        <tr class="line bold">
            <td>JUMLAH LIABILITAS</td>
            <td></td>
            <td class="right">
                {{ rupiah(($utang_usaha ?? 0)+($utang_bank ?? 0)) }}
            </td>
            <td class="right">
                {{ rupiah(($utang_usaha_lalu ?? 0)+($utang_bank_lalu ?? 0)) }}
            </td>
        </tr>

        <tr><td colspan="4"><br></td></tr>

        <!-- EKUITAS -->
        <tr>
            <td class="bold">EKUITAS</td>
            <td></td><td></td><td></td>
        </tr>
        <tr>
            <td>Modal</td>
            <td></td>
            <td class="right">{{ rupiah($modal ?? 0) }}</td>
            <td class="right">{{ rupiah($modal_lalu ?? 0) }}</td>
        </tr>
        <tr>
            <td>Saldo laba (defisit)</td>
            <td class="catatan">9</td>
            <td class="right">{{ rupiah($laba ?? 0) }}</td>
            <td class="right">{{ rupiah($laba_lalu ?? 0) }}</td>
        </tr>
        <tr class="line bold">
            <td>JUMLAH EKUITAS</td>
            <td></td>
            <td class="right">
                {{ rupiah(($modal ?? 0)+($laba ?? 0)) }}
            </td>
            <td class="right">
                {{ rupiah(($modal_lalu ?? 0)+($laba_lalu ?? 0)) }}
            </td>
        </tr>

        <tr class="double bold">
            <td>JUMLAH LIABILITAS DAN EKUITAS</td>
            <td></td>
            <td class="right">
                {{ rupiah(
                    ($utang_usaha ?? 0)+($utang_bank ?? 0)
                    + ($modal ?? 0)+($laba ?? 0)
                ) }}
            </td>
            <td class="right">
                {{ rupiah(
                    ($utang_usaha_lalu ?? 0)+($utang_bank_lalu ?? 0)
                    + ($modal_lalu ?? 0)+($laba_lalu ?? 0)
                ) }}
            </td>
        </tr>

    </table>
</div>

</body>
</html>