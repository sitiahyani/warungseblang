@extends('layouts.kasir')

@section('content')

<div class="row">

    {{-- KIRI : INPUT BARANG --}}
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-dark text-white">
                <h5><i class="fas fa-box"></i> Input Pembelian</h5>
            </div>

            <div class="card-body">

                <form method="POST" action="{{ route('kasir.pembelian.store') }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label>Supplier</label>
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
                        <label>Tanggal</label>
                        <input type="text"
                               class="form-control"
                               value="{{ now()->format('d-m-Y') }}"
                               readonly>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-5">
                        <label>Bahan</label>
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
                        <label>Qty</label>
                        <input type="number" name="qty" id="qty" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label>Harga</label>
                        <input type="number" name="harga" id="harga" class="form-control" required>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-success w-100">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- KANAN : RINGKASAN --}}
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-receipt"></i> Ringkasan</h5>
            </div>
            <div class="card-body">

                <div class="mb-3">
                    <label>Subtotal</label>
                    <input type="text" id="subtotal"
                           class="form-control text-right"
                           readonly>
                </div>

                <div class="mb-3">
                    <label>Metode Bayar</label>
                    <select name="metode_bayar" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="tunai">Tunai</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- AUTO HITUNG --}}
<script>
document.addEventListener('input', function() {
    let qty = document.getElementById('qty').value;
    let harga = document.getElementById('harga').value;
    let subtotal = qty * harga;

    document.getElementById('subtotal').value =
        subtotal ? subtotal.toLocaleString('id-ID') : '';
});
</script>

@endsection
