@extends('layouts.kasir')

@section('content')
<div class="container-fluid">

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h4 class="mb-0">Pembayaran Hutang Pelanggan</h4>
            <small class="text-muted">
                Kelola cicilan transaksi resto, wedding, dan homestay
            </small>
        </div>

        <div class="mb-2 px-3 mt-2">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text"
                    id="searchHutang"
                    placeholder="Cari kode / pelanggan...">
            </div>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="bg-light text-center">
                        <tr>
                            <th>Kode</th>
                            <th>Pelanggan</th>
                            <th>No HP</th>
                            <th>Total</th>
                            <th>DP</th>
                            <th>Sisa</th>
                            <th>Status</th>
                            <th style="width:230px;">Bayar Cicilan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hutangPelanggan as $hutang)
                        <tr class="{{ $hutang->sisa_fix > 0 ? 'table-warning' : '' }}">
                            <td>
                                <strong>{{ $hutang->kode_transaksi }}</strong>
                            </td>

                            <td>
                                {{ $hutang->pelangganRel->nama_pelanggan
                                    ?? $hutang->nama_pelanggan
                                    ?? '-' }}
                            </td>

                            <td>
                                {{ $hutang->pelangganRel->no_hp ?? '-' }}
                            </td>

                            <td class="text-success fw-bold">
                                Rp {{ number_format($hutang->total,0,',','.') }}
                            </td>

                            <td class="text-primary fw-bold">
                                Rp {{ number_format($hutang->bayar,0,',','.') }}
                            </td>

                            <td class="text-danger fw-bold">
                                Rp {{ number_format($hutang->sisa_fix,0,',','.') }}
                            </td>

                            <td class="text-center">
                                @if($hutang->sisa_fix <= 0)
                                    <span class="badge bg-success">Lunas</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum</span>
                                @endif
                            </td>

                            <td>
                                @if($hutang->sisa_fix > 0)
                                <form method="POST" action="{{ route('hutang_pelanggan.bayar') }}">
                                    @csrf
                                    <input type="hidden" name="id_penjualan" value="{{ $hutang->id_penjualan }}">

                                    <div class="d-flex gap-2">
                                        <input type="number"
                                            name="jumlah_bayar"
                                            class="form-control form-control-sm"
                                            placeholder="Nominal"
                                            min="1"
                                            max="{{ $hutang->sisa_fix }}"
                                            required>

                                        <button class="btn btn-success btn-sm">
                                            Bayar
                                        </button>

                                        <!-- 🔥 DETAIL TETAP ADA -->
                                        <button type="button"
                                            onclick='showDetail(@json($hutang))'
                                            class="btn btn-outline-info btn-sm"
                                            title="Detail">
                                            👁
                                        </button>
                                    </div>
                                </form>
                                @else
                                <div class="d-flex gap-2">
                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                        Lunas
                                    </button>

                                    <!-- 🔥 DETAIL TETAP ADA -->
                                    <button type="button"
                                        onclick='showDetail(@json($hutang))'
                                        class="btn btn-outline-info btn-sm"
                                        title="Detail">
                                        👁
                                    </button>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                Belum ada hutang pelanggan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

{{-- ================= MODAL DETAIL ================= --}}
<div id="modalDetail" style="display:none; position:fixed; top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);justify-content:center;align-items:center;">
    <div style="background:#fff;padding:20px;border-radius:10px;width:400px;">
        <h4>Riwayat Pembayaran</h4>
        <div id="detailContent"></div>

        <button onclick="closeModal()" class="btn btn-secondary mt-2">Tutup</button>
    </div>
</div>

{{-- ================= MODAL STRUK ================= --}}
<div id="receiptModal" class="receipt-overlay" style="display:none;">
    <div class="receipt-box">
        <div class="receipt-header">
            <h3>WARUNG SEBLANG</h3>
            <small>POS & Accounting</small>
        </div>

        <div class="receipt-body" id="receiptContent"></div>

        <div class="receipt-footer">
            <button id="printReceiptBtn" class="btn btn-primary">Cetak</button>
            <button id="closeReceiptBtn" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</div>

<style>
.receipt-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.45);
    display:flex;
    align-items:center;
    justify-content:center;
    z-index:9999;
}
.receipt-box{
    width:360px;
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 20px 40px rgba(0,0,0,.2);
}
.receipt-header{
    background:#0b1635;
    color:#fff;
    text-align:center;
    padding:18px;
}
.receipt-body{
    padding:18px;
    font-size:14px;
}
.receipt-body .row-line{
    display:flex;
    justify-content:space-between;
    margin-bottom:8px;
}
.receipt-footer{
    display:flex;
    gap:10px;
    padding:16px;
}
.receipt-footer button{
    flex:1;
}

