@extends('layouts.kasir')

@section('title', 'Layanan Transaksi')

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Layanan Transaksi</h2>
            <p class="text-muted mb-0">Pilih Layanan Untuk Memulai Transaksi Baru</p>
        </div>

        <div class="d-flex align-items-center gap-3">

            <!-- PROFILE -->
            <div class="d-flex align-items-center bg-light rounded-pill px-3 py-2 shadow-sm">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                    style="width:40px;height:40px;">
                    {{ substr(Auth::user()->name ?? 'K', 0, 1) }}
                </div>
                <div class="lh-sm">
                    <div class="fw-semibold">{{ Auth::user()->name ?? 'Kasir' }}</div>
                    <small class="text-muted">Shift Aktif (08.00 - 22.00)</small>
                </div>
            </div>

            <!-- LOGOUT -->
            <form action="{{ route('logout') }}" method="POST" class="ms-3">
                @csrf
                <button type="submit"
                        style="border:none;background:none;"
                        class="text-danger fw-semibold">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </button>
            </form>

        </div>
    </div>

    <hr class="mb-4">

    <!-- STATISTIK -->
    <div class="row g-4 mb-5 justify-content-center">

        <!-- JUMLAH TRANSAKSI -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                <div class="card-body d-flex align-items-center px-4">

                    <div class="bg-warning text-white rounded-3 d-flex align-items-center justify-content-center me-4 stat-icon">
                        <i class="fas fa-shopping-cart fa-lg"></i>
                    </div>

                    <div>
                        <div class="text-muted small mb-1">Jumlah Transaksi</div>
                        <h3 class="fw-bold mb-0">
                            {{ $totalTransaksi ?? 24 }}
                        </h3>
                    </div>

                </div>
            </div>
        </div>

        <!-- PENDAPATAN -->
        <div class="col-md-5">
            <div class="card border-0 shadow-sm rounded-4 h-100 stat-card">
                <div class="card-body d-flex align-items-center px-4">

                    <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center me-4 stat-icon">
                        <i class="fas fa-coins fa-lg"></i>
                    </div>

                    <div>
                        <div class="text-muted small mb-1">Pendapatan</div>
                        <h3 class="fw-bold mb-0">
                            Rp {{ number_format($totalPendapatan ?? 3835000,0,',','.') }}
                        </h3>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- LAYANAN -->
    <div class="row g-4">

        <!-- RESTO -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100 overflow-hidden">
                <div class="bg-primary" style="height:6px;"></div>
                <div class="p-4 d-flex flex-column">
                    <div class="mb-3">
                        <i class="fas fa-utensils fa-3x text-dark"></i>
                    </div>
                    <h5 class="fw-bold">Resto & Kafe</h5>
                    <p class="text-muted small flex-grow-1">
                        Transaksi makanan dan minuman harian. Cetak struk,
                        kelola meja, dan buat pesanan.
                    </p>
                    <a href="{{ route('penjualan.resto') }}"
                       class="btn btn-primary rounded-pill px-4 mt-3">
                        ➜ Pilih Layanan
                    </a>
                </div>
            </div>
        </div>

        <!-- HOMESTAY -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100 overflow-hidden">
                <div class="bg-primary" style="height:6px;"></div>
                <div class="p-4 d-flex flex-column">
                    <div class="mb-3">
                        <i class="fas fa-hotel fa-3x text-dark"></i>
                    </div>
                    <h5 class="fw-bold">Homestay</h5>
                    <p class="text-muted small flex-grow-1">
                        Reservasi kamar, check-in/out, pembayaran,
                        dan kelola ketersediaan kamar.
                    </p>
                    <a href="{{ route('penjualan.homestay') }}"
                       class="btn btn-primary rounded-pill px-4 mt-3">
                        ➜ Pilih Layanan
                    </a>
                </div>
            </div>
        </div>

        <!-- WEDDING -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center h-100 overflow-hidden">
                <div class="bg-primary" style="height:6px;"></div>
                <div class="p-4 d-flex flex-column">
                    <div class="mb-3">
                        <i class="fas fa-ring fa-3x text-dark"></i>
                    </div>
                    <h5 class="fw-bold">Paket Wedding</h5>
                    <p class="text-muted small flex-grow-1">
                        Kelola paket acara pernikahan, DP, angsuran,
                        dan layanan tambahan.
                    </p>
                    <a href="{{ route('penjualan.wedding') }}"
                       class="btn btn-primary rounded-pill px-4 mt-3">
                        ➜ Pilih Layanan
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<style>
.stat-card {
    min-height: 110px;
}

.stat-icon {
    width: 60px;
    height: 60px;
}
</style>

@endsection