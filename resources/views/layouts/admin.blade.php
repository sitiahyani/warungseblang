<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title','Admin Dashboard') - Warung Seblang</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
body{font-family:'Inter',sans-serif;background:#f4f7fc;}
.main-sidebar{box-shadow:2px 0 12px rgba(0,0,0,.04);}
.brand-link{background:#0a3147;color:white;text-align:center;padding:18px;}
.brand-link:hover{color:white;}
.brand-text{font-weight:700;font-size:1.1rem;}
.brand-sub{font-size:.75rem;opacity:.8;}

.nav-sidebar .nav-link{font-weight:500;font-size:.9rem;border-radius:8px;margin:2px 10px;}
.nav-sidebar .nav-link.active{background:#1d4e6b !important;color:white !important;}
.nav-sidebar .nav-link.active i{color:white !important;}

.card{border-radius:12px;border:none;box-shadow:0 4px 12px rgba(0,0,0,.03);}
.card-header{font-weight:600;}
.table{margin:0;}
.main-footer{font-size:.85rem;}

.offline-save-btn{
position:fixed;bottom:25px;right:25px;
padding:10px 18px;border-radius:30px;border:none;
background:white;font-weight:600;color:#1d4e6b;
box-shadow:0 6px 15px rgba(0,0,0,.08);z-index:1050;
}
.offline-save-btn:hover{background:#f0f9ff;}
/* Sidebar full height & scroll sendiri */
.main-sidebar .sidebar {
    height: calc(100vh - 70px);
    overflow-y: auto;
    overflow-x: hidden;
    padding-bottom: 40px;
    scroll-behavior: smooth;
}

/* Scrollbar lebih halus */
.main-sidebar .sidebar::-webkit-scrollbar {
    width: 6px;
}

.main-sidebar .sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.15);
    border-radius: 10px;
}

.main-sidebar .sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.25);
}
/* ===== BRAND TEXT ONLY ===== */
.custom-brand {
    background: #0b2f44;
    padding: 20px 10px 18px;
    border-bottom: 1px solid rgba(255,255,255,0.08);
}

.brand-title {
    font-size: 1.1rem;
    font-weight: 700;
    letter-spacing: 1px;
    color: #ffffff;
}

.brand-subtitle {
    font-size: 0.75rem;
    color: #ffffff;
    opacity: 0.8;
    letter-spacing: 1.5px;
    margin-top: 4px;
}
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #28a745;
    border-color: #28a745;
}

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
<li class="nav-header">PENGATURAN TRANSAKSI</li>

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

<!-- ================= LAPORAN ================= -->
<!-- ================= LAPORAN ================= -->
<li class="nav-header">LAPORAN</li>

<li class="nav-item 
{{ request()->is(
'admin/jurnal*','admin/buku-besar*','admin/laba-rugi*',
'admin/posisi-keuangan*',
'admin/arus-kas*',
'admin/laporan-penjualan*',
'admin/laporan-pembelian*',
'admin/laporan-pajak*',
'admin/laporan-shift*',
'admin/laporan-stok*',
'admin/laporan-hutang*',
'admin/laporan-piutang*'
) ? 'menu-open' : '' }}">

<a href="#" class="nav-link 
{{ request()->is(
'admin/laba-rugi*',
'admin/posisi-keuangan*',
'admin/arus-kas*',
'admin/laporan-penjualan*',
'admin/laporan-pembelian*',
'admin/laporan-pajak*',
'admin/laporan-shift*',
'admin/laporan-stok*',
'admin/laporan-hutang*',
'admin/laporan-piutang*'
) ? 'active' : '' }}">

<i class="fas fa-chart-bar nav-icon"></i>
<p>
Laporan
<i class="right fas fa-angle-left"></i>
</p>
</a>

<ul class="nav nav-treeview">

<li class="nav-item">
<a href="{{ url('/admin/jurnal') }}" class="nav-link {{ request()->is('admin/jurnal*') ? 'active' : '' }}">
<i class="fas fa-book-open nav-icon"></i>
<p>Jurnal Umum</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/buku-besar') }}" class="nav-link {{ request()->is('admin/buku-besar*') ? 'active' : '' }}">
<i class="fas fa-archive nav-icon"></i>
<p>Buku Besar</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laba-rugi') }}" 
class="nav-link {{ request()->is('admin/laba-rugi*') ? 'active' : '' }}">
<i class="fas fa-chart-line nav-icon"></i>
<p>Laba Rugi</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/posisi-keuangan') }}" 
class="nav-link {{ request()->is('admin/posisi-keuangan*') ? 'active' : '' }}">
<i class="fas fa-balance-scale nav-icon"></i>
<p>Posisi Keuangan</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/arus-kas') }}" 
class="nav-link {{ request()->is('admin/arus-kas*') ? 'active' : '' }}">
<i class="fas fa-coins nav-icon"></i>
<p>Arus Kas</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-penjualan') }}" 
class="nav-link {{ request()->is('admin/laporan-penjualan*') ? 'active' : '' }}">
<i class="fas fa-shopping-cart nav-icon"></i>
<p>Laporan Penjualan</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-pembelian') }}" 
class="nav-link {{ request()->is('admin/laporan-pembelian*') ? 'active' : '' }}">
<i class="fas fa-truck-loading nav-icon"></i>
<p>Laporan Pembelian</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-pajak') }}" 
class="nav-link {{ request()->is('admin/laporan-pajak*') ? 'active' : '' }}">
<i class="fas fa-file-invoice-dollar nav-icon"></i>
<p>Laporan Pajak</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-shift') }}" 
class="nav-link {{ request()->is('admin/laporan-shift*') ? 'active' : '' }}">
<i class="fas fa-user-clock nav-icon"></i>
<p>Laporan Shift</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-stok') }}" 
class="nav-link {{ request()->is('admin/laporan-stok*') ? 'active' : '' }}">
<i class="fas fa-boxes nav-icon"></i>
<p>Laporan Stok</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-hutang') }}" 
class="nav-link {{ request()->is('admin/laporan-hutang*') ? 'active' : '' }}">
<i class="fas fa-arrow-down nav-icon"></i>
<p>Laporan Hutang</p>
</a>
</li>

<li class="nav-item">
<a href="{{ url('/admin/laporan-piutang') }}" 
class="nav-link {{ request()->is('admin/laporan-piutang*') ? 'active' : '' }}">
<i class="fas fa-arrow-up nav-icon"></i>
<p>Laporan Piutang</p>
</a>
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

<button class="offline-save-btn" onclick="simpanDataOfflineAdmin()">
<i class="fas fa-cloud-download-alt mr-2"></i> Simpan Data Admin
</button>

@stack('scripts')

</body>
</html>
