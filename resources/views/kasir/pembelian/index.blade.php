@extends('layouts.kasir')

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm">
<i class="fas fa-check-circle mr-2"></i>
{{ session('success') }}
<button type="button" class="close" data-dismiss="alert">
<span>&times;</span>
</button>
</div>
@endif

<div class="row">

{{-- ================= FORM PEMBELIAN ================= --}}
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

<form method="POST"
action="{{ route('kasir.pembelian.store') }}"
id="formPembelian">

@csrf

<div class="row mb-4">

<div class="col-md-6">
<label class="font-weight-semibold">Pilih Supplier</label>

<select name="id_supplier"
class="form-control"
id="supplier"
required>

<option value="">-- Pilih Supplier --</option>

@foreach($supplier as $s)

<option value="{{ $s->id_supplier }}">
{{ $s->nama_supplier }}
</option>

@endforeach

</select>
</div>

<div class="col-md-6">
<label class="font-weight-semibold">Tanggal</label>

<input type="text"
class="form-control bg-light"
value="{{ now()->format('d-m-Y') }}"
readonly>

</div>

</div>


<div class="row">

<div class="col-md-5">

<label class="font-weight-semibold">Bahan</label>

<select name="id_bahan"
class="form-control"
id="bahan"
required>

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

<input type="number"
name="qty"
id="qty"
class="form-control text-center"
min="1"
required>

</div>


<div class="col-md-3">

<label class="font-weight-semibold">Harga</label>

<input type="number"
name="harga"
id="harga"
class="form-control text-right"
min="0"
required>

</div>

</div>

</form>

</div>
</div>

</div>



{{-- ================= RINGKASAN ================= --}}
<div class="col-md-4">

<div class="card border-0 shadow-sm"
style="border-radius:16px;">

<div class="card-body p-4">

<h6 class="font-weight-bold mb-4">

<i class="fas fa-receipt mr-2"></i>
Ringkasan Pembelian

</h6>


<div class="d-flex justify-content-between mb-2">

<span class="text-muted">Subtotal</span>

<span id="subtotal-text">Rp 0</span>

</div>


<div class="d-flex justify-content-between mb-2">

<span class="text-muted">Total</span>

<span id="total-text">Rp 0</span>

</div>

<hr>


<div class="mb-3">

<label class="font-weight-semibold">Metode Bayar</label>

<select name="metode_bayar"
id="metode_bayar"
class="form-control"
form="formPembelian"
required>

<option value="">-- Pilih --</option>

<option value="tunai">Tunai</option>

<option value="kredit">Kredit</option>

</select>

</div>


<div id="info-kredit"
class="alert alert-warning d-none">

Pembelian kredit akan otomatis tercatat sebagai hutang supplier.

</div>



<div class="mt-4">

<label class="font-weight-semibold">
Jumlah Uang Diterima
</label>

<div class="form-control bg-light text-right font-weight-bold"
id="total-besar"
style="font-size:18px;">

Rp 0

</div>

</div>



<button type="submit"
form="formPembelian"
class="btn btn-success btn-block mt-4 shadow-sm"
style="border-radius:12px;">

<i class="fas fa-save mr-1"></i>
Simpan Transaksi

</button>



<button type="button"
onclick="showStruk()"
class="btn btn-light btn-block mt-2 shadow-sm"
style="border-radius:12px;">

<i class="fas fa-print mr-1"></i>
Cetak Struk

</button>


</div>
</div>

</div>

</div>



{{-- ================= POPUP STRUK ================= --}}

<div id="popup-struk" class="popup-overlay">

<div class="popup-card" id="print-area">

<div class="popup-header">

<h2>Warung Seblang</h2>
<small>Resto & Cafe</small>

</div>


<div class="popup-body">

<h3>STRUK PEMBELIAN</h3>


<div class="info-row">
<span>No Transaksi</span>
<span id="p-kode"></span>
</div>

<div class="info-row">
<span>Tanggal</span>
<span id="p-tanggal"></span>
</div>

<div class="info-row">
<span>Kasir</span>
<span>{{ Auth::user()->name }}</span>
</div>

<div class="info-row">
<span>Supplier</span>
<span id="p-supplier"></span>
</div>

<hr>

<div id="p-items"></div>

<hr>

<div class="info-row">
<span>Total</span>
<span id="p-total"></span>
</div>

<div class="info-row">
<span>Metode</span>
<span id="p-metode"></span>
</div>

<p class="thanks">
Terima kasih 🙏
</p>

</div>


<div class="popup-footer no-print">

<button class="btn-print"
onclick="printStruk()">

Cetak Struk

</button>

<button class="btn-close"
onclick="closeStruk()">

Tutup

</button>

</div>

</div>

</div>



<style>

.popup-overlay{
position:fixed;
inset:0;
background:rgba(0,0,0,0.5);
display:none;
justify-content:center;
align-items:center;
z-index:9999;
}

.popup-card{
width:360px;
background:white;
border-radius:14px;
overflow:hidden;
box-shadow:0 10px 30px rgba(0,0,0,0.2);
font-size:13px;
}

.popup-header{
background:#0f172a;
color:white;
padding:16px;
text-align:center;
}

.popup-body{
padding:16px;
}

.info-row{
display:flex;
justify-content:space-between;
margin-bottom:6px;
}

.popup-footer{
display:flex;
gap:10px;
padding:12px;
}

.btn-print{
flex:1;
background:#2563eb;
color:white;
border:none;
padding:8px;
border-radius:8px;
}

.btn-close{
flex:1;
background:#e5e7eb;
border:none;
padding:8px;
border-radius:8px;
}

</style>



<script>

let subtotal = 0;

document.addEventListener('input', function(){

let qty = parseFloat(document.getElementById('qty').value) || 0;

let harga = parseFloat(document.getElementById('harga').value) || 0;

subtotal = qty * harga;

let format = subtotal
? new Intl.NumberFormat('id-ID',{
style:'currency',
currency:'IDR'
}).format(subtotal)
: 'Rp 0';

document.getElementById('subtotal-text').innerText = format;
document.getElementById('total-text').innerText = format;
document.getElementById('total-besar').innerText = format;

});



document.getElementById('metode_bayar')
.addEventListener('change',function(){

let info = document.getElementById('info-kredit');

if(this.value === 'kredit'){

info.classList.remove('d-none');

}else{

info.classList.add('d-none');

}

});



function showStruk(){

let supplier = document.getElementById('supplier');
let bahan = document.getElementById('bahan');
let qty = document.getElementById('qty').value;
let harga = document.getElementById('harga').value;
let metode = document.getElementById('metode_bayar').value;

let namaSupplier = supplier.options[supplier.selectedIndex].text;
let namaBahan = bahan.options[bahan.selectedIndex].text;

document.getElementById('p-kode').innerText = "PB-" + Date.now();
document.getElementById('p-tanggal').innerText = new Date().toLocaleString();
document.getElementById('p-supplier').innerText = namaSupplier;

document.getElementById('p-metode').innerText = metode;

document.getElementById('p-total').innerText =
"Rp " + subtotal.toLocaleString();

document.getElementById('p-items').innerHTML =

`<div class="info-row">
<span>${namaBahan} x${qty}</span>
<span>Rp ${(qty*harga).toLocaleString()}</span>
</div>`;

document.getElementById('popup-struk').style.display='flex';

}



function closeStruk(){

document.getElementById('popup-struk').style.display='none';

}



function printStruk(){

window.print();

}

</script>

@endsection