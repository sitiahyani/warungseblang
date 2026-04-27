@extends('layouts.kasir')

@section('content')
<div class="container-fluid">

    <h3 class="mb-1">Riwayat Transaksi</h3>
    <p class="text-muted">Cek riwayat transaksi tunai, kredit, dan cicilan di sini</p>

    <!-- ================= FILTER ================= -->
    <form method="GET">
        <div class="card mb-3 p-3">
            <div class="row">

                <div class="col-md-4">
                    <label>Rentang Tanggal</label>
                    <input type="text"
                           name="tanggal"
                           id="tanggal"
                           class="form-control"
                           value="{{ request('tanggal') }}"
                           placeholder="2026-03-01 - 2026-03-31">
                </div>

                <div class="col-md-3">
                    <label>Metode Pembayaran</label>
                    <select name="metode_bayar" class="form-control">
                        <option value="semua">Semua</option>
                        <option value="tunai" {{ request('metode_bayar')=='tunai'?'selected':'' }}>
                            Tunai
                        </option>
                        <option value="kredit" {{ request('metode_bayar')=='kredit'?'selected':'' }}>
                            Kredit
                        </option>
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Terapkan</button>
                </div>

            </div>
        </div>
    </form>

    <!-- ================= PENJUALAN ================= -->
    <h5>Riwayat Penjualan</h5>

    <div class="card mb-4">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kategori</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($penjualan as $p)
                    <tr>
                        <td>{{ $p->kode_transaksi }}</td>
                        <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y H:i') }}</td>

                        <td>
                            {{ $p->pelangganRel->nama_pelanggan 
                                ?? $p->nama_pelanggan 
                                ?? '-' }}
                        </td>

                        <td>
                            @if($p->kategoriRel)
                                {{ $p->kategoriRel->nama_kategori }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            {{ 
                                ($p->details->count() ?? 0) + 
                                ($p->detailLayanan->count() ?? 0) 
                            }} item
                        </td>

                        <td class="text-success">
                            Rp {{ number_format($p->total,0,',','.') }}
                        </td>

                        <td>
                            @if(($p->status_fix ?? 'dp') == 'lunas')
                                <span class="badge badge-success">Lunas</span>
                            @else
                                <span class="badge badge-warning">DP</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-info">
                                {{ ucfirst($p->metode_bayar) }}
                            </span>
                        </td>

                        <td>
                            <button class="btn btn-sm btn-light"
                                    onclick='showDetail(@json($p))'>
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">
                            Belum ada transaksi
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= PEMBELIAN ================= -->
    <h5 class="mt-4">Riwayat Pembelian</h5>

    <div class="card mb-4">
        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead class="bg-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Supplier</th>
                        <th>Bahan</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembelian as $pb)
                        <tr>
                            <td>PB-{{ $pb->id_pembelian }}</td>

                            <td>
                                {{ \Carbon\Carbon::parse($pb->tanggal)->format('d M Y H:i') }}
                            </td>

                            <td>
                                {{ $pb->supplier->nama_supplier ?? '-' }}
                            </td>

                            <td>
                                {{ $pb->detailPembelian->first()->bahan->nama_bahan ?? '-' }}
                            </td>

                            <td>
                                {{ $pb->detailPembelian->first()->qty ?? 0 }}
                            </td>

                            <td class="text-success">
                                Rp {{ number_format($pb->total,0,',','.') }}
                            </td>

                            <td>
                                <span class="badge badge-info">
                                    {{ ucfirst($pb->metode_bayar) }}
                                </span>
                            </td>

                            <td>
                                @if($pb->status == 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @else
                                    <span class="badge badge-warning">Belum</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                Belum ada pembelian
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ================= HISTORI CICILAN ================= -->
    <h5 class="mt-4">Histori Pembayaran Cicilan</h5>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Pembayaran Hari Ini</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historiCicilan as $h)
                    <tr>
                        <td>{{ $h->kode }}</td>
                        <td>{{ date('d M Y H:i', strtotime($h->tanggal_bayar)) }}</td>
                        <td>{{ $h->pelanggan }}</td>

                        <td class="text-primary">
                            Rp {{ number_format($h->jumlah_bayar,0,',','.') }}
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Belum ada pembayaran cicilan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= MODAL DETAIL ================= -->
<div id="detailModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:9999; align-items:center; justify-content:center;">
    <div id="print-area-detail" style="width:360px; background:#fff; border-radius:16px; overflow:hidden; box-shadow:0 20px 40px rgba(0,0,0,.2);">
        
        <div style="background:#0b1635; color:white; padding:16px; text-align:center;">
            <h4 style="margin:0;">WARUNG SEBLANG</h4>
            <small>POS & Accounting</small>
        </div>

        <div id="detailContent" style="padding:18px; font-size:14px;"></div>

        <div class="no-print" style="display:flex; gap:10px; padding:16px;">
            <button onclick="printDetail()" class="btn btn-primary w-100">Cetak</button>
            <button onclick="closeDetail()" class="btn btn-secondary w-100">Tutup</button>
        </div>
    </div>
