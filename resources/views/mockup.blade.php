@extends('adminlte::page')

@section('title','Sistem Keuangan')

@section('content_header')
<h1 class="mb-0">Sistem Keuangan</h1>
<small class="text-muted">Mockup UI • Kelola & Laporan</small>
@stop

@section('content')

<style>
    .toolbar {
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:15px;
    }
    .list-group-item span:last-child {
        font-weight:600;
    }
</style>

{{-- ================================================= --}}
{{-- DASHBOARD --}}
{{-- ================================================= --}}
<div class="row mb-4">
    <div class="col-md-3">
        <x-adminlte-small-box title="Rp 12.000.000" text="Saldo Kas" icon="fas fa-wallet" theme="info"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="Rp 25.000.000" text="Pendapatan" icon="fas fa-chart-line" theme="success"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="Rp 15.000.000" text="Biaya" icon="fas fa-receipt" theme="warning"/>
    </div>
    <div class="col-md-3">
        <x-adminlte-small-box title="Rp 10.000.000" text="Laba Bersih" icon="fas fa-coins" theme="danger"/>
    </div>
</div>

{{-- ================================================= --}}
{{-- KELOLA DATA --}}
{{-- ================================================= --}}
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title">Kelola Data</h3>
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#barang">Barang</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#layanan">Layanan</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#stok">Stok & HPP</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#biaya">Biaya</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#akun">Akun (COA)</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#modal">Modal</a></li>
        </ul>

        <div class="tab-content">

            {{-- DATA BARANG --}}
           <button class="btn btn-success btn-sm mb-2" data-toggle="modal" data-target="#modalBarang">
    + Tambah Barang
</button>

<table class="table table-hover">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Harga Jual</th>
            <th>Stok</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>BRG01</td>
            <td>Nasi Bebek</td>
            <td>Makanan</td>
            <td>15.000</td>
            <td>12</td>
        </tr>
    </tbody>
</table>
<div class="modal fade" id="modalBarang">
<div class="modal-dialog modal-lg">
<div class="modal-content">

<div class="modal-header">
    <h5 class="modal-title">Tambah Barang</h5>
    <button class="close" data-dismiss="modal">&times;</button>
</div>

<div class="modal-body">
<div class="row">

    {{-- KIRI --}}
    <div class="col-md-6">
        <div class="form-group">
            <label>Kode Barang</label>
            <input class="form-control" placeholder="BRG01">
        </div>

        <div class="form-group">
            <label>Nama Barang</label>
            <input class="form-control" placeholder="Nasi Bebek">
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select class="form-control">
                <option>Makanan</option>
                <option>Minuman</option>
                <option>Perlengkapan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Satuan</label>
            <input class="form-control" placeholder="Porsi / Unit">
        </div>

        <div class="form-group">
            <label>Stok Awal</label>
            <input type="number" class="form-control" placeholder="0">
        </div>
    </div>

    {{-- KANAN --}}
    <div class="col-md-6">
         <div class="form-group">
            <label>Tipe</label>
            <input type="number" class="form-control" placeholder="kemasan / ongkir (opsional)">
        </div>
        <div class="form-group">
            <label>Harga Beli</label>
            <input type="number" class="form-control" placeholder="contoh: 10.000">
        </div>

        {{-- HPP INFO (BUKAN LINK, BUKAN INPUT) --}}
        <div class="alert alert-info py-2">
            <strong>HPP / Harga Pokok:</strong>
            <span class="float-right">Rp 10.500</span>
            <br>
            <small class="text-muted">
                HPP dihitung dari harga beli + biaya tambahan
            </small>
        </div>

        <div class="form-group">
            <label>Harga Jual</label>
            <input type="number" class="form-control" placeholder="contoh: 15.000">
        </div>

        <div class="form-group">
            <label>Foto Barang</label>
            <input type="file" class="form-control-file">
        </div>
    </div>

</div>
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button class="btn btn-success">Simpan</button>
</div>

