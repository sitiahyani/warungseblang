<!DOCTYPE html>
<html>
<head>
    <title>Kasir - Warung Seblang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body { background:#f4f6f9; }
        .sidebar {
            height:100vh;
            background:#343a40;
            color:white;
        }
        .sidebar a {
            color:white;
            display:block;
            padding:12px 20px;
        }
        .sidebar a.active,
        .sidebar a:hover {
            background:#495057;
            text-decoration:none;
        }
    </style>
</head>

<body>

<div class="container-fluid">
<div class="row">

<div class="col-md-2 sidebar p-0">
    <h5 class="text-center py-3 border-bottom">KASIR</h5>

    <a href="{{ route('kasir.pembelian') }}"
       class="{{ request()->is('kasir/pembelian*') ? 'active' : '' }}">
       <i class="fas fa-shopping-cart"></i> Pembelian
    </a>
</div>

<div class="col-md-10 p-4">
    @yield('content')
</div>

</div>
</div>

</body>
</html>
