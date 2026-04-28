<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Admin Dashboard') - Warung Seblang</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<!-- Tailwind SAFE MODE -->
<script>
tailwind.config = {
  corePlugins: {
    preflight: false,
  }
}
</script>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body{
    font-family:'Inter',sans-serif;
}

/* ===== NAVBAR CLEAN ===== */
.main-header{
    background:#ffffff !important;
    border-bottom:1px solid #e5e7eb !important;
    box-shadow:none !important;
    padding:0.5rem 1rem;
}

.navbar .nav-link{
    color:#334155 !important;
    font-weight:500;
}

.navbar .nav-link:hover{
    color:#0f172a !important;
}

/* ===== SIDEBAR MODERN ===== */
.main-sidebar{
    background:#ffffff;
    border-right:1px solid #e5e7eb;
    box-shadow:none;
}

.custom-brand{
    background:#ffffff;
    border-bottom:1px solid #e5e7eb;
    padding:20px 10px 18px;
}

.brand-title{
    font-size:1.05rem;
    font-weight:700;
    letter-spacing:.5px;
    color:#0f172a;
}

.brand-subtitle{
    font-size:.7rem;
    color:#64748b;
    letter-spacing:1px;
    margin-top:4px;
}

.nav-sidebar .nav-link{
    border-radius:12px;
    margin:4px 12px;
    font-size:.9rem;
    font-weight:500;
    color:#475569;
    transition:all .2s ease;
}

.nav-sidebar .nav-link:hover{
    background:#f1f5f9;
    color:#0f172a;
}

.nav-sidebar .nav-link.active{
    background:#2563eb !important;
    color:#ffffff !important;
}

.nav-sidebar .nav-link.active i{
    color:#ffffff !important;
}

.nav-header{
    font-size:.65rem;
    font-weight:600;
    color:#94a3b8;
    letter-spacing:1px;
    margin:18px 18px 6px;
}

/* ===== CONTENT ===== */
.content-wrapper{
    background:#f8fafc;
}

/* ===== CARD MODERN ===== */
.card{
    border-radius:18px;
    border:1px solid #e5e7eb;
    box-shadow:0 4px 12px rgba(0,0,0,.03);
}

.card-header{
    background:#ffffff;
    border-bottom:1px solid #e5e7eb;
    font-weight:600;
}

/* ===== TABLE CLEAN ===== */
.table{
    margin:0;
}

.table thead{
    background:#f8fafc;
}

.table th{
    font-size:.75rem;
    text-transform:uppercase;
    letter-spacing:.5px;
    color:#64748b;
    border-bottom:1px solid #e5e7eb;
}

.table td{
    border-top:1px solid #f1f5f9;
}

/* ===== FOOTER ===== */
.main-footer{
    background:#ffffff;
    border-top:1px solid #e5e7eb;
    font-size:.8rem;
}

/* ===== SIDEBAR SCROLL ===== */
.main-sidebar .sidebar {
    height: calc(100vh - 70px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-bottom: 40px;
}

.main-sidebar .sidebar::-webkit-scrollbar {
    width: 6px;
}

.main-sidebar .sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.15);
    border-radius: 10px;
}
/* ===== NAIKKAN NAV HEADER ===== */
.nav-sidebar > .nav-header:first-child{
    margin-top: 5px !important;
    padding-top: 0 !important;
}

