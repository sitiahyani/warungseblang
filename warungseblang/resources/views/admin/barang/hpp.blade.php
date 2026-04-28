@extends('layouts.admin')

@section('content')

<div class="container">

    <div class="card shadow">
        <div class="card-header">
            <h5>
                Kelola Resep & Hitung HPP
                <small class="text-muted">
                    - {{ $barang->nama_barang }}
                </small>
            </h5>
        </div>

        <div class="card-body">

            <form action="{{ route('barang.simpanResep',$barang->id_barang) }}"
                  method="POST">
                @csrf

                {{-- ================= RESEP ================= --}}
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Bahan</th>
                            <th width="15%">Harga</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>
                            <th width="5%">#</th>
                        </tr>
                    </thead>

                    <tbody id="resepWrapper">

                        @foreach($barang->resep ?? [] as $r)
                        <tr>
                            <td>
                                <select name="bahan_id[]"
                                        class="form-control bahan"
                                        onchange="hitung()">

                                    @foreach($bahan_baku as $b)
                                        <option value="{{ $b->id_bahan }}"
                                                data-harga="{{ $b->harga_per_satuan }}"
                                                {{ $r->id_bahan == $b->id_bahan ? 'selected' : '' }}>
                                            {{ $b->nama_bahan }}
                                        </option>
                                    @endforeach

                                </select>
                            </td>

                            <td class="harga text-center"></td>

                            <td>
                                <input type="number"
                                       name="qty[]"
                                       class="form-control qty"
                                       value="{{ $r->qty }}"
                                       min="1"
                                       onchange="hitung()">
                            </td>

                            <td class="subtotal text-center"></td>

                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-danger"
                                        onclick="hapusBaris(this)">
                                    x
                                </button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

                <button type="button"
                        class="btn btn-secondary btn-sm mb-3"
                        onclick="tambahBahan()">
                    + Tambah Bahan
                </button>

                <hr>

                {{-- ================= HPP ================= --}}
                <div class="row">

                    <div class="col-md-4">
                        <label>Total HPP</label>
                        <input type="text"
                               id="totalDisplay"
                               class="form-control"
                               readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Harga Jual</label>
                        <input type="text"
                               class="form-control"
                               value="Rp {{ number_format($barang->harga_jual ?? 0,0,',','.') }}"
                               readonly>
                    </div>

                    <div class="col-md-4">
                        <label>Margin</label>
                        <input type="text"
                               id="marginDisplay"
                               class="form-control"
                               readonly>
                    </div>

                </div>

                <input type="hidden" name="hpp" id="hpp" value="0">

                <div class="mt-4">
                    <button class="btn btn-primary">
                        Simpan
                    </button>

                    <a href="{{ route('barang.index') }}"
                       class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>
</div>


<script>

function tambahBahan(){

    let wrapper = document.getElementById('resepWrapper');

    wrapper.innerHTML += `
        <tr>
            <td>
                <select name="bahan_id[]"
                        class="form-control bahan"
                        onchange="hitung()">

                    @foreach($bahan_baku as $b)
                        <option value="{{ $b->id_bahan }}"
                                data-harga="{{ $b->harga_per_satuan }}">
                            {{ $b->nama_bahan }}
                        </option>
                    @endforeach

                </select>
            </td>

            <td class="harga text-center"></td>

            <td>
                <input type="number"
                       name="qty[]"
                       class="form-control qty"
                       value="1"
                       min="1"
                       onchange="hitung()">
            </td>

            <td class="subtotal text-center"></td>

            <td>
                <button type="button"
                        class="btn btn-sm btn-danger"
                        onclick="hapusBaris(this)">
                    x
                </button>
            </td>
        </tr>
    `;

    hitung();
}

function hapusBaris(btn){
    btn.closest('tr').remove();
    hitung();
}

function hitung(){

    let total = 0;

    document.querySelectorAll('#resepWrapper tr')
        .forEach(function(row){

            let select = row.querySelector('.bahan');

            let harga = parseFloat(
                select.options[select.selectedIndex].dataset.harga
            ) || 0;

            let qty = parseFloat(
                row.querySelector('.qty').value
            ) || 0;

            let subtotal = harga * qty;

            row.querySelector('.harga').innerText =
                "Rp " + harga.toLocaleString('id-ID');

            row.querySelector('.subtotal').innerText =
                "Rp " + subtotal.toLocaleString('id-ID');

            total += subtotal;
        });

    let hargaJual = {{ $barang->harga_jual ?? 0 }};
    let margin = hargaJual - total;

    document.getElementById('totalDisplay').value =
        "Rp " + total.toLocaleString('id-ID');

    document.getElementById('marginDisplay').value =
        "Rp " + margin.toLocaleString('id-ID');

    document.getElementById('hpp').value = total;
}

document.addEventListener("DOMContentLoaded", function(){
    hitung();
});

</script>

@endsection
