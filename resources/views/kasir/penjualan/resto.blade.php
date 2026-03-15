@extends('layouts.kasir')

@section('content')
<div class="pos-wrapper">
<main class="main-content">

<!-- HEADER -->
    <div class="page-header">
        <div>
            <h1>Transaksi Penjualan Resto</h1>
            <p>Kelola pesanan dan penjualan resto</p>
        </div>

        <div class="user-box">
            <div class="avatar">{{ substr(Auth::user()->name ?? 'K',0,1) }}</div>
            <div>
                <strong>{{ Auth::user()->name ?? 'Kasir 1' }}</strong>
                <small>Shift Aktif (08.00 - 22.00)</small>
            </div>
        </div>
    </div>

    <hr class="section-divider">

<!-- FORM -->
    <div class="top-form">
        <input type="text" id="customer_name" placeholder="Nama pelanggan">
        <input type="text" id="order_note" placeholder="Catatan pesanan (opsional)">
    </div>
    <div class="content-grid">

<!-- MENU AREA -->
<div>
    <div class="search-wrapper-row">

    <div class="search-wrapper">
        <i class="fas fa-search"></i>
        <input type="text" id="search" placeholder="Cari menu...">
        </div>

        <button class="btn-pelanggan" onclick="openCustomerPopup()">
            + Pelanggan
        </button>

    </div>

            <div class="menu-grid">
                @forelse($barang as $item)
                    @php $habis = $item->stok <= 0; @endphp
                    <div class="menu-card {{ $habis ? 'habis' : '' }}"
                         data-id="{{ $item->id_barang }}"
                         data-name="{{ $item->nama_barang }}"
                         data-price="{{ $item->harga_jual }}">

                        <div class="img-placeholder"></div>

                        <h4>{{ $item->nama_barang }}</h4>
                        <div class="price">
                            Rp {{ number_format($item->harga_jual,0,',','.') }}
                        </div>
                        <small>Stok {{ $item->stok }}</small>

                        @if($habis)
                            <span class="badge-habis">HABIS</span>
                        @else
                            <button class="btn-add">Tambah</button>
                        @endif
                    </div>
                @empty
                    <p>Menu kosong.</p>
                @endforelse
            </div>
</div>

<!-- CART -->
    <div class="cart-box">
            <div class="cart-header">
                <span>🛒 Keranjang</span>
                <button class="btn-clear" onclick="clearCart()">Kosongkan</button>
            </div>
            
            <div id="customer-info" style="margin-bottom:8px;font-size:12px;color:#6b7280;"></div>
            <div class="cart-items" id="cart-items">
                <p class="empty">Keranjang kosong</p>
            </div>

            <!-- SUMMARY -->
            <div class="cart-summary">
                <div>
                    <span>Subtotal</span>
                    <span id="subtotal">Rp 0</span>
                </div>

                <div>
                    <span>
                        Pajak Restoran
                        @if($pajak)
                            @if($pajak->tipe_pajak == 'persen')
                                ({{ $pajak->nilai_pajak }}%)
                            @else
                                (Rp {{ number_format($pajak->nilai_pajak,0,',','.') }})
                            @endif
                        @else
                            (0)
                        @endif
                    </span>
                    <span id="tax">Rp 0</span>
                </div>

                <div class="total">
                    <span>Total</span>
                    <span id="total">Rp 0</span>
                </div>
                
                <div class="payment-method">
                <label>Metode Pembayaran</label>

                <select id="payment_method">
                    <option value="tunai">Tunai</option>
                    <option value="non_tunai">Non Tunai</option>
                </select>
            </div>
            </div>

            <input type="number" id="payment" placeholder="Masukkan jumlah uang">

            <div class="change-box">
                Kembali: <span id="change">Rp 0</span>
            </div>

            <button class="btn-pay" onclick="checkout()">Simpan</button>
        </div>

    </div>
</main>
</div>