</div>
</div>
</div>


            {{-- DATA LAYANAN --}}
            <div class="tab-pane fade" id="layanan">
                <button class="btn btn-primary btn-sm mb-2">+ Tambah Layanan</button>
                <table class="table table-hover">
                    <tr><th>Kode</th><th>Nama</th><th>Harga</th></tr>
                    <tr><td>LY01</td><td>Paket Wedding</td><td>5.000.000</td></tr>
                </table>
            </div>

            {{-- STOK --}}
            <div class="tab-pane fade" id="stok">
                <form class="row mb-3">
                    <div class="col-md-3"><input type="date" class="form-control"></div>
                    <div class="col-md-3"><input class="form-control" placeholder="Harga Pokok"></div>
                    <div class="col-md-3"><input class="form-control" placeholder="Jumlah"></div>
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>Tambah</option>
                            <option>Kurang</option>
                        </select>
                    </div>
                </form>
                <small class="text-muted">Perubahan stok mempengaruhi HPP.</small>
            </div>

            {{-- BIAYA --}}
            <div class="tab-pane fade" id="biaya">
                <button class="btn btn-danger btn-sm mb-2">+ Tambah Biaya</button>
                <table class="table table-hover">
                    <tr><th>Tanggal</th><th>Nama Biaya</th><th>Jumlah</th></tr>
                    <tr><td>01-01-2026</td><td>Listrik</td><td>500.000</td></tr>
                </table>
            </div>

            {{-- AKUN --}}
            <div class="tab-pane fade" id="akun">
                <button class="btn btn-success btn-sm mb-2">+ Tambah Akun</button>
                <table class="table table-hover">
                    <tr><th>Kode</th><th>Nama Akun</th><th>Kelompok</th></tr>
                    <tr><td>101</td><td>Kas</td><td>Aset</td></tr>
                    <tr><td>401</td><td>Pendapatan</td><td>Pendapatan</td></tr>
                </table>
            </div>

            {{-- MODAL --}}
            <div class="tab-pane fade" id="modal">

    {{-- RINGKASAN MODAL --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="info-box bg-light">
                <span class="info-box-icon bg-success"><i class="fas fa-arrow-down"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Setoran Modal</span>
                    <span class="info-box-number">Rp 20.000.000</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-light">
                <span class="info-box-icon bg-danger"><i class="fas fa-arrow-up"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Penarikan Modal</span>
                    <span class="info-box-number">Rp 6.000.000</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box bg-light">
                <span class="info-box-icon bg-info"><i class="fas fa-wallet"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Saldo Modal</span>
                    <span class="info-box-number">Rp 14.000.000</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TOOLBAR --}}
    <div class="d-flex justify-content-between mb-2">
        <small class="text-muted">Modal dikelola oleh admin</small>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTransaksiModal">
            + Transaksi Modal
        </button>
    </div>

    {{-- DAFTAR TRANSAKSI MODAL --}}
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jenis</th>
                <th>Keterangan</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>01-01-2026</td>
                <td><span class="badge badge-success">Setoran</span></td>
                <td>Modal Awal Pemilik</td>
                <td class="text-right">10.000.000</td>
            </tr>
            <tr>
                <td>15-01-2026</td>
                <td><span class="badge badge-success">Setoran</span></td>
                <td>Penambahan Modal</td>
                <td class="text-right">10.000.000</td>
            </tr>
            <tr>
                <td>20-01-2026</td>
                <td><span class="badge badge-danger">Penarikan</span></td>
                <td>Prive Pemilik</td>
                <td class="text-right">(6.000.000)</td>
            </tr>
        </tbody>
    </table>

