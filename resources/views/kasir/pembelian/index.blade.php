@extends('layouts.kasir')

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    </div>
@endif

<div class="row">

    {{-- ================= LEFT : FORM PEMBELIAN ================= --}}
    <div class="col-md-8">
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-body p-4">

                <h5 class="font-weight-bold mb-1">
                    Transaksi Pembelian Supplier
                </h5>
                <small class="text-muted">
                    Kelola pembelian dan pembayaran supplier
                </small>

                <hr class="my-4">

                <form method="POST" action="{{ route('kasir.pembelian.store') }}" id="formPembelian">
                @csrf

                {{-- Supplier & Tanggal --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="font-weight-semibold">Pilih Supplier</label>
                        <select name="id_supplier" class="form-control" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($supplier as $s)
                                <option value="{{ $s->id_supplier }}">
                                    {{ $s->nama_supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="font-weight-semibold">Tanggal Pembelian</label>
                        <input type="text"
                               class="form-control bg-light"
                               value="{{ now()->format('d-m-Y') }}"
                               readonly>
                    </div>
                </div>

                {{-- Bahan --}}
                <div class="row">
                    <div class="col-md-5">
                        <label class="font-weight-semibold">Bahan</label>
                        <select name="id_bahan" class="form-control" required>
                            <option value="">-- Pilih Bahan --</option>
                            @foreach($bahan as $b)
                                <option value="{{ $b->id_bahan }}">
                                    {{ $b->nama_bahan }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="font-weight-semibold">Qty</label>
                        <input type="number" name="qty" id="qty"
                               class="form-control text-center"
                               min="1" required>
                    </div>

                    <div class="col-md-3">
                        <label class="font-weight-semibold">Harga</label>
                        <input type="number" name="harga" id="harga"
                               class="form-control text-right"
                               min="0" required>
                    </div>

                    {{-- KOLOM TOMBOL DIHAPUS --}}
                    <div class="col-md-2"></div>
                </div>

                </form>

            </div>
        </div>
    </div>

    {{-- ================= RIGHT : RINGKASAN ================= --}}
    <div class="col-md-4">
        <div class="card border-0 shadow-sm" style="border-radius:16px;">
            <div class="card-body p-4">

                <h6 class="font-weight-bold mb-4">
                    <i class="fas fa-receipt mr-2"></i>
                    Ringkasan Pembelian
                </h6>

                {{-- Subtotal --}}
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal</span>
                    <span id="subtotal-text">Rp 0</span>
                </div>

                {{-- Total --}}
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total</span>
                    <span id="total-text">Rp 0</span>
                </div>

                <hr>

                {{-- Metode Bayar --}}
                <div class="mb-3">
                    <label class="font-weight-semibold">Metode Bayar</label>
                    <select name="metode_bayar" id="metode_bayar"
                            class="form-control" form="formPembelian" required>
                        <option value="">-- Pilih --</option>
                        <option value="tunai">Tunai</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>

                <div id="info-kredit" class="alert alert-warning d-none">
                    Pembelian kredit akan otomatis tercatat sebagai hutang supplier.
                </div>

                {{-- Total Besar --}}
                <div class="mt-4">
                    <label class="font-weight-semibold">Jumlah Uang Diterima</label>
                    <div class="form-control bg-light text-right font-weight-bold"
                         id="total-besar"
                         style="font-size:18px;">
                        Rp 0
                    </div>
                </div>

                {{-- Tombol --}}
                <button type="submit"
                        form="formPembelian"
                        class="btn btn-success btn-block mt-4 shadow-sm"
                        style="border-radius:12px;">
                    <i class="fas fa-save mr-1"></i> Simpan Transaksi
                </button>

                <button type="button"
                        onclick="window.print()"
                        class="btn btn-light btn-block mt-2 shadow-sm"
                        style="border-radius:12px;">
                    <i class="fas fa-print mr-1"></i> Cetak Struk
                </button>

            </div>
        </div>
    </div>

</div>


{{-- ================= AUTO HITUNG ================= --}}
<script>
document.addEventListener('input', function() {

    let qty = parseFloat(document.getElementById('qty').value) || 0;
    let harga = parseFloat(document.getElementById('harga').value) || 0;
    let subtotal = qty * harga;

    let formatRupiah = subtotal ?
        new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(subtotal)
        : 'Rp 0';

    document.getElementById('subtotal-text').innerText = formatRupiah;
    document.getElementById('total-text').innerText = formatRupiah;
    document.getElementById('total-besar').innerText = formatRupiah;
});

document.getElementById('metode_bayar').addEventListener('change', function(){
    let info = document.getElementById('info-kredit');
    if(this.value === 'kredit'){
        info.classList.remove('d-none');
    } else {
        info.classList.add('d-none');
    }
});
</script>

@endsection