<div id="popup" class="popup-overlay">
    <div class="popup-card" id="print-area">

        <div class="popup-header">
            <h2>Warung Seblang</h2>
            <small>Resto & Cafe</small>
        </div>

        <div class="popup-body">

            <h3>STRUK PEMBAYARAN</h3>

            <div class="info-row">
                <span>No. Transaksi</span>
                <span id="struk-kode"></span>
            </div>
            <div class="info-row">
                <span>Tanggal</span>
                <span id="struk-tanggal"></span>
            </div>
            <div class="info-row">
                <span>Kasir</span>
                <span>{{ Auth::user()->name }}</span>
            </div>

            <hr>

            <div id="struk-items"></div>

            <hr>

            <div class="info-row">
                <span>Subtotal</span>
                <span id="struk-subtotal"></span>
            </div>
            <div class="info-row">
                <span id="struk-pajak-label">Pajak</span>
                <span id="struk-pajak"></span>
            </div>

            <div class="info-row total-row">
                <span>TOTAL</span>
                <span id="struk-total"></span>
            </div>

            <div class="info-row">
                <span>Bayar</span>
                <span id="struk-bayar"></span>
            </div>
            <div class="info-row">
                <span>Kembali</span>
                <span id="struk-kembali"></span>
            </div>

            <p class="thanks">Terima kasih atas kunjungan Anda!</p>

        </div>

        <div class="popup-footer no-print">
            <button class="btn-print" onclick="printStruk()">Cetak Struk</button>
            <button class="btn-close" onclick="closePopup()">Tutup</button>
        </div>

    </div>
</div>

<div id="customer-popup" class="popup-overlay">

<div class="popup-card customer-card">

    <div class="popup-header">
        <h2>Tambah Pelanggan</h2>
    </div>

    <div class="popup-body">

        <input type="text" id="nama_pelanggan" placeholder="Nama pelanggan">

        <input type="text" id="no_hp" placeholder="Nomor HP">

        <textarea id="alamat" placeholder="Alamat"></textarea>

    </div>

    <div class="popup-footer">
        <button class="btn-print" onclick="saveCustomer()">Simpan</button>
        <button class="btn-close" onclick="closeCustomerPopup()">Batal</button>
    </div>

</div>
</div>

