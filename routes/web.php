<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Kasir\PembelianController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\KaryawanController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\TipeController;
use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\ModalController;
use App\Http\Controllers\Admin\BahanBakuController;
use App\Http\Controllers\Admin\JurnalController;
use App\Http\Controllers\Admin\layananController;
use App\Http\Controllers\Admin\BukuBesarController;
use App\Http\Controllers\Admin\DiskonController;
use App\Http\Controllers\Admin\PajakController;
use App\Http\Controllers\Kasir\RestoController;
use App\Http\Controllers\Admin\ShiftController;
use App\Http\Controllers\Admin\StokBarangController;
use App\Http\Controllers\Admin\KodeAkunController;
use App\Http\Controllers\Admin\LaporanPembelianController;
use App\Http\Controllers\Admin\LaporanHutangController;
use App\Http\Controllers\Admin\BiayaPengeluaranController;
use App\Http\Controllers\Kasir\PembayaranHutangController;
use App\Http\Controllers\Admin\PelangganController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\StokOpnameController;
use App\Http\Controllers\Kasir\WeddingController;
use App\Http\Controllers\Kasir\HomestayController;
use App\Http\Controllers\Kasir\RiwayatController;
use App\Http\Controllers\Kasir\HutangPelangganController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\LaporanPajakController;
use App\Http\Controllers\Admin\LaporanPenjualanController;
use App\Http\Controllers\Admin\LaporanShiftController;
use App\Http\Controllers\Admin\LaporanPiutangController;
use App\Http\Controllers\Kasir\CashDrawerController;
/*
|--------------------------------------------------------------------------
| HALAMAN AWAL → LOGIN
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','role:admin'])
    ->prefix('admin')
    ->group(function () {

// 🔥 AKSES /admin
    Route::get('/', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
//Karyawan
        Route::resource('karyawan', KaryawanController::class);

        Route::patch('karyawan/{id}/toggle-status',
            [KaryawanController::class, 'toggleStatus']
        )->name('karyawan.toggle');
//kategori
        Route::resource('kategori', KategoriController::class);
//tipe
        Route::resource('tipe', TipeController::class);
//barang
        Route::resource('barang', BarangController::class)
            ->except(['show']);

        Route::patch('/barang/{id}/status',
        [BarangController::class,'updateStatus'])
        ->name('barang.status');
        Route::get('/barang/{id}/hpp', [BarangController::class, 'hpp'])->name('barang.hpp');
        Route::post('/barang/{id}/hpp', [BarangController::class, 'simpanHpp'])->name('barang.simpanHpp');
        Route::get('/barang/{id}/resep', [BarangController::class, 'resep'])
            ->name('barang.resep');
        Route::post('/barang/{id}/resep', [BarangController::class, 'simpanResep'])
            ->name('barang.simpanResep');
        //stok barang
        Route::get('/stok-barang', [StokBarangController::class,'index'])
            ->name('stok.index');
        Route::put('/stok-barang/{id}', [StokBarangController::class,'update'])
            ->name('stok.update');
    //layanan
    Route::resource('layanan', LayananController::class);
    Route::patch('/layanan/{id}/toggle-status',
        [LayananController::class,'toggleStatus']
    )->name('layanan.toggle-status');

    //supplier
    Route::get('/supplier', [SupplierController::class,'index'])
        ->name('supplier.index');

    Route::post('/supplier/store', [SupplierController::class,'store'])
        ->name('supplier.store');
    Route::put('/supplier/update/{id}', [SupplierController::class,'update'])
    ->name('supplier.update');

    Route::delete('/supplier/delete/{id}', [SupplierController::class,'destroy'])
        ->name('supplier.delete');


    //bahan baku
    Route::get('/bahan', 
        [BahanBakuController::class,'index']
    )->name('bahan.index');

    Route::post('/bahan/store', 
        [BahanBakuController::class,'store']
    )->name('bahan.store');

    Route::put('/bahan/update/{id}', 
        [BahanBakuController::class,'update']
    )->name('bahan.update');

    Route::delete('/bahan/delete/{id}', 
        [BahanBakuController::class,'destroy']
    )->name('bahan.delete');

    //stok opname
    Route::get('/stok-opname', [\App\Http\Controllers\Admin\StokOpnameController::class, 'index'])
        ->name('stok-opname.index');

    Route::post('/stok-opname/simpan', [\App\Http\Controllers\Admin\StokOpnameController::class, 'simpan'])
        ->name('stok-opname.simpan');

    Route::post('/stok-opname/sesuaikan', [\App\Http\Controllers\Admin\StokOpnameController::class, 'sesuaikan'])
        ->name('stok-opname.sesuaikan');
    
    //kode akun
    Route::get('kode-akun', [KodeAkunController::class, 'index'])
        ->name('kode-akun.index');

    Route::post('kode-akun', [KodeAkunController::class, 'store'])
        ->name('kode-akun.store');

    Route::put('kode-akun/{id}', [KodeAkunController::class, 'update'])
        ->name('kode-akun.update');

    Route::delete('kode-akun/{id}', [KodeAkunController::class, 'destroy'])
        ->name('kode-akun.destroy');
        
    //biaya pengeluaran
    // INDEX + SEARCH
        Route::get('/biaya-pengeluaran',
            [BiayaPengeluaranController::class,'index'])
            ->name('biaya.index');

        // SIMPAN
        Route::post('/biaya-pengeluaran',
            [BiayaPengeluaranController::class,'store'])
            ->name('biaya.store');

        // UPDATE
        Route::put('/biaya-pengeluaran/{id}',
            [BiayaPengeluaranController::class,'update'])
            ->name('biaya.update');

        // DELETE
        Route::delete('/biaya-pengeluaran/{id}',
            [BiayaPengeluaranController::class,'destroy'])
            ->name('biaya.delete');

    //modal
    Route::resource('modal', ModalController::class)
        ->except(['create','show','edit','destroy']);

    //jurnal
    Route::get('/jurnal', [JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('/jurnal/excel', [JurnalController::class, 'exportExcel'])->name('jurnal.excel');
    Route::get('/jurnal/pdf', [JurnalController::class, 'exportPdf'])->name('jurnal.pdf');

    //bukubesar
    Route::get('/buku-besar',
    [App\Http\Controllers\Admin\BukuBesarController::class,'index'])
    ->name('bukubesar.index');
    Route::get('/admin/buku-besar/excel',
    [App\Http\Controllers\Admin\BukuBesarController::class,'exportExcel'])
    ->name('bukubesar.excel');
    Route::get('/admin/buku-besar/pdf',
    [App\Http\Controllers\Admin\BukuBesarController::class,'exportPdf'])
    ->name('bukubesar.pdf');

    //laporan pembelian
       Route::get('/laporan-pembelian',
        [LaporanPembelianController::class,'index']
    )->name('laporan.pembelian');

    Route::get('/laporan-pembelian/excel',
        [LaporanPembelianController::class,'exportExcel']
    )->name('laporan.pembelian.excel');

    Route::get('/laporan-pembelian/pdf',
        [LaporanPembelianController::class,'exportPdf']
    )->name('laporan.pembelian.pdf');
    
    //hutang
      Route::get('/laporan-hutang',
        [LaporanHutangController::class,'index']
    )->name('laporan.hutang');

    Route::get('/laporan-hutang/pdf',
        [LaporanHutangController::class,'exportPdf']
    )->name('laporan.hutang.pdf');

    Route::get('/laporan-hutang/excel',
        [LaporanHutangController::class,'exportExcel']
    )->name('laporan.hutang.excel');
    //pelanggan
      Route::post('/pelanggan/simpan', [PelangganController::class, 'simpan'])
        ->name('pelanggan.simpan');
    // diskon
    Route::resource('diskon', DiskonController::class);

    Route::patch(
        'diskon/{id}/toggle-status',
        [DiskonController::class,'toggleStatus']
    )->name('diskon.toggle-status');
    // pajak
    Route::resource('pajak', PajakController::class);
    Route::patch(
        'pajak/{id}/toggle-status',
        [PajakController::class,'toggleStatus']
    )->name('pajak.toggle-status');
    //shift
    Route::resource('shift', ShiftController::class);
    Route::patch(
        'shift/{id}/toggle-status',
        [ShiftController::class,'toggleStatus']
    )->name('shift.toggle-status');
    //laba rugi
    Route::get('/laba-rugi',
    [LaporanController::class,'labaRugi'])
    ->name('admin.laba_rugi');
    Route::get('/laporan/laba-rugi/pdf',
    [LaporanController::class,'labaRugiPdf'])
    ->name('admin.laba_rugi.pdf');
    //posisi keuangan// POSISI KEUANGAN
    Route::get('/posisi-keuangan',
        [LaporanController::class,'posisiKeuangan']
    )->name('admin.posisi_keuangan');
    Route::get('/posisi-keuangan/pdf',
        [LaporanController::class,'posisiKeuanganPdf']
    )->name('admin.posisi_keuangan.pdf');
    //CALK
    Route::get('/calk', [LaporanController::class, 'calk'])
    ->name('calk');

    Route::get('/calk/pdf', [LaporanController::class, 'calkPdf'])
    ->name('calk.pdf');

    //laporan transaksi
    //laporan penjualan
    Route::get('/laporan-penjualan', [LaporanPenjualanController::class, 'index'])
        ->name('laporan.penjualan');
    Route::get('/laporan-penjualan/pdf', [LaporanPenjualanController::class, 'exportPdf'])
        ->name('laporan.penjualan.pdf');
    Route::get('/laporan-penjualan/excel', [LaporanPenjualanController::class, 'exportExcel'])
        ->name('laporan.penjualan.excel');

    //laporan pajak
    Route::get('/laporan-pajak', [LaporanPajakController::class, 'index'])
        ->name('laporan.pajak');
    Route::get('/laporan-pajak/pdf', [LaporanPajakController::class, 'exportPdf'])
        ->name('laporan.pajak.pdf');
    Route::get('/laporan-pajak/excel', [LaporanPajakController::class, 'exportExcel'])
        ->name('laporan.pajak.excel');

    //laporan shift
    Route::get('/laporan-shift', [LaporanShiftController::class, 'index'])
    ->name('laporan.shift');
    Route::get('/laporan-shift/pdf', [LaporanShiftController::class, 'exportPdf'])
    ->name('laporan.shift.pdf');
    Route::get('/laporan-shift/excel', [LaporanShiftController::class, 'exportExcel'])
    ->name('laporan.shift.excel');
    
    //laporan piutang
    Route::get('/laporan-piutang', [LaporanPiutangController::class, 'index'])
        ->name('laporan.piutang');
    Route::get('/laporan-piutang/pdf', [LaporanPiutangController::class, 'exportPdf'])
        ->name('laporan.piutang.pdf');
    Route::get('/laporan-piutang/excel', [LaporanPiutangController::class, 'exportExcel'])
        ->name('laporan.piutang.excel');
    
});


/*
|--------------------------------------------------------------------------
| KASIR
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('kasir')->group(function () {
    //DASHBOARD
        Route::get('/', function () {
            return view('kasir.dashboard');
        })->name('dashboard');

       // CASH DRAWER
    Route::get('/cashdrawer', [CashDrawerController::class, 'index'])
        ->name('cashdrawer');
    Route::post('/cashdrawer/simpan', [CashDrawerController::class, 'store'])
        ->name('cashdrawer.simpan');
    Route::post('/cashdrawer/buka-shift', [CashDrawerController::class, 'bukaShift'])
        ->name('cashdrawer.bukaShift');
    Route::post('/cashdrawer/tutup-shift', [CashDrawerController::class, 'tutupShift'])
        ->name('cashdrawer.tutupShift');

//RIWAYAT
    Route::get('/kasir/riwayat-transaksi', [RiwayatController::class, 'index'])
        ->name('kasir.riwayat');

        // ===== LAYANAN =====

        // RESTO (PAKAI CONTROLLER 🔥)
       // RESTO
    Route::get('/penjualan/resto', [RestoController::class, 'index'])->name('penjualan.resto');
    Route::post('/penjualan/simpan', [RestoController::class, 'simpan'])->name('penjualan.simpan');
    Route::post('/pelanggan/simpan', [PelangganController::class, 'simpan'])
        ->name('pelanggan.simpan');
   Route::get('/pelanggan/cari', [PelangganController::class, 'cari'])
    ->name('kasir.pelanggan.cari');

       // ===== LAYANAN =====
    // RESTO
    Route::get('/penjualan/resto', [RestoController::class, 'index'])->name('penjualan.resto');
    Route::post('/penjualan/simpan', [RestoController::class, 'simpan'])->name('penjualan.simpan');
    Route::post('/pelanggan/simpan', [PelangganController::class, 'simpan'])
        ->name('pelanggan.simpan');

    //HOMESTAY
    Route::get('/homestay', [HomestayController::class, 'index'])
        ->name('penjualan.homestay');
    Route::post('/homestay/simpan', [HomestayController::class, 'simpan'])
        ->name('kasir.homestay.simpan');  

    // WEDDING
    Route::get('/kasir/penjualan/wedding', [WeddingController::class, 'index'])
        ->name('penjualan.wedding');
    Route::post('/kasir/penjualan/wedding/simpan', [WeddingController::class, 'store'])
        ->name('penjualan.wedding.simpan');

//pembayaran hutang pelanggan
    Route::get('/hutang-pelanggan', [HutangPelangganController::class, 'index'])
        ->name('hutang_pelanggan');
    Route::post('/hutang-pelanggan/bayar', [HutangPelangganController::class, 'bayar'])
        ->name('hutang_pelanggan.bayar');

    //pembelian
  Route::get('/pembelian',
        [PembelianController::class,'index']
    )->name('kasir.pembelian');

    Route::post('/pembelian/store',
        [PembelianController::class,'store']
    )->name('kasir.pembelian.store');
    //pembayaran hutang
     Route::get('/hutang',
            [PembayaranHutangController::class,'index']
        )->name('kasir.hutang');

        Route::post('/hutang/bayar',
            [PembayaranHutangController::class,'bayar']
        )->name('kasir.hutang.bayar');
});

/*
|--------------------------------------------------------------------------
| PELAYAN
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pelayan'])
    ->prefix('pelayan')
    ->group(function () {

        Route::get('/', function () {
            return view('pelayan.dashboard');
        })->name('pelayan.dashboard');
});


/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});