</div>
@endsection

<style>
@media print {

    /* sembunyikan semua */
    body * {
        visibility: hidden;
    }

    /* tampilkan hanya struk */
    #print-area-detail, #print-area-detail * {
        visibility: visible;
    }

    /* posisi struk */
    #print-area-detail {
        position: absolute;
        top: 0;
        left: 0;
        width: 300px;
    }

    /* sembunyikan tombol */
    .no-print {
        display: none !important;
    }
}
</style>
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker"></script>

<script>
$(function () {
    $('#tanggal').daterangepicker({
        autoUpdateInput: false,
        locale: {
            format: 'YYYY-MM-DD',
            cancelLabel: 'Clear'
        }
    });

    $('#tanggal').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(
            picker.startDate.format('YYYY-MM-DD') +
            ' - ' +
            picker.endDate.format('YYYY-MM-DD')
        );
    });
});

// ================= DETAIL =================
function showDetail(data) {

    let html = `
        <div style="text-align:center;font-weight:bold;margin-bottom:10px;">
            DETAIL TRANSAKSI
        </div>

        <div><b>Invoice:</b> ${data.kode_transaksi ?? '-'}</div>
        <div><b>Tanggal Transaksi:</b> ${moment(data.tanggal).format('DD MMM YYYY HH:mm')}</div>
        <div><b>Pelanggan:</b> ${data.pelanggan_rel?.nama_pelanggan || data.nama_pelanggan || '-'}</div>
        <div><b>Kasir:</b> ${data.karyawan_rel?.nama_karyawan || data.user_rel?.name || '-'}</div>

        <hr>

        <div><b>Kategori:</b> ${data.kategori_rel?.nama_kategori || '-'}</div>
        <div><b>Tipe Acara:</b> ${data.tipe_rel?.nama_tipe || '-'}</div>

        <div><b>Tanggal Acara:</b> ${data.tanggal_acara ? moment(data.tanggal_acara).format('DD MMM YYYY') : '-'}</div>
        <div><b>Jam Acara:</b> ${data.jam_acara || '-'}</div>

        <hr>
        <b>Detail Item:</b><br>
    `;

    // ================= RESTO =================
    if (data.details && data.details.length > 0) {
        data.details.forEach(item => {
            const nama = item.barang?.nama_barang ?? '-';

            html += `
                <div style="display:flex;justify-content:space-between;">
                    <span>${nama} x${item.qty}</span>
                    <span>Rp ${parseInt(item.harga).toLocaleString('id-ID')}</span>
                </div>
            `;
        });
    }

    // ================= WEDDING =================
    if (data.detail_layanan && data.detail_layanan.length > 0) {
        data.detail_layanan.forEach(item => {
            const nama = item.layanan?.nama_layanan ?? 'Paket Wedding';

            html += `
                <div style="display:flex;justify-content:space-between;">
                    <span>${nama}</span>
                    <span>Rp ${parseInt(item.harga).toLocaleString('id-ID')}</span>
                </div>
            `;
        });
    }

    html += `
        <hr>

        <div><b>Keterangan:</b> ${data.keterangan || '-'}</div>

        <div><b>Status:</b> ${data.status}</div>

        <div style="display:flex;justify-content:space-between;font-weight:bold;margin-top:10px;">
            <span>Total</span>
            <span>Rp ${parseInt(data.total).toLocaleString('id-ID')}</span>
        </div>

        <div style="text-align:center;margin-top:10px;">
            -----------------------
            <br>
            Terima kasih 🙏
        </div>
    `;

    document.getElementById('detailContent').innerHTML = html;
    document.getElementById('detailModal').style.display = 'flex';
}

function closeDetail() {
    document.getElementById('detailModal').style.display = 'none';
}

function printDetail() {
    window.print();
}

function printStrukRiwayat(data) {
    showDetail(data);
    setTimeout(() => window.print(), 300);
}

//pesanan
document.getElementById('pending_order')?.addEventListener('change', function(){
    const opt = this.selectedOptions[0];
    document.getElementById('pending_total').value =
        'Rp ' + Number(opt.dataset.total).toLocaleString();
});


function bayarPending(id){
    const bayar = parseInt(document.getElementById('pending_bayar').value || 0);
    const metode = document.getElementById('pending_metode').value;

    fetch(`/kasir/pesanan/${id}/bayar`, {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
            jumlah_bayar: bayar,
            metode_bayar: metode
        })
    })
    .then(res=>res.json())
    .then(data=>{
        if(data.success){
            showPopup(
                data.kode,
                data.total
            );
            setTimeout(()=>location.reload(),500);
        }else{
            alert(data.message);
        }
    });
}

@if(session('print_trx'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trxId = @json(session('print_trx'));

    const trx = @json($penjualan->firstWhere('id_penjualan', session('print_trx')));

    if (trx) {
        showDetail(trx);
    }
});
</script>
@endif

</script>
@endpush