<style>
body{margin:0;font-family:'Inter',sans-serif;background:#f4f6f9;}
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
.page-header h1{font-size:22px;margin:0;}
.page-header p{font-size:13px;color:#6b7280;margin:3px 0 0;}
.user-box{background:white;padding:8px 16px;border-radius:40px;display:flex;gap:10px;align-items:center;box-shadow:0 3px 10px rgba(0,0,0,0.05);}
.avatar{width:36px;height:36px;border-radius:50%;background:#3b82f6;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;}
.section-divider{border:none;border-top:1px solid #e5e7eb;margin:15px 0 18px;}
.top-form{display:flex;gap:14px;margin-bottom:18px;}
.top-form input{flex:1;padding:10px 14px;border-radius:12px;border:1px solid #d1d5db;font-size:14px;}
.content-grid{display:grid;grid-template-columns:1fr 340px;gap:20px;}
.search-wrapper{position:relative;margin-bottom:15px;}
.search-wrapper i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:13px;}
.search-wrapper input{height:38px; width:100%;padding:9px 12px 9px 34px;border-radius:12px;border:1px solid #d1d5db;font-size:14px;}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;}
.menu-card{background:white;padding:14px;border-radius:16px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.05);transition:.2s;position:relative;}
.menu-card:hover{transform:translateY(-4px);}
.img-placeholder{height:80px;background:#eef2f7;border-radius:10px;margin-bottom:8px;}
.menu-card h4{font-size:14px;margin-bottom:4px;}
.price{color:#2563eb;font-weight:600;margin-bottom:3px;}
.btn-add{margin-top:6px;width:100%;padding:6px;border:none;border-radius:14px;background:#2563eb;color:white;font-size:12px;}
.badge-habis{position:absolute;top:8px;right:8px;background:#ef4444;color:white;padding:3px 8px;border-radius:20px;font-size:10px;}
.cart-box{background:white;padding:18px;border-radius:18px;box-shadow:0 6px 18px rgba(0,0,0,0.06);position:sticky;top:20px;font-size:13px;}
.cart-header{display:flex;justify-content:space-between;font-weight:600;margin-bottom:10px;}
.btn-clear{border:none;background:#f3f4f6;padding:4px 10px;border-radius:14px;font-size:12px;}
.cart-item{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;padding:6px 0;border-bottom:1px solid #f1f1f1;}
.qty-control{display:flex;align-items:center;gap:6px;}
.qty-btn{width:22px;height:22px;border:none;border-radius:50%;background:#e5e7eb;font-weight:bold;}
.cart-summary div{display:flex;justify-content:space-between;margin-bottom:4px;}
.total{border-top:1px dashed #d1d5db;padding-top:6px;font-weight:600;}
input[type="number"]{width:100%;padding:8px;border-radius:10px;border:1px solid #d1d5db;margin-top:6px;font-size:13px;}
.change-box{margin-top:6px;font-weight:600;color:#16a34a;}
.btn-pay{margin-top:10px;width:100%;padding:10px;border:none;border-radius:20px;background:#16a34a;color:white;font-size:14px;}
.search-wrapper-row{
display:flex;
gap:10px;
margin-bottom:15px;
}

.search-wrapper-row .search-wrapper{
flex:1;
}

.btn-pelanggan{
height:38px;
padding:0 14px;
border:none;
border-radius:12px;
background:#2563eb;
color:white;
font-size:13px;
display:flex;
align-items:center;
justify-content:center;
cursor:pointer;
white-space:nowrap;
}

.customer-card input,
.customer-card textarea{
width:100%;
padding:10px;
border-radius:10px;
border:1px solid #d1d5db;
margin-bottom:10px;
font-size:13px;
}

.customer-card textarea{
height:70px;
resize:none;
}

.payment-method{
margin-top:10px;
display:flex;
flex-direction:column;
gap:4px;
font-size:13px;
}

.payment-method select{
padding:8px;
border-radius:10px;
border:1px solid #d1d5db;
font-size:13px;
background:white;
}

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

.popup-header h2{
    margin:0;
    font-size:18px;
}

.popup-body{
    padding:16px;
}

.popup-body h3{
    text-align:center;
    margin-bottom:12px;
}

.info-row{
    display:flex;
    justify-content:space-between;
    margin-bottom:6px;
}

.total-row{
    font-weight:bold;
    font-size:14px;
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

.thanks{
    text-align:center;
    margin-top:14px;
    font-size:12px;
    color:#6b7280;
}
@media print {
    body *{
        visibility:hidden;
    }

    #print-area, #print-area *{
        visibility:visible;
    }

    #print-area{
        position:absolute;
        left:0;
        top:0;
        width:100%;
    }

    .no-print{
        display:none;
    }
}
</style>


<script>
const TAX_TYPE = "{{ $pajak ? strtolower($pajak->tipe_pajak) : '' }}";
const TAX_VALUE = {{ $pajak ? $pajak->nilai_pajak : 0 }};
const TAX_LABEL = `Pajak Restoran ${
    TAX_TYPE === 'persen'
        ? '(' + TAX_VALUE + '%)'
        : TAX_TYPE === 'nominal'
            ? '(Rp ' + TAX_VALUE.toLocaleString() + ')'
            : ''
}`;
let cart = {};
let lastTransaction = null;

document.querySelectorAll('.btn-add').forEach(btn=>{
    btn.addEventListener('click',function(){
        const card=this.closest('.menu-card');
        const id=card.dataset.id;
        const name=card.dataset.name;
        const price=parseInt(card.dataset.price);

        if(!cart[id]) cart[id]={name,price,qty:0};
        cart[id].qty++;
        renderCart();
    });
});

function increaseQty(id){cart[id].qty++;renderCart();}
function decreaseQty(id){cart[id].qty--;if(cart[id].qty<=0) delete cart[id];renderCart();}
function clearCart(){
    cart={};
    document.getElementById('customer_name').value='';
    document.getElementById('order_note').value='';
    renderCart();
}

function renderCart(){
    const container=document.getElementById('cart-items');
    const customerInfo=document.getElementById('customer-info');

    let subtotal=0,html='';

    for(let id in cart){
        let item=cart[id];
        let totalItem=item.price*item.qty;
        subtotal+=totalItem;

        html+=`
        <div class="cart-item">
            <div>
                <div>${item.name}</div>
                <div>Rp ${totalItem.toLocaleString()}</div>
            </div>
            <div class="qty-control">
                <button class="qty-btn" onclick="decreaseQty('${id}')">-</button>
                <span>${item.qty}</span>
                <button class="qty-btn" onclick="increaseQty('${id}')">+</button>
            </div>
        </div>`;
    }

    container.innerHTML=html||'<p class="empty">Keranjang kosong</p>';

    // 🔥 Ambil nama & catatan
    const name=document.getElementById('customer_name').value;
    const note=document.getElementById('order_note').value;

    let infoHTML='';
    if(name) infoHTML += `<div><strong>Pelanggan:</strong> ${name}</div>`;
    if(note) infoHTML += `<div><strong>Catatan:</strong> ${note}</div>`;

    customerInfo.innerHTML = infoHTML;

    let tax = 0;

    if(TAX_TYPE==='persen'){
        tax=Math.round((subtotal*TAX_VALUE)/100);
    }else if(TAX_TYPE==='nominal'){
        tax=TAX_VALUE;
    }

    const total=subtotal+tax;

    document.getElementById('subtotal').innerText='Rp '+subtotal.toLocaleString();
    document.getElementById('tax').innerText='Rp '+tax.toLocaleString();
    document.getElementById('total').innerText='Rp '+total.toLocaleString();

    updateChange(total);
}

document.getElementById('payment').addEventListener('input',function(){
    const total=parseInt(document.getElementById('total').innerText.replace(/\D/g,''))||0;
    updateChange(total);
});

function updateChange(total){
    const pay=parseInt(document.getElementById('payment').value)||0;
    const change=pay-total;
    document.getElementById('change').innerText=
        change>0?'Rp '+change.toLocaleString():'Rp 0';
}

function openCustomerPopup(){
document.getElementById('customer-popup').style.display='flex';
}

function closeCustomerPopup(){
document.getElementById('customer-popup').style.display='none';
}

function saveCustomer(){

let nama = document.getElementById('nama_pelanggan').value;
let hp = document.getElementById('no_hp').value;
let alamat = document.getElementById('alamat').value;

if(nama === ""){
alert("Nama pelanggan wajib diisi");
return;
}

fetch("{{ route('pelanggan.simpan') }}",{

method:'POST',

headers:{
'Content-Type':'application/json',
'Accept':'application/json',
'X-CSRF-TOKEN':'{{ csrf_token() }}'
},

body:JSON.stringify({
nama_pelanggan:nama,
no_hp:hp,
alamat:alamat
})

})

.then(res => res.json())

.then(data => {

if(data.success){

alert("Data pelanggan berhasil ditambahkan");

closeCustomerPopup();

document.getElementById('nama_pelanggan').value='';
document.getElementById('no_hp').value='';
document.getElementById('alamat').value='';

}else{

alert("Error : " + data.message);

}

})

.catch(error => {

console.log(error);
alert("Server error");

});

}

function checkout(){

    if(Object.keys(cart).length === 0){
        alert("Keranjang kosong");
        return;
    }

    const total = parseInt(document.getElementById('total').innerText.replace(/\D/g,''))||0;
    const bayar = parseInt(document.getElementById('payment').value)||0;

    if(bayar < total){
        alert("Uang kurang");
        return;
    }

    let items=[];

    for(let id in cart){

        items.push({
            id:id,
            qty:cart[id].qty,
            harga:cart[id].price
        });

    }

    const data = {

        total: total,
        bayar: bayar,
        metode_bayar: document.getElementById('payment_method').value,
        items: items

    };


    // =========================
    // JIKA OFFLINE
    // =========================

    if(!navigator.onLine){

        saveOfflineTransaction(data);

        alert("Internet offline. Transaksi disimpan lokal.");

        clearCart();

        return;

    }


    // =========================
    // JIKA ONLINE
    // =========================

    fetch("{{ route('penjualan.simpan') }}",{

        method:'POST',

        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },

        body:JSON.stringify(data)

    })

    .then(res=>res.json())

    .then(data=>{

        if(data.status === "success"){

            showPopup(data.kode,data.total);

            clearCart();

        }else{

            alert(data.message);

        }

    });

}

function showPopup(kode,total){

    const subtotal = parseInt(document.getElementById('subtotal').innerText.replace(/\D/g,''))||0;
    const tax = parseInt(document.getElementById('tax').innerText.replace(/\D/g,''))||0;
    const bayar = parseInt(document.getElementById('payment').value)||0;
    const kembali = bayar-total;

    const pelanggan = document.getElementById('customer_name').value;
    const catatan = document.getElementById('order_note').value;

    // 🔥 SIMPAN SEMUA DATA TRANSAKSI
    lastTransaction = {
        kode,
        tanggal: new Date().toLocaleString(),
        pelanggan,
        catatan,
        subtotal,
        tax,
        total,
        bayar,
        kembali,
        items: JSON.parse(JSON.stringify(cart))
    };

    document.getElementById('struk-kode').innerText = kode;
    document.getElementById('struk-tanggal').innerText = new Date().toLocaleString();

    document.getElementById('struk-pajak-label').innerText = TAX_LABEL;
    document.getElementById('struk-subtotal').innerText = 'Rp '+subtotal.toLocaleString();
    document.getElementById('struk-pajak').innerText = 'Rp '+tax.toLocaleString();
    document.getElementById('struk-total').innerText = 'Rp '+total.toLocaleString();
    document.getElementById('struk-bayar').innerText = 'Rp '+bayar.toLocaleString();
    document.getElementById('struk-kembali').innerText = 'Rp '+kembali.toLocaleString();

    // Tambah pelanggan & catatan
    let extraInfo = '';
    if(pelanggan){
        extraInfo += `
            <div class="info-row">
                <span>Pelanggan</span>
                <span>${pelanggan}</span>
            </div>
        `;
    }

    if(catatan){
        extraInfo += `
            <div class="info-row">
                <span>Catatan</span>
                <span>${catatan}</span>
            </div>
        `;
    }

    let itemsHTML = '';

    for(let id in cart){
        let item = cart[id];
        let totalItem = item.price * item.qty;

        itemsHTML += `
            <div class="info-row">
                <span>${item.name} x${item.qty}</span>
                <span>Rp ${totalItem.toLocaleString()}</span>
            </div>
        `;
    }

    document.getElementById('struk-items').innerHTML =
        extraInfo +
        `<hr>` +
        itemsHTML;

    document.getElementById('popup').style.display='flex';
}

function closePopup(){
    document.getElementById('popup').style.display='none';
}

function printStruk(){

    if(!lastTransaction){
        alert("Tidak ada transaksi untuk dicetak");
        return;
    }

    const t = lastTransaction;

    let itemsHTML = '';

    for(let id in t.items){
        let item = t.items[id];
        let totalItem = item.price * item.qty;

        itemsHTML += `
            <div class="row">
                <span>${item.name} x${item.qty}</span>
                <span>${totalItem.toLocaleString()}</span>
            </div>
        `;
    }

    const printWindow = window.open('', '', 'width=420,height=700');

    printWindow.document.write(`
        <html>
        <head>
            <title>Struk</title>
            <style>
            @page {size: 80mm auto; margin: 0;}

            body{
                width:80mm;
                font-family:monospace;
                font-size:13px;
                padding:10px;
            }

                .center{text-align:center;}
                .row{display:flex;justify-content:space-between;margin-bottom:6px;}
                hr{border:none;border-top:1px dashed #000;margin:6px 0;}
                .bold{font-weight:bold;}
            </style>
        </head>
        <body>

            <div class="center bold">WARUNG SEBLANG</div>
            <div class="center">Resto & Cafe</div>
            <hr>

            <div class="row">
                <span>No</span>
                <span>${t.kode}</span>
            </div>

            <div class="row">
                <span>Tanggal</span>
                <span>${t.tanggal}</span>
            </div>

            <div class="row">
                <span>Kasir</span>
                <span>{{ Auth::user()->name }}</span>
            </div>

            ${t.pelanggan ? `
            <div class="row">
                <span>Pelanggan</span>
                <span>${t.pelanggan}</span>
            </div>` : ''}

            ${t.catatan ? `
            <div style="margin-top:4px;">
                Catatan: ${t.catatan}
            </div>` : ''}

            <hr>

            ${itemsHTML}

            <hr>

            <div class="row">
                <span>Subtotal</span>
                <span>${t.subtotal.toLocaleString()}</span>
            </div>

            <div class="row">
                <span>${TAX_LABEL}</span>
                <span>${t.tax.toLocaleString()}</span>
            </div>

            <div class="row bold">
                <span>TOTAL</span>
                <span>${t.total.toLocaleString()}</span>
            </div>

            <div class="row">
                <span>Bayar</span>
                <span>${t.bayar.toLocaleString()}</span>
            </div>

            <div class="row">
                <span>Kembali</span>
                <span>${t.kembali.toLocaleString()}</span>
            </div>

            <hr>
            <div class="center">Terima kasih 🙏</div>

        </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}

document.getElementById('customer_name').addEventListener('input',renderCart);
document.getElementById('order_note').addEventListener('input',renderCart);

// 🔍 FUNGSI PENCARIAN MENU
document.getElementById('search').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const menuCards = document.querySelectorAll('.menu-card');

    menuCards.forEach(card => {
        const name = card.dataset.name.toLowerCase();

        card.style.display = keyword === '' || name.includes(keyword) ? '' : 'none';
    });
});
</script>

@endsection