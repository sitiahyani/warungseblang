<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="csrf-token" content="{{ csrf_token() }}">
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
    background:#1e40af;
    border-bottom:1px solid rgba(255,255,255,.15);
}

.brand-text{
    font-weight:700;
    color:#ffffff;
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
    background:#1e40af !important;
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

/* ===== STATUS INTERNET ===== */
.internet-status{
    padding:4px 10px;
    border-radius:20px;
    font-size:.75rem;
    font-weight:600;
}

.status-online{
    background:#dcfce7;
    color:#166534;
}

.status-offline{
    background:#fee2e2;
    color:#991b1b;
}

.offline-save-btn{
position:fixed;
bottom:20px;
right:20px;
background:#2563eb;
color:white;
border:none;
padding:12px 16px;
border-radius:10px;
font-weight:600;
box-shadow:0 4px 10px rgba(0,0,0,.2);
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

<li class="nav-item mr-3">
<span id="internet-status" class="internet-status status-online">
🟢 Online
</span>
</li>

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
<a href="{{ route('dashboard') }}"
class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
<i class="fas fa-home nav-icon"></i>
<p>Dashboard</p>
</a>
</li>

<li class="nav-item">
<a href="{{ route('cashdrawer') }}"
class="nav-link {{ request()->routeIs('kasir.cashdrawer') ? 'active' : '' }}">
<i class="fas fa-cash-register nav-icon"></i>
<p>Cash Drawer</p>
</a>
</li>

<li class="nav-item">
<a href="{{ route('kasir.riwayat') }}"
class="nav-link {{ request()->routeIs('kasir.riwayat') ? 'active' : '' }}">
<i class="fas fa-history nav-icon"></i>
<p>Riwayat</p>
</a>
</li>

<li class="nav-header">LAYANAN</li>

<li class="nav-item">
<a href="{{ route('penjualan.resto') }}"
class="nav-link {{ request()->routeIs('penjualan.resto') ? 'active' : '' }}">
<i class="fas fa-utensils nav-icon"></i>
<p>Resto</p>
</a>
</li>

<li class="nav-item">
<a href="{{ route('penjualan.homestay') }}"
class="nav-link {{ request()->routeIs('penjualan.homestay') ? 'active' : '' }}">
<i class="fas fa-bed nav-icon"></i>
<p>Homestay</p>
</a>
</li>

<li class="nav-item">
<a href="{{ route('penjualan.wedding') }}"
class="nav-link {{ request()->routeIs('penjualan.wedding') ? 'active' : '' }}">
<i class="fas fa-ring nav-icon"></i>
<p>Aula / Wedding</p>
</a>
</li>

<li class="nav-item">
<a href="{{ route('kasir.pembelian') }}"
class="nav-link {{ request()->routeIs('kasir.pembelian') ? 'active' : '' }}">
<i class="fas fa-shopping-cart nav-icon"></i>
<p>Pembelian</p>
</a>
</li>

<li class="nav-header">KEUANGAN</li>

<li class="nav-item">
<a href="{{ route('kasir.hutang') }}"
class="nav-link {{ request()->routeIs('kasir.hutang') ? 'active' : '' }}">
<i class="fas fa-money-bill-wave nav-icon"></i>
<p>Pembayaran Hutang</p>
</a>
</li>

<li class="nav-item">
    <a href="{{ route('hutang_pelanggan') }}"
       class="nav-link {{ request()->routeIs('hutang_pelanggan') ? 'active' : '' }}">
        <i class="fas fa-money-bill-wave nav-icon"></i>
        <p>Pembayaran Piutang</p>
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

<button class="offline-save-btn" onclick="manualSync()">
<i class="fas fa-sync mr-2"></i> Sinkronkan Data
</button>

@stack('scripts')

<script>

/* ===============================
   STATUS ONLINE OFFLINE
================================ */


<script>
async function updateInternetStatus() {
    let status = document.getElementById("internet-status");

    try {
        await fetch("/offline.html", {
            method: "HEAD",
            cache: "no-store",
        });

        status.innerHTML = "🟢 Online";
        status.classList.remove("status-offline");
        status.classList.add("status-online");
    } catch {
        status.innerHTML = "🔴 Offline";
        status.classList.remove("status-online");
        status.classList.add("status-offline");
    }
}

setInterval(updateInternetStatus, 3000);
updateInternetStatus();


/* ===============================
   REGISTER SERVICE WORKER
================================ */

if("serviceWorker" in navigator){

window.addEventListener("load",()=>{

navigator.serviceWorker.register("/service-worker.js")
.then(()=>console.log("SW aktif"))
.catch(err=>console.log("SW gagal",err))

})

}


/* ===============================
   AUTO SYNC SAAT ONLINE
================================ */

window.addEventListener("online",()=>{

navigator.serviceWorker.ready.then(sw=>{

if("sync" in sw){

sw.sync.register("sync-transactions")

}

})

})


function manualSync(){

if("serviceWorker" in navigator){

navigator.serviceWorker.ready.then(sw=>{

if("sync" in sw){

sw.sync.register("sync-transactions")
alert("Sinkronisasi dimulai")

}else{

alert("Browser tidak mendukung background sync")

}

})

}

}

</script>

<script src="{{ asset('js/offline-pos.js') }}"></script>

<script>
if ("serviceWorker" in navigator) {
    window.addEventListener("load", () => {
        navigator.serviceWorker
            .register("/service-worker.js")
            .then(() => console.log("SW aktif"))
            .catch((err) => console.log("SW gagal", err));
    });
}
</script>
</body>
</html>