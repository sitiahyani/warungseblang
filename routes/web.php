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

        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');
//Karyawan
        Route::resource('karyawan', KaryawanController::class);

        Route::patch('karyawan/{id}/toggle-status',
            [KaryawanController::class, 'toggleStatus']
        )->name('karyawan.toggle');
//kategori
        Route::resource('kategori', KategoriController::class)
            ->except(['destroy']);
//tipe
        Route::resource('tipe', TipeController::class)
            ->only(['index', 'store']);
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
    
});


/*
|--------------------------------------------------------------------------
| KASIR
|--------------------------------------------------------------------------
*/
Route::prefix('kasir')->group(function () {

    //DASHBOARD
        Route::get('/', function () {
            return view('kasir.dashboard');
        })->name('dashboard');

        // CASH DRAWER
        Route::get('/cashdrawer', function () {
            return view('kasir.cashdrawer');
        })->name('cashdrawer');

        // RIWAYAT
        Route::get('/riwayat', function () {
            return view('kasir.riwayat');
        })->name('riwayat');

        // ===== LAYANAN =====

        // RESTO (PAKAI CONTROLLER 🔥)
        Route::get('/penjualan/resto', [RestoController::class, 'index'])
            ->name('penjualan.resto');
        Route::post('/penjualan/simpan',
            [RestoController::class,'simpan']
        )->name('penjualan.simpan');

        Route::get('/homestay', function () {
            return view('kasir.penjualan.homestay');
        })->name('penjualan.homestay');

        Route::get('/wedding', function () {
            return view('kasir.penjualan.wedding'); // ← tadi typo wwedding
        })->name('penjualan.wedding');

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