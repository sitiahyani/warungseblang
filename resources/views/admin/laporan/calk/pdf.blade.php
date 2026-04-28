<!DOCTYPE html>
<html>
<head>
    <title>CALK</title>
    <style>
        body {
            font-family: serif;
            font-size: 13px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td, th {
            padding: 6px;
            vertical-align: top;
        }
        .judul {
            border: 1px solid #000;
            background: #ddd;
            font-weight: bold;
            padding: 8px;
        }
        .section-title {
            font-weight: bold;
            padding-top: 10px;
        }
        .nominal {
            text-align: right;
            width: 25%;
        }
    </style>
</head>
<body>

<div class="judul">
    ENTITAS <br>
    CATATAN ATAS LAPORAN KEUANGAN <br>
    {{ date('d F Y') }}
</div>

<table border="0">
    <tr>
        <td width="5%">1.</td>
        <td><b>UMUM</b></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td>Warung Seblang bergerak di bidang restoran dan penjualan makanan minuman.</td>
        <td></td>
    </tr>

    <tr>
        <td>3.</td>
        <td><b>KAS DAN SETARA KAS</b></td>
        <td class="nominal">Rp {{ number_format($kas,0,',','.') }}</td>
    </tr>

    <tr>
        <td>6.</td>
        <td><b>PIUTANG USAHA</b></td>
        <td class="nominal">Rp {{ number_format($piutang,0,',','.') }}</td>
    </tr>

    <tr>
        <td>8.</td>
        <td><b>UTANG BANK</b></td>
        <td class="nominal">Rp {{ number_format($utang,0,',','.') }}</td>
    </tr>

    <tr>
        <td>9.</td>
        <td><b>MODAL DAN SALDO LABA</b></td>
        <td class="nominal">
            Rp {{ number_format($modal + $laba,0,',','.') }}
        </td>
    </tr>

    <tr>
        <td>10.</td>
        <td><b>PENDAPATAN PENJUALAN</b></td>
        <td class="nominal">Rp {{ number_format($pendapatan,0,',','.') }}</td>
    </tr>

    <tr>
        <td>11.</td>
        <td><b>BEBAN</b></td>
        <td class="nominal">Rp {{ number_format($beban,0,',','.') }}</td>
    </tr>
</table>

</body>
</html>