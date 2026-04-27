<!DOCTYPE html>
<html>
<head>
<style>
    body {
        font-family: serif;
        font-size: 12px;
    }

    .container {
        border: 1px solid black;
        padding: 10px;
        width: 90%;
        margin: auto;
    }

    .header-box {
        border: 1px solid black;
        padding: 8px;
        margin-bottom: 15px;
        background-color: #eaeaea;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    td {
        padding: 4px;
    }

    .right {
        text-align: right;
    }

    .center {
        text-align: center;
    }

    .bold {
        font-weight: bold;
    }

    .italic {
        font-style: italic;
    }

    .line {
        border-top: 1px solid black;
    }

    .double {
        border-top: 2px solid black;
    }

    .spasi {
        height: 10px;
    }
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
        <div class="bold">LAPORAN LABA RUGI</div>
        <div class="bold">UNTUK TAHUN YANG BERAKHIR 31 DESEMBER {{ $tahun }} DAN {{ $tahun_lalu }}</div>
    </div>

    <table>

        <!-- HEADER -->
        <tr>
            <td class="bold">PENDAPATAN</td>
            <td class="center bold">Catatan</td>
            <td class="right bold">{{ $tahun }}</td>
            <td class="right bold">{{ $tahun_lalu }}</td>
        </tr>

        <!-- PENDAPATAN -->
        <tr>
            <td>Pendapatan usaha</td>
            <td class="center">10</td>
            <td class="right">{{ rupiah($pendapatan) }}</td>
            <td class="right">{{ rupiah($pendapatan_lalu) }}</td>
        </tr>

        <tr>
            <td>Pendapatan lain-lain</td>
            <td></td>
            <td class="right">0</td>
            <td class="right">0</td>
        </tr>

        <tr class="line italic bold">
            <td>JUMLAH PENDAPATAN</td>
            <td></td>
            <td class="right">{{ rupiah($pendapatan) }}</td>
            <td class="right">{{ rupiah($pendapatan_lalu) }}</td>
        </tr>

        <tr class="spasi"><td colspan="4"></td></tr>

        <!-- BEBAN -->
        <tr>
            <td class="bold">BEBAN</td>
        </tr>

        <tr>
            <td>Beban usaha</td>
            <td></td>
            <td class="right">{{ rupiah($beban) }}</td>
            <td class="right">{{ rupiah($beban_lalu) }}</td>
        </tr>

        <tr>
            <td>Beban lain-lain</td>
            <td class="center">11</td>
            <td class="right">0</td>
            <td class="right">0</td>
        </tr>

        <tr class="line italic bold">
            <td>JUMLAH BEBAN</td>
            <td></td>
            <td class="right">{{ rupiah($beban) }}</td>
            <td class="right">{{ rupiah($beban_lalu) }}</td>
        </tr>

        <tr class="spasi"><td colspan="4"></td></tr>

        <!-- LABA -->
        <tr>
            <td class="bold">LABA (RUGI) SEBELUM PAJAK PENGHASILAN</td>
            <td></td>
            <td class="right">{{ rupiah($laba_sebelum_pajak) }}</td>
            <td class="right">{{ rupiah($laba_sebelum_pajak_lalu) }}</td>
        </tr>

        <tr>
            <td>Beban pajak penghasilan</td>
            <td class="center">12</td>
            <td class="right">{{ rupiah($pajak) }}</td>
            <td class="right">{{ rupiah($pajak_lalu) }}</td>
        </tr>

        <tr class="double italic bold">
            <td>LABA (RUGI) SETELAH PAJAK PENGHASILAN</td>
            <td></td>
            <td class="right">{{ rupiah($laba_bersih) }}</td>
            <td class="right">{{ rupiah($laba_bersih_lalu) }}</td>
        </tr>

    </table>

</div>

</body>
</html>