.search-box {
    position: relative;
    width: 240px; /* 🔥 diperkecil */
}

.search-box i {
    position: absolute;
    top: 50%;
    left: 10px;
    transform: translateY(-50%);
    font-size: 12px; /* 🔥 lebih kecil */
    color: #999;
}

.search-box input {
    width: 100%;
    padding: 6px 10px 6px 30px; /* 🔥 lebih slim */
    border-radius: 10px; /* 🔥 jangan terlalu bulat */
    border: 1px solid #ddd;
    font-size: 13px;
    height: 32px; /* 🔥 lebih pendek */
}

.search-box input:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 3px rgba(13,110,253,0.2);
}
</style>

{{-- ================= PRINT MODAL ================= --}}
@if(session('print_data'))
<script>
document.addEventListener('DOMContentLoaded', function () {

    const data = @json(session('print_data'));

    const modal = document.getElementById('receiptModal');
    const content = document.getElementById('receiptContent');
    const printBtn = document.getElementById('printReceiptBtn');
    const closeBtn = document.getElementById('closeReceiptBtn');

    if (!modal || !content) return;

    content.innerHTML = `
        <div class="row-line"><span>No. Transaksi</span><strong>${data.kode}</strong></div>
        <div class="row-line"><span>Pelanggan</span><span>${data.pelanggan}</span></div>
        <div class="row-line"><span>Total</span><strong>Rp ${Number(data.total).toLocaleString('id-ID')}</strong></div>
        <div class="row-line"><span>Bayar</span><strong>Rp ${Number(data.bayar_sekarang).toLocaleString('id-ID')}</strong></div>
        <div class="row-line"><span>Sisa</span><strong>Rp ${Number(data.sisa).toLocaleString('id-ID')}</strong></div>
        <div class="row-line"><span>Status</span><strong>${data.status}</strong></div>
        <hr>
        <p style="text-align:center;color:#666;">Terima kasih atas pembayaran Anda</p>
    `;

    modal.style.display = 'flex';

    printBtn?.addEventListener('click', () => window.print());
    closeBtn?.addEventListener('click', () => modal.style.display = 'none');

});
</script>
@endif


{{-- ================= DETAIL MODAL ================= --}}
<script>
function showDetail(data) {

    let html = `
        <div style="margin-bottom:10px;">
            <b>Kode:</b> ${data.kode_transaksi}<br>
            <b>Pelanggan:</b> ${data.pelanggan_rel?.nama_pelanggan ?? '-'}<br>
            <b>Total:</b> Rp ${parseInt(data.total).toLocaleString('id-ID')}<br>
            <b>Terbayar:</b> Rp ${parseInt(data.bayar).toLocaleString('id-ID')}<br>
            <b>Sisa:</b> Rp ${(data.total - data.bayar).toLocaleString('id-ID')}<br>
        </div>
        <hr>
        <b>Riwayat Pembayaran:</b><br>
    `;

    let histori = data.histori_pembayaran ?? [];

    if (histori.length === 0) {
        html += `<i>Belum ada pembayaran</i>`;
    } else {

        // urut dari lama → baru
        histori.sort((a,b) => new Date(a.tanggal_bayar) - new Date(b.tanggal_bayar));

        histori.forEach((item, index) => {
            html += `
                <div style="padding:6px 0;border-bottom:1px dashed #ddd;">
                    <small>Cicilan ${index + 1}</small><br>
                    ${item.tanggal_bayar}<br>
                    <b>Rp ${parseInt(item.jumlah_bayar).toLocaleString('id-ID')}</b>
                </div>
            `;
        });
    }

    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('modalDetail').style.display = 'flex';
}

function closeModal() {
    document.getElementById('modalDetail').style.display = 'none';
}
</script>


{{-- ================= SEARCH (FIX UTAMA) ================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const input = document.getElementById('searchHutang');
    if (!input) return;

    input.addEventListener('keyup', function () {

        const keyword = this.value.toLowerCase().trim();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {

            const kode = row.children[0]?.innerText.toLowerCase() || '';
            const pelanggan = row.children[1]?.innerText.toLowerCase() || '';

            if (keyword === '') {
                row.style.display = '';
                return;
            }

            if (kode.includes(keyword) || pelanggan.includes(keyword)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }

        });

    });

});
</script>
@endsection