@extends('layouts.admin')

@section('content')
<div class="container-fluid">

    <!-- HEADER -->
    <div class="mb-4">
        <h3 class="fw-bold">Dashboard</h3>
        <small class="text-muted">Selamat datang 👋</small>
    </div>

    <!-- CARD -->
    <div class="row g-3 mb-4">

        <!-- TRANSAKSI -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-white"
                 style="background: linear-gradient(135deg,#36d1dc,#5b86e5); border-radius:15px;">
                <small>Transaksi Hari Ini</small>
                <h2 class="fw-bold">{{ $totalTransaksi }}</h2>

                <div class="mt-2 small">
                    @foreach($transaksiPerKategori as $item)
                        • {{ $item->nama_kategori }} ({{ $item->total }})<br>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- PEMASUKAN -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-white"
                 style="background: linear-gradient(135deg,#11998e,#38ef7d); border-radius:15px;">
                <small>Pemasukan Hari Ini</small>
                <h2 class="fw-bold">
                    Rp {{ number_format($totalPemasukan,0,',','.') }}
                </h2>
                <small>+ dari transaksi hari ini</small>
            </div>
        </div>

        <!-- LABA -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 text-white"
                 style="background: linear-gradient(135deg,#f7971e,#ffd200); border-radius:15px;">
                <small>Laba Bersih (Estimasi)</small>
                <h2 class="fw-bold">
                    Rp {{ number_format($totalPemasukan * 0.4,0,',','.') }}
                </h2>
                <small>estimasi 40%</small>
            </div>
        </div>

    </div>

    <!-- GRAFIK -->
    <div class="card border-0 shadow-sm p-4 mb-4" style="border-radius:15px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">Grafik Penjualan</h5>
            <small class="text-muted">7 hari terakhir</small>
        </div>
        <canvas id="chartPenjualan" height="100"></canvas>
    </div>

    <!-- TABLE -->
    <div class="card border-0 shadow-sm p-4" style="border-radius:15px;">
        <h5 class="fw-bold mb-3">Detail Transaksi Hari Ini</h5>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Kategori</th>
                    <th class="text-center">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksiPerKategori as $item)
                <tr>
                    <td>{{ $item->nama_kategori }}</td>
                    <td class="text-center fw-bold">{{ $item->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('chartPenjualan');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($labels),
        datasets: [{
            label: 'Penjualan',
            data: @json($data),
            borderWidth: 3,
            tension: 0.4,
            fill: true,
            backgroundColor: 'rgba(54, 209, 220, 0.2)',
            borderColor: '#36d1dc',
            pointBackgroundColor: '#36d1dc'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

@endpush