/* ❗ OFFLINE BUTTON TIDAK DIUBAH */
.offline-save-btn{
position:fixed;bottom:25px;right:25px;
padding:10px 18px;border-radius:30px;border:none;
background:white;font-weight:600;color:#1d4e6b;
box-shadow:0 6px 15px rgba(0,0,0,.08);z-index:1050;
}
.offline-save-btn:hover{background:#f0f9ff;}
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

<!-- ================= NAVBAR ================= -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link" data-widget="pushmenu" href="#">
<i class="fas fa-bars"></i>
</a>
</li>
<li class="nav-item d-none d-sm-inline-block">
<span class="nav-link">
<i class="far fa-calendar-alt mr-1"></i> {{ date('d F Y') }}
</span>
</li>
</ul>

<ul class="navbar-nav ml-auto">
<li class="nav-item">
<span class="nav-link">
<i class="far fa-user-circle mr-1"></i> Admin
</span>
</li>
<li class="nav-item">
<form method="POST" action="{{ route('logout') }}">
@csrf
<button type="submit" class="btn btn-link nav-link text-danger">
<i class="fas fa-sign-out-alt mr-1"></i> Logout
</button>
</form>
</li>
</ul>
</nav>

<!-- ================= SIDEBAR ================= -->
<aside class="main-sidebar sidebar-light-primary elevation-2">

<a href="{{ url('/admin') }}" class="brand-link custom-brand text-center">

    <div class="brand-title">
        WARUNG SEBLANG
    </div>

    <div class="brand-subtitle">
        POS & Accounting
    </div>

</a>


<div class="sidebar">

<nav class="mt-3">
<ul class="nav nav-pills nav-sidebar flex-column nav-child-indent"
data-widget="treeview" role="menu" data-accordion="false">

<!-- ================= UTAMA ================= -->
<li class="nav-header">UTAMA</li>

<li class="nav-item">
<a href="{{ url('/admin') }}"
class="nav-link {{ request()->is('admin') ? 'active' : '' }}">
<i class="fas fa-home nav-icon"></i>
<p>Dashboard</p>
</a>
</li>

<!-- ================= MANAJEMEN DATA ================= -->
<li class="nav-header">MANAJEMEN DATA</li>

<li class="nav-item {{ request()->is('admin/kategori*','admin/tipe*','admin/bahan*','admin/barang*','admin/stok-barang*','admin/layanan*','admin/supplier*','admin/karyawan*','admin/pelanggan*','admin/kode-akun*') ? 'menu-open' : '' }}">
<a href="#" class="nav-link {{ request()->is('admin/kategori*','admin/tipe*','admin/barang*','admin/layanan*','admin/supplier*','admin/karyawan*','admin/pelanggan*','admin/biaya-pengeluaran*','admin/kode-akun*','admin/modal*') ? 'active' : '' }}">
<i class="fas fa-database nav-icon"></i>
<p>Master Data <i class="right fas fa-angle-left"></i></p>
</a>
<ul class="nav nav-treeview">

<li class="nav-item">
<a href="{{ url('/admin/kategori') }}" class="nav-link {{ request()->is('admin/kategori*') ? 'active' : '' }}">
<i class="fas fa-tag nav-icon"></i><p>Kategori</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/tipe') }}" class="nav-link {{ request()->is('admin/tipe*') ? 'active' : '' }}">
<i class="fas fa-clipboard-list nav-icon"></i><p>Tipe</p>
</a>
</li>
<li class="nav-item">
    <a href="{{ route('bahan.index') }}"
       class="nav-link {{ request()->is('admin/bahan*') ? 'active' : '' }}">
        <i class="fas fa-boxes nav-icon"></i>
        <p>Bahan Baku</p>
    </a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/barang') }}" class="nav-link {{ request()->is('admin/barang*') ? 'active' : '' }}">
<i class="fas fa-box nav-icon"></i><p>Barang</p>
</a>
</li>

<li class="nav-item">
    <a href="{{ url('/admin/stok-barang') }}" 
       class="nav-link {{ request()->is('admin/stok-barang*') ? 'active' : '' }}">
        <i class="fas fa-warehouse nav-icon"></i>
        <p>Stok Barang</p>
    </a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/layanan') }}" class="nav-link {{ request()->is('admin/layanan*') ? 'active' : '' }}">
<i class="fas fa-concierge-bell nav-icon"></i><p>Layanan</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/supplier') }}" class="nav-link {{ request()->is('admin/supplier*') ? 'active' : '' }}">
<i class="fas fa-truck nav-icon"></i><p>Supplier</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/karyawan') }}" class="nav-link {{ request()->is('admin/karyawan*') ? 'active' : '' }}">
<i class="fas fa-users nav-icon"></i><p>Karyawan</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/pelanggan') }}" class="nav-link {{ request()->is('admin/pelanggan*') ? 'active' : '' }}">
<i class="fas fa-user-friends nav-icon"></i><p>Pelanggan</p>
</a>
</li>

<li class="nav-item">
    <a href="{{ url('/admin/biaya-pengeluaran') }}"
       class="nav-link {{ request()->is('admin/biaya-pengeluaran*') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave nav-icon"></i>
        <p>Biaya Pengeluaran</p>
    </a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/kode-akun') }}" class="nav-link {{ request()->is('admin/kode-akun*') ? 'active' : '' }}">
<i class="fas fa-book nav-icon"></i><p>Kode Akun</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/modal') }}" class="nav-link {{ request()->is('admin/modal*') ? 'active' : '' }}">
<i class="fas fa-hand-holding-usd nav-icon"></i>
<p>Modal</p>
</a>
</li>

</ul>
</li>
<!-- ================= PENGATURAN TRANSAKSI ================= -->
<li class="nav-item {{ request()->is('admin/pajak*') || request()->is('admin/diskon*') || request()->is('admin/shift*') ? 'menu-open' : '' }}">
    
    <a href="#" class="nav-link {{ request()->is('admin/pajak*') || request()->is('admin/diskon*') || request()->is('admin/shift*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-cogs"></i>
        <p>
            Pengaturan Transaksi
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>

    <ul class="nav nav-treeview">
        <li class="nav-item">
            <a href="{{ url('/admin/pajak') }}" class="nav-link {{ request()->is('admin/pajak*') ? 'active' : '' }}">
                <i class="fas fa-percent nav-icon"></i>
                <p>Pajak</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('/admin/diskon') }}" class="nav-link {{ request()->is('admin/diskon*') ? 'active' : '' }}">
                <i class="fas fa-tags nav-icon"></i>
                <p>Diskon</p>
            </a>
        </li>

        <li class="nav-item">
            <a href="{{ url('/admin/shift') }}" class="nav-link {{ request()->is('admin/shift*') ? 'active' : '' }}">
                <i class="fas fa-clock nav-icon"></i>
                <p>Shift</p>
            </a>
        </li>
    </ul>
