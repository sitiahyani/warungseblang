<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Kasir - Warung Seblang</title>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<script>
tailwind.config = {
  corePlugins: { preflight: false }
}
</script>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body{
    font-family:'Inter',sans-serif;
}

/* ===== NAVBAR SAME AS ADMIN ===== */
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

/* ===== SIDEBAR SAME AS ADMIN ===== */
.main-sidebar{
    background:#ffffff;
    border-right:1px solid #e5e7eb;
    box-shadow:none;
}

.brand-link{
    background:#ffffff !important;
    border-bottom:1px solid #e5e7eb;
}

.brand-text{
    font-weight:700;
    color:#0f172a;
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

/* ===== CONTENT ===== */
.content-wrapper{
    background:#f8fafc;
}

.card{
    border-radius:18px;
    border:1px solid #e5e7eb;
    box-shadow:0 4px 12px rgba(0,0,0,.03);
}

/* ===== FOOTER ===== */
.main-footer{
    background:#ffffff;
    border-top:1px solid #e5e7eb;
    font-size:.8rem;
}
</style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">

<div class="wrapper">

<!-- NAVBAR -->
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
<i class="far fa-user-circle mr-1"></i> Kasir
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

<!-- SIDEBAR -->
<aside class="main-sidebar sidebar-light-primary elevation-2">

<a href="#" class="brand-link text-center">
<span class="brand-text">WARUNG SEBLANG</span><br>
<small class="text-muted">POS & Accounting</small>
</a>

<div class="sidebar">
<nav class="mt-3">
<ul class="nav nav-pills nav-sidebar flex-column" role="menu">

<li class="nav-item">
<a href="{{ route('kasir.pembelian') }}"
class="nav-link {{ request()->is('kasir/pembelian*') ? 'active' : '' }}">
<i class="fas fa-shopping-cart nav-icon"></i>
<p>Pembelian</p>
</a>
</li>
<li class="nav-item">
<a href="{{ route('kasir.hutang') }}"
class="nav-link {{ request()->is('kasir/hutang*') ? 'active' : '' }}">
<i class="fas fa-money-bill-wave nav-icon"></i>
<p>Pembayaran Hutang</p>
</a>
</li>

</ul>
</nav>
</div>
</aside>

<!-- CONTENT -->
<div class="content-wrapper">
<section class="content pt-3">
<div class="container-fluid">
@yield('content')
</div>
</section>
</div>

<footer class="main-footer text-sm">
<strong>Copyright &copy; 2026 Warung Seblang.</strong>
</footer>

</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>
</html>