</div>
<div class="modal fade" id="modalTransaksiModal">
<div class="modal-dialog">
<div class="modal-content">

    <div class="modal-header">
        <h5 class="modal-title">Transaksi Modal Pemilik</h5>
        <button class="close" data-dismiss="modal">&times;</button>
    </div>

    <div class="modal-body">
        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" class="form-control">
        </div>

        <div class="form-group">
            <label>Jenis Transaksi</label>
            <select class="form-control">
                <option>Setoran Modal</option>
                <option>Penarikan Modal (Prive)</option>
            </select>
        </div>

        <div class="form-group">
            <label>Jumlah</label>
            <input class="form-control" placeholder="Masukkan jumlah">
        </div>

        <div class="form-group">
            <label>Keterangan</label>
            <textarea class="form-control" rows="2" placeholder="Contoh: Modal awal usaha"></textarea>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
        <button class="btn btn-primary">Simpan</button>
    </div>

</div>
</div>
</div>


        </div>
    </div>
</div>

{{-- ================================================= --}}
{{-- LAPORAN --}}
{{-- ================================================= --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Keuangan</h3>
    </div>

    

        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#posisi">Posisi Keuangan</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#labarugi">Laba Rugi</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#aruskas">Arus Kas</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#bukubesar">Buku Besar</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#jurnal">Jurnal Umum</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#hutang">Hutang</a></li>
            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#piutang">Piutang</a></li>
        </ul>
<div class="card-body">
        <div class="toolbar">
            <input type="month" class="form-control w-25">
            <div>
                <button class="btn btn-outline-secondary btn-sm">Import</button>
                <button class="btn btn-success btn-sm">Cetak</button>
            </div>
        </div>
        <div class="tab-content">

            {{-- POSISI KEUANGAN --}}
          <div class="tab-pane fade show active" id="posisi">
    <small class="text-muted">Per 31 Desember 2025</small>

    <div class="row mt-3">
        {{-- ASET --}}
        <div class="col-md-6">
            <h6 class="font-weight-bold">ASET</h6>

            <ul class="list-group mb-3">
                <li class="list-group-item font-weight-bold">Aset Lancar</li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Kas</span><span>12.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Piutang Usaha</span><span>2.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Persediaan</span><span>5.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
                    <span>Total Aset Lancar</span><span>19.000.000</span>
                </li>

                <li class="list-group-item font-weight-bold mt-2">Aset Tidak Lancar</li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Peralatan</span><span>3.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
                    <span>Total Aset Tidak Lancar</span><span>3.000.000</span>
                </li>

                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-secondary text-white">
                    <span>TOTAL ASET</span><span>22.000.000</span>
                </li>
            </ul>
        </div>

        {{-- KEWAJIBAN & EKUITAS --}}
        <div class="col-md-6">
            <h6 class="font-weight-bold">KEWAJIBAN & EKUITAS</h6>

            <ul class="list-group">
                <li class="list-group-item font-weight-bold">Kewajiban</li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Hutang Usaha</span><span>3.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
                    <span>Total Kewajiban</span><span>3.000.000</span>
                </li>

                <li class="list-group-item font-weight-bold mt-2">Ekuitas</li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Modal Pemilik</span><span>14.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Saldo Laba</span><span>5.000.000</span>
                </li>
                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
                    <span>Total Ekuitas</span><span>19.000.000</span>
                </li>

                <li class="list-group-item d-flex justify-content-between font-weight-bold bg-secondary text-white">
                    <span>TOTAL KEWAJIBAN & EKUITAS</span><span>22.000.000</span>
                </li>
            </ul>
        </div>
    </div>
</div>


            {{-- LABA RUGI --}}
            <div class="tab-pane fade" id="labarugi">
    <small class="text-muted">Periode Januari – Desember 2025</small>

    <ul class="list-group mt-3">
        <li class="list-group-item font-weight-bold">Pendapatan</li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Pendapatan Usaha</span><span>25.000.000</span>
        </li>
        <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
            <span>Total Pendapatan</span><span>25.000.000</span>
        </li>

        <li class="list-group-item font-weight-bold mt-2">Harga Pokok Penjualan</li>
        <li class="list-group-item d-flex justify-content-between">
            <span>HPP</span><span>(10.000.000)</span>
        </li>

        <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
            <span>Laba Kotor</span><span>15.000.000</span>
        </li>

        <li class="list-group-item font-weight-bold mt-2">Beban Usaha</li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Beban Operasional</span><span>(5.000.000)</span>
        </li>

        <li class="list-group-item d-flex justify-content-between font-weight-bold bg-secondary text-white">
            <span>LABA BERSIH</span><span>10.000.000</span>
        </li>
    </ul>
</div>

            {{-- ARUS KAS --}}
            <div class="tab-pane fade" id="aruskas">
    <small class="text-muted">Periode Januari – Desember 2025</small>

    <ul class="list-group mt-3">
        <li class="list-group-item font-weight-bold">Arus Kas dari Aktivitas Operasional</li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Penerimaan dari Pelanggan</span><span>25.000.000</span>
        </li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Pembayaran Beban</span><span>(15.000.000)</span>
        </li>
        <li class="list-group-item d-flex justify-content-between font-weight-bold bg-light">
            <span>Kas Bersih Operasional</span><span>10.000.000</span>
        </li>

        <li class="list-group-item font-weight-bold mt-2">Arus Kas dari Aktivitas Investasi</li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Pembelian Peralatan</span><span>(3.000.000)</span>
        </li>

        <li class="list-group-item font-weight-bold mt-2">Arus Kas dari Aktivitas Pendanaan</li>
        <li class="list-group-item d-flex justify-content-between">
            <span>Setoran Modal Pemilik</span><span>10.000.000</span>
        </li>

        <li class="list-group-item d-flex justify-content-between font-weight-bold bg-secondary text-white">
            <span>KENAIKAN KAS BERSIH</span><span>17.000.000</span>
        </li>
    </ul>
</div>

            {{-- BUKU BESAR --}}
            <div class="tab-pane fade" id="bukubesar">
                <table class="table table-bordered">
                    <tr><th>Tanggal</th><th>Keterangan</th><th>Debit</th><th>Kredit</th><th>Saldo</th></tr>
                    <tr><td>01-01-26</td><td>Setoran Modal</td><td>10.000.000</td><td>-</td><td>10.000.000</td></tr>
                </table>
            </div>

            {{-- JURNAL --}}
            <div class="tab-pane fade" id="jurnal">
                <table class="table table-bordered">
                    <tr><th>Tanggal</th><th>Akun</th><th>Debit</th><th>Kredit</th></tr>
                    <tr><td>01-01-26</td><td>Kas</td><td>10.000.000</td><td>-</td></tr>
                    <tr><td></td><td>Modal</td><td>-</td><td>10.000.000</td></tr>
                </table>
            </div>

            {{-- HUTANG --}}
            <div class="tab-pane fade" id="hutang">
                <table class="table table-bordered">
                    <tr><th>Supplier</th><th>Jumlah</th><th>Status</th></tr>
                    <tr><td>Supplier Ayam</td><td>3.000.000</td><td>Belum Lunas</td></tr>
                </table>
            </div>

            {{-- PIUTANG --}}
            <div class="tab-pane fade" id="piutang">
                <table class="table table-bordered">
                    <tr><th>Pelanggan</th><th>Jumlah</th><th>Status</th></tr>
                    <tr><td>Pelanggan Catering</td><td>2.000.000</td><td>Belum Lunas</td></tr>
                </table>
            </div>

        </div>
    </div>
</div>

{{-- MODAL TAMBAH BARANG --}}
<div class="modal fade" id="modalBarang">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header"><h5>Tambah Barang</h5></div>
<div class="modal-body">
    <input class="form-control mb-2" placeholder="Kode Barang">
    <input class="form-control mb-2" placeholder="Nama Barang">
    <input class="form-control mb-2" placeholder="Harga Jual">
    <input class="form-control mb-2" placeholder="Stok">
</div>
<div class="modal-footer">
    <button class="btn btn-secondary" data-dismiss="modal">Batal</button>
    <button class="btn btn-success">Simpan</button>
</div>
</div>
</div>
</div>

@stop