</li>

<!-- ================= LAPORAN ================= -->
<li class="nav-header">LAPORAN</li>

<li class="nav-item 
{{ request()->is(
'admin/jurnal*','admin/buku-besar*','admin/laba-rugi*',
'admin/posisi-keuangan*','admin/catatan-keuangan*',
'admin/laporan-penjualan*','admin/laporan-pembelian*',
'admin/laporan-pajak*','admin/laporan-shift*',
'admin/laporan-stok*','admin/laporan-hutang*',
'admin/laporan-piutang*'
) ? 'menu-open' : '' }}">

<a href="#" class="nav-link 
{{ request()->is(
'admin/jurnal*','admin/buku-besar*','admin/laba-rugi*',
'admin/posisi-keuangan*','admin/catatan-keuangan*',
'admin/laporan-penjualan*','admin/laporan-pembelian*',
'admin/laporan-pajak*','admin/laporan-shift*',
'admin/laporan-stok*','admin/laporan-hutang*',
'admin/laporan-piutang*'
) ? 'active' : '' }}">

<i class="fas fa-chart-bar nav-icon"></i>
<p>
Laporan
<i class="right fas fa-angle-left"></i>
</p>
</a>

<ul class="nav nav-treeview">

<!-- ================= LAPORAN AKUNTANSI ================= -->
<li class="nav-item 
{{ request()->is(
'admin/jurnal*','admin/buku-besar*',
'admin/laba-rugi*','admin/posisi-keuangan*','admin/arus-kas*'
) ? 'menu-open' : '' }}">

<a href="#" class="nav-link 
{{ request()->is(
'admin/jurnal*','admin/buku-besar*',
'admin/laba-rugi*','admin/posisi-keuangan*','admin/arus-kas*'
) ? 'active' : '' }}">

<i class="fas fa-book nav-icon"></i>
<p>
Laporan Akuntansi
<i class="right fas fa-angle-left"></i>
</p>
</a>
<ul class="nav nav-treeview">

    <li class="nav-item">
        <a href="{{ url('/admin/jurnal') }}" 
           class="nav-link {{ request()->is('admin/jurnal*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-book"></i>
            <p>Jurnal Umum</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/buku-besar') }}" 
           class="nav-link {{ request()->is('admin/buku-besar*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-book-open"></i>
            <p>Buku Besar</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/laba-rugi') }}" 
           class="nav-link {{ request()->is('admin/laba-rugi*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-chart-line"></i>
            <p>Laba Rugi</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/posisi-keuangan') }}" 
           class="nav-link {{ request()->is('admin/posisi-keuangan*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-scale-balanced"></i>
            <p>Posisi Keuangan</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/catatan-keuangan') }}" 
           class="nav-link {{ request()->is('admin/catatan-keuangan*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-lines"></i>
            <p>CALK</p>
        </a>
    </li>

</ul>
</li>

<!-- ================= LAPORAN TRANSAKSI ================= -->
<li class="nav-item 
{{ request()->is(
'admin/laporan-penjualan*','admin/laporan-pembelian*',
'admin/laporan-pajak*','admin/laporan-shift*','admin/laporan-hutang*',
'admin/laporan-piutang*'
) ? 'menu-open' : '' }}">

<a href="#" class="nav-link 
{{ request()->is(
'admin/laporan-penjualan*','admin/laporan-pembelian*',
'admin/laporan-pajak*','admin/laporan-shift*','admin/laporan-hutang*',
'admin/laporan-piutang*'
) ? 'active' : '' }}">

<i class="fas fa-receipt nav-icon"></i>
<p>
Laporan Transaksi
<i class="right fas fa-angle-left"></i>
</p>
</a>

<ul class="nav nav-treeview">

    <li class="nav-item">
        <a href="{{ url('/admin/laporan-penjualan') }}" 
           class="nav-link {{ request()->is('admin/laporan-penjualan*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-cart-shopping"></i>
            <p>Laporan Penjualan</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/laporan-pembelian') }}" 
           class="nav-link {{ request()->is('admin/laporan-pembelian*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-truck"></i>
            <p>Laporan Pembelian</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/laporan-pajak') }}" 
           class="nav-link {{ request()->is('admin/laporan-pajak*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-file-invoice"></i>
            <p>Laporan Pajak</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/laporan-shift') }}" 
           class="nav-link {{ request()->is('admin/laporan-shift*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-user-clock"></i>
            <p>Laporan Shift</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/laporan-hutang') }}" 
           class="nav-link {{ request()->is('admin/laporan-hutang*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-hand-holding-dollar"></i>
            <p>Laporan Hutang</p>
        </a>
    </li>

    <li class="nav-item">
        <a href="{{ url('/admin/laporan-piutang') }}" 
           class="nav-link {{ request()->is('admin/laporan-piutang*') ? 'active' : '' }}">
            <i class="nav-icon fas fa-money-bill-wave"></i>
            <p>Laporan Piutang</p>
        </a>
    </li>

</ul>
</li>

</ul>
</li>


</ul>
</nav>

</div>
</aside>

<!-- ================= CONTENT ================= -->
<div class="content-wrapper">

<section class="content-header">
<div class="container-fluid">

<div class="d-flex justify-content-between align-items-center">

<div>
<h1 class="mb-0 font-weight-bold">
@yield('page_title')
</h1>

@if(View::hasSection('page_subtitle'))
<small class="text-muted">
@yield('page_subtitle')
</small>
@endif
</div>

<ol class="breadcrumb mb-0">
<li class="breadcrumb-item">
<a href="{{ url('/admin') }}">Home</a>
</li>

@if(View::hasSection('breadcrumb'))
@yield('breadcrumb')
@else
<li class="breadcrumb-item active">
@yield('page_title')
</li>
@endif

</ol>

</div>

</div>
</section>


<section class="content">
<div class="container-fluid">
@yield('content')
</div>
</section>
</div>

<footer class="main-footer text-sm">
<div class="float-right d-none d-sm-inline">
Version 1.0.0
</div>
<strong>Copyright &copy; 2026 Warung Seblang.</strong>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>


<button class="offline-save-btn" onclick="manualSync()">
<i class="fas fa-sync mr-2"></i> Sinkronkan Data
</button>

@stack('scripts')

<script>
/* ===============================
   REGISTER SERVICE WORKER
================================ */
if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
        navigator.serviceWorker.register("/service-worker.js")
        .then(reg => console.log("SW Registered"))
        .catch(err => console.log("SW Error:", err));
    });
}

/* ===============================
   AUTO SYNC SAAT ONLINE
================================ */
window.addEventListener("online", () => {
    navigator.serviceWorker.ready.then((sw) => {
        if ("sync" in sw) {
            sw.sync.register("sync-transactions");
        }
    });
});

function manualSync() {
    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.ready.then((sw) => {
            if ("sync" in sw) {
                sw.sync.register("sync-transactions");
                alert("Sinkronisasi dimulai");
            } else {
                alert("Browser tidak mendukung background sync");
            }
        });
    }
}
</script>
<script>

if("serviceWorker" in navigator){

window.addEventListener("load",()=>{

navigator.serviceWorker.register("/service-worker.js")
.then(()=>console.log("SW aktif"))
.catch(err=>console.log("SW gagal",err));

});

}

</script>
<script>

window.addEventListener("online",()=>{

navigator.serviceWorker.ready.then(sw=>{

if("sync" in sw){

sw.sync.register("sync-transactions");

}

});

});

</script>
<script src="{{ asset('js/offline-pos.js') }}"></script>
</body>
</html>
