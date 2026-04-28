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

            <!-- PROFILE -->
            @php
                $color = '#000000'; // default hitam
                if(isset($shift) && $shift){
                    $now = now()->format('H:i:s');
                    $selesai = $shift->waktu_selesai;
                    if($now > $selesai){
                        $color = '#ef4444'; // merah kalau sudah lewat
                    }
                }
            @endphp

            <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                {{-- AVATAR --}}
                @if($karyawan && $karyawan->foto)
                    <img src="{{ asset('storage/'.$karyawan->foto) }}"
                        style="width:40px;height:40px;border-radius:50%;object-fit:cover;"
                        class="me-2">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                        style="width:40px;height:40px;">
                        {{ strtoupper(substr($karyawan->nama_karyawan ?? 'K', 0, 1)) }}
                    </div>
                @endif

                {{-- INFO --}}
                <div class="lh-sm">
                    <div class="fw-semibold">
                        {{ $karyawan->nama_karyawan ?? 'Kasir' }}
                    </div>

                    <small style="color: {{ $color }}">
                        @if($shift)
                            Shift 
                                {{ \Carbon\Carbon::parse($shift->waktu_mulai)->format('H.i') }}
                                -
                                {{ \Carbon\Carbon::parse($shift->waktu_selesai)->format('H.i') }}
                        @else
                            Tidak ada shift
                        @endif
                    </small>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        <!-- FORM -->
        <div class="top-form">
            <div class="customer-wrapper">
                <input type="hidden" id="id_pelanggan">

                <input type="text"
                    id="customer_name"
                    placeholder="Nama pelanggan"
                    autocomplete="off">

                <div id="customer_suggestions" class="customer-suggestions"></div>
            </div>
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

                        <div class="menu-image">
                            <img src="{{ $item->gambar 
                                ? asset('storage/'.$item->gambar) 
                                : asset('img/no-image.png') }}"
                                alt="{{ $item->nama_barang }}">
                        </div>

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
                    <label>Pilih Diskon</label>
                    <select id="diskon_select">
                        <option value="">Tanpa Diskon</option>

                        @foreach($diskon as $d)

                        @php
                            // ================= HITUNG SISA =================
                            if($d->masa_aktif_tipe == 'pesanan'){
                                $sisa = max($d->masa_aktif_nilai - $d->penjualan_count, 0);
                            }elseif($d->masa_aktif_tipe == 'hari' && $d->tanggal_selesai){
                                $sisa = now()->startOfDay()->diffInDays(
                                    \Carbon\Carbon::parse($d->tanggal_selesai)->endOfDay(),
                                    false
                                );
                                if($sisa < 0) $sisa = 0;
                            }else{
                                $sisa = null;
                            }

                            // ================= FORMAT TEXT =================
                            $label = $d->nama_diskon;

                            $label .= ' (';
                            $label .= $d->tipe_diskon == 'persen'
                                ? $d->nilai_diskon.'%'
                                : 'Rp '.number_format($d->nilai_diskon,0,',','.');
                            $label .= ')';

                            if(!is_null($sisa)){
                                $label .= ' | sisa: '.$sisa;
                                if($d->masa_aktif_tipe == 'hari'){
                                    $label .= ' hari';
                                }
                            }
                        @endphp

                        <option 
                            value="{{ $d->id_diskon }}"
                            data-tipe="{{ $d->tipe_diskon }}"
                            data-nilai="{{ $d->nilai_diskon }}"
                            data-sisa="{{ $sisa }}">
                            {{ $label }}
                        </option>

                        @endforeach
                    </select>
                </div>

                <div style="font-size:12px;color:#6b7280;">
                    Status: <span id="status_text">-</span>
                </div>

                <div>
                    <span>Diskon</span>
                    <span id="diskon">Rp 0</span>
                </div>
                
                <div class="payment-method">
                    <label>Metode Pembayaran</label>
                    <select id="payment_method">
                        <option value="tunai">Tunai</option>
                        <option value="kredit">Kredit</option>
                    </select>
                </div>
            </div>
            
            <input type="number" id="payment" placeholder="Masukkan jumlah uang">

            <div style="font-size:12px;color:#ef4444;">
                Sisa: <span id="sisa">Rp 0</span>
            </div>

            <div class="change-box">
                Kembali: <span id="change">Rp 0</span>
            </div>
            
            <button class="btn-pay" onclick="checkout()">Simpan</button>
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
                <span>Pelanggan</span>
                <span id="struk-pelanggan"></span>
            </div>

            <div class="info-row">
                <span>Catatan</span>
                <span id="struk-catatan"></span>
            </div>
            <div class="info-row">
                <span>Tanggal</span>
                <span id="struk-tanggal"></span>
            </div>
            <div class="info-row">
                <span>Kasir</span>
                <span>{{ $karyawan->nama_karyawan ?? 'Kasir' }}</span>
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

            <div class="info-row">
                <span id="struk-diskon-label">Diskon</span>
                <span id="struk-diskon"></span>
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
    .avatar-img{width:36px; height:36px; border-radius:50%; object-fit:cover;}
    .section-divider{border:none;border-top:1px solid #e5e7eb;margin:15px 0 18px;}
    .top-form{
        display:flex;
        gap:14px;
        margin-bottom:18px;
        position:relative;
        overflow:visible !important;
    }

    .customer-wrapper{
        flex:1;
        position:relative;
        overflow:visible !important;
    }

    .top-form > input{
        flex:1;
        padding:10px 14px;
        border-radius:12px;
        border:1px solid #d1d5db;
        font-size:14px;
    }
    .customer-wrapper.full-width{
        width:100%;
        max-width:420px;
        position:relative;
        z-index:999999;
        overflow:visible !important;
    }

    .customer-wrapper.full-width input{
        width:100%;
        position:relative;
        z-index:999999;
    }

    .customer-suggestions{
        position:absolute;
        top:calc(100% + 6px);
        left:0;
        width:100%;
        background:#fff;
        border:1px solid #d1d5db;
        border-radius:12px;
        box-shadow:0 10px 25px rgba(0,0,0,0.12);
        z-index:999999;
        max-height:220px;
        overflow-y:auto;
        display:none;
    }

    .customer-wrapper input{
        width:100%;
        padding:10px 14px;
        border-radius:12px;
        border:1px solid #d1d5db;
        font-size:14px;
    }
    
    .content-grid{
        display:grid;
        grid-template-columns:1fr 340px;
        gap:20px;
        position:relative;
        z-index:1;
    }
    .search-wrapper{position:relative;margin-bottom:15px;}
    .search-wrapper i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:13px;}
    .search-wrapper input{height:38px; width:100%;padding:9px 12px 9px 34px;border-radius:12px;border:1px solid #d1d5db;font-size:14px;}
    .menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;}
    .menu-card{background:white;padding:14px;border-radius:16px;text-align:center;box-shadow:0 4px 12px rgba(0,0,0,0.05);transition:.2s;position:relative;}
    .menu-card:hover{transform:translateY(-4px);}
    .menu-image{
        width:100%;
        height:110px;
        border-radius:12px;
        overflow:hidden;
        margin-bottom:10px;
        background:#f1f5f9;
    }

    .menu-image img{
        width:100%;
        height:100%;
        object-fit:cover;
        display:block;
        transition:.25s;
    }

    .menu-card:hover .menu-image img{
        transform:scale(1.05);
    }
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
    .search-wrapper-row{display:flex; gap:10px; margin-bottom:15px;}
    .search-wrapper-row .search-wrapper{flex:1;}
    .btn-pelanggan{height:38px;padding:0 14px;border:none;border-radius:12px;background:#2563eb;color:white;font-size:13px;display:flex;align-items:center;justify-content:center;cursor:pointer;white-space:nowrap;}
    .customer-card input,.customer-card textarea{width:100%;padding:10px;border-radius:10px;border:1px solid #d1d5db;margin-bottom:10px;font-size:13px;}
    .customer-card textarea{height:70px;resize:none;}
    .payment-method{margin-top:10px;display:flex;flex-direction:column;gap:4px;font-size:13px;}
    .payment-method select{padding:8px;border-radius:10px;border:1px solid #d1d5db;font-size:13px;background:white;}
    .popup-overlay{position:fixed;inset:0; background:rgba(0,0,0,0.5);display:none;justify-content:center;align-items:center;z-index:9999;}
    .popup-card{width:360px;background:white;border-radius:14px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,0.2);font-size:13px;}
    .popup-header{background:#0f172a;color:white;padding:16px;text-align:center;}
    .popup-header h2{margin:0;font-size:18px;}
    .popup-body{padding:16px;}
    .popup-body h3{text-align:center;margin-bottom:12px;}
    .info-row{display:flex;justify-content:space-between;margin-bottom:6px;}
    .total-row{font-weight:bold;font-size:14px;}
    .popup-footer{display:flex;gap:10px;padding:12px;}
    .btn-print{flex:1;background:#2563eb;color:white;border:none;padding:8px;border-radius:8px;}
    .btn-close{flex:1;background:#e5e7eb;border:none;padding:8px;border-radius:8px;}
    .thanks{text-align:center; margin-top:14px;font-size:12px;color:#6b7280;}
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

    .customer-item{
        padding:10px 14px;
        cursor:pointer;
        border-bottom:1px solid #f3f4f6;
        font-size:13px;
    }

    .customer-item:hover{
        background:#f9fafb;
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function(){

const TAX_TYPE = "{{ $pajak->tipe_pajak ?? '' }}".toLowerCase();
const TAX_VALUE = {{ $pajak->nilai_pajak ?? 0 }};

let cart = {};

// ================= TAMBAH KE KERANJANG =================
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

// ================= QTY =================
window.increaseQty = function(id){
    cart[id].qty++;
    renderCart();
}

window.decreaseQty = function(id){
    cart[id].qty--;
    if(cart[id].qty<=0) delete cart[id];
    renderCart();
}

// ================= CLEAR =================
window.clearCart = function(){
    cart = {};

    document.getElementById('customer_name').value = '';
    document.getElementById('order_note').value = '';
    document.getElementById('payment').value = '';

    const diskon = document.getElementById('diskon_select');
    if(diskon) diskon.value = '';

    document.getElementById('cart-items').innerHTML = '<p class="empty">Keranjang kosong</p>';
    document.getElementById('customer-info').innerHTML = '';

    document.getElementById('subtotal').innerText = 'Rp 0';
    document.getElementById('tax').innerText = 'Rp 0';
    document.getElementById('diskon').innerText = 'Rp 0';
    document.getElementById('total').innerText = 'Rp 0';
    document.getElementById('change').innerText = 'Rp 0';
}

// ================= RENDER =================
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

    container.innerHTML = html || '<p class="empty">Keranjang kosong</p>';

    const name=document.getElementById('customer_name').value;
    const note=document.getElementById('order_note').value;

    let infoHTML = '';

    const selectedPhone = customerInput.dataset.phone || '-';

    if(name){
        infoHTML += `
            <div><strong>Pelanggan:</strong> ${name}</div>
            <div><strong>No HP:</strong> ${selectedPhone}</div>
        `;
    }

    if(note){
        infoHTML += `<div><strong>Catatan:</strong> ${note}</div>`;
    }

    customerInfo.innerHTML = infoHTML;

    let tax = 0;
    if (TAX_TYPE === 'persen') {
        tax = Math.round((subtotal * TAX_VALUE) / 100);
    } else if (TAX_TYPE === 'nominal') {
        tax = TAX_VALUE;
    }

    let discount = 0;
    const diskonSelect = document.getElementById('diskon_select');

    if(diskonSelect && diskonSelect.value){
        const opt = diskonSelect.options[diskonSelect.selectedIndex];
        const tipe  = opt.getAttribute('data-tipe');
        const nilai = parseInt(opt.getAttribute('data-nilai')) || 0;

        if(tipe === 'persen'){
            discount = Math.round((subtotal * nilai) / 100);
        }else{
            discount = nilai;
        }
    }

    const total = Math.max(subtotal + tax - discount, 0);

    document.getElementById('subtotal').innerText = 'Rp ' + subtotal.toLocaleString();
    document.getElementById('tax').innerText = 'Rp ' + tax.toLocaleString();
    document.getElementById('diskon').innerText = 'Rp ' + discount.toLocaleString();
    document.getElementById('total').innerText = 'Rp ' + total.toLocaleString();

    updateChange(total);
}

// ================= EVENT =================
document.getElementById('payment')?.addEventListener('input',function(){
    const total=parseInt(document.getElementById('total').innerText.replace(/\D/g,''))||0;
    updateChange(total);
});

document.getElementById('diskon_select')?.addEventListener('change', renderCart);
document.getElementById('order_note')?.addEventListener('input', renderCart);

// ================= KEMBALIAN =================
function updateChange(total){

    const pay = parseInt(document.getElementById('payment').value) || 0;

    const change = pay - total;
    const sisa = total - pay;

    // KEMBALIAN
    document.getElementById('change').innerText =
        change > 0 ? 'Rp ' + change.toLocaleString() : 'Rp 0';

    // 🔥 SISA
    document.getElementById('sisa').innerText =
        sisa > 0 ? 'Rp ' + sisa.toLocaleString() : 'Rp 0';

    // 🔥 STATUS
    let status = 'Belum Lunas';
    if(pay >= total){
        status = 'Lunas';
    }

    document.getElementById('status_text').innerText = status;
}

// ================= CHECKOUT =================
window.checkout = function(){

    if(Object.keys(cart).length === 0){
        alert("Keranjang kosong");
        return;
    }

    const total = parseInt(document.getElementById('total').innerText.replace(/\D/g,''))||0;
    const bayar = parseInt(document.getElementById('payment').value)||0;

    const metode = document.getElementById('payment_method').value;

// tunai harus cukup
if(metode === 'tunai' && bayar < total){
    alert("Uang kurang");
    return;
    window.checkout = async function () {

    if (Object.keys(cart).length === 0) {
        alert("Keranjang kosong");
        return;
    }

    const total = parseInt(document.getElementById('total').innerText.replace(/\D/g, '')) || 0;
    const bayar = parseInt(document.getElementById('payment').value) || 0;
    const metode = document.getElementById('payment_method').value;

    if (metode === 'tunai' && bayar < total) {
        alert("Uang kurang");
        return;
    }

    if (metode === 'kredit' && bayar < 50000) {
        alert("DP minimal Rp 50.000");
        return;
    }

    let items = [];

    for (let id in cart) {
        items.push({
            id: id,
            qty: cart[id].qty,
            harga: cart[id].price
        });
    }

    const payload = {
        nama_pelanggan: document.getElementById('customer_name').value,
        id_pelanggan: document.getElementById('id_pelanggan').value || null,
        catatan: document.getElementById('order_note').value,
        bayar: bayar,
        metode_bayar: metode,
        diskon_id: document.getElementById('diskon_select')?.value || null,
        items: items
    };

    // ================= OFFLINE =================
    if (!navigator.onLine) {
        saveOfflineTransaction(payload);
        alert("Offline: transaksi disimpan ke IndexedDB");
        clearCart();
        return;
    }

    // ================= ONLINE =================
    try {
        const res = await fetch("{{ route('penjualan.simpan') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.status === "success") {
            showPopup(data.kode, data.total);
            clearCart();
        } else {
            alert(data.message || "Gagal simpan");
        }

    } catch (err) {
        console.error(err);
        alert("Gagal koneksi ke server");
    }
}

}

// kredit minimal 50rb
if(metode === 'kredit' && bayar < 50000){
    alert("DP minimal Rp 50.000");
    return;
}

    let items = [];

    for(let id in cart){
        items.push({
            id: id,
            qty: cart[id].qty,
            harga: cart[id].price
        });
    }

    const diskonSelect = document.getElementById('diskon_select');
    const selectedOption = diskonSelect?.selectedOptions[0];

    fetch("{{ route('penjualan.simpan') }}",{
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'X-CSRF-TOKEN':'{{ csrf_token() }}'
        },
        body: JSON.stringify({
            nama_pelanggan: document.getElementById('customer_name').value,
            id_pelanggan: document.getElementById('id_pelanggan').value || null,
            catatan: document.getElementById('order_note').value,
            bayar: bayar,
            metode_bayar: document.getElementById('payment_method').value,
            diskon_id: diskonSelect?.value || null,
            items: items
        })
    })
    .then(async res => {
        let text = await res.text();
        let data = JSON.parse(text);

        if(data.status === "success"){

            // ================= UPDATE DISKON REALTIME =================
            if(selectedOption){
                let sisa = parseInt(selectedOption.getAttribute('data-sisa')) || 0;

                if (sisa > 0) {
                    sisa -= 1;
                    selectedOption.setAttribute('data-sisa', sisa);

                    selectedOption.textContent = selectedOption.textContent.replace(/sisa:\s*\d+/, 'sisa: ' + sisa);

                    if (sisa <= 0) {
                        selectedOption.remove();
                    }
                }
            }

            showPopup(data.kode, data.total);
            clearCart();

        }else{
            alert(data.message || "Gagal simpan");
        }

    })
    .catch(err=>{
        console.error(err);
        alert("Gagal koneksi ke server");
    });
}

// ================= POPUP =================
function showPopup(kode,total){

    const pelanggan = document.getElementById('customer_name').value || '-';
    const catatan = document.getElementById('order_note').value || '-';
    const subtotal = parseInt(document.getElementById('subtotal').innerText.replace(/\D/g,''))||0;
    const tax = parseInt(document.getElementById('tax').innerText.replace(/\D/g,''))||0;
    const discount = parseInt(document.getElementById('diskon').innerText.replace(/\D/g,''))||0;
    const bayar = parseInt(document.getElementById('payment').value)||0;
    const kembali = bayar-total;

    const diskonSelect = document.getElementById('diskon_select');
    let diskonLabel = "Diskon";

    if(diskonSelect && diskonSelect.value){
        const opt = diskonSelect.options[diskonSelect.selectedIndex];
        const tipe = opt.getAttribute('data-tipe');
        const nilai = opt.getAttribute('data-nilai');

        if(tipe === 'persen'){
            diskonLabel = `Diskon (${nilai}%)`;
        }
    }

    document.getElementById('struk-kode').innerText = kode;
    document.getElementById('struk-pelanggan').innerText = pelanggan;
    document.getElementById('struk-catatan').innerText = catatan;
    document.getElementById('struk-tanggal').innerText = new Date().toLocaleString();

    document.getElementById('struk-subtotal').innerText = 'Rp '+subtotal.toLocaleString();
    document.getElementById('struk-pajak').innerText = 'Rp '+tax.toLocaleString();
    document.getElementById('struk-diskon-label').innerText = diskonLabel;
    document.getElementById('struk-diskon').innerText = 'Rp '+discount.toLocaleString();
    document.getElementById('struk-total').innerText = 'Rp '+total.toLocaleString();
    document.getElementById('struk-bayar').innerText = 'Rp '+bayar.toLocaleString();
    document.getElementById('struk-kembali').innerText = 'Rp '+kembali.toLocaleString();

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

    document.getElementById('struk-items').innerHTML = itemsHTML;

    document.getElementById('popup').style.display = 'flex';
}

// ================= SEARCH MENU =================
const searchInput = document.getElementById('search');

searchInput?.addEventListener('input', function () {
    const keyword = this.value.toLowerCase();

    document.querySelectorAll('.menu-card').forEach(card => {
        const name = card.dataset.name.toLowerCase();

        if (name.includes(keyword)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});

// ================= AUTOCOMPLETE PELANGGAN =================
const customerSearchUrl = "{{ route('kasir.pelanggan.cari') }}";
const customerInput = document.getElementById('customer_name');
const customerId = document.getElementById('id_pelanggan');
const customerSuggestions = document.getElementById('customer_suggestions');
const paymentMethod = document.querySelector('#payment_method');

// ================= GANTI METODE =================
paymentMethod?.addEventListener('change', function () {
    const metode = this.value;

    customerInput.value = '';
    customerId.value = '';
    customerSuggestions.innerHTML = '';
    customerSuggestions.style.display = 'none';

    if (metode === 'tunai') {
        customerInput.placeholder = 'Nama pelanggan (manual)';
    } else {
        customerInput.placeholder = 'Cari nama / no hp pelanggan';
    }

    renderCart();
});

// ================= INPUT PELANGGAN =================
customerInput?.addEventListener('input', async function () {
    const paymentSelect = document.querySelector('#payment_method');
    const metode = paymentSelect ? paymentSelect.value : 'tunai';
    const keyword = this.value.trim();

    console.log('metode:', metode);
    console.log('keyword:', keyword);

    customerId.value = '';

    renderCart();

    if (metode !== 'kredit') {
        customerSuggestions.style.display = 'none';
        return;
    }

    if (keyword.length < 1) {
        customerSuggestions.innerHTML = '';
        customerSuggestions.style.display = 'none';
        return;
    }

    try {
        const response = await fetch(
            `${customerSearchUrl}?keyword=${encodeURIComponent(keyword)}`
        );

        const data = await response.json();

        console.log('hasil customer:', data);

        let html = '';

        if (!data.length) {
            html = `
                <div class="customer-item">
                    Pelanggan tidak ditemukan
                </div>
            `;
        } else {
            data.forEach(item => {
                html += `
                    <div class="customer-item"
                        data-id="${item.id_pelanggan}"
                        data-name="${item.nama_pelanggan}"
                        data-phone="${item.no_hp ?? '-'}">
                        <strong>${item.nama_pelanggan}</strong><br>
                        <small>${item.no_hp ?? '-'}</small>
                    </div>
                `;
            });
        }

        customerSuggestions.innerHTML = html;
        customerSuggestions.style.display = 'block';

    } catch (error) {
        console.error('Autocomplete error:', error);
    }
});

// ================= PILIH CUSTOMER =================
customerSuggestions?.addEventListener('click', function (e) {
    const item = e.target.closest('.customer-item');
    if (!item || !item.dataset.id) return;

    customerInput.value = item.dataset.name;
    customerInput.dataset.phone = item.dataset.phone;
    customerId.value = item.dataset.id;

    customerSuggestions.style.display = 'none';

    renderCart();
});

// ================= KLIK LUAR =================
document.addEventListener('click', function (e) {
    if (
        !customerInput.contains(e.target) &&
        !customerSuggestions.contains(e.target)
    ) {
        customerSuggestions.style.display = 'none';
    }
});

// ================= TOMBOL =================
window.closePopup = function(){
    document.getElementById('popup').style.display = 'none';
}

window.printStruk = function(){
    window.print();
}

// ================= CUSTOMER =================
window.openCustomerPopup = function(){
    document.getElementById('customer-popup').style.display = 'flex';
}

window.closeCustomerPopup = function(){
    document.getElementById('customer-popup').style.display = 'none';
}

window.saveCustomer = async function () {
    const nama = document.getElementById('nama_pelanggan').value.trim();
    const hp = document.getElementById('no_hp').value.trim();
    const alamat = document.getElementById('alamat').value.trim();

    if (!nama) {
        alert("Nama pelanggan wajib diisi");
        return;
    }

    try {
        const response = await fetch("{{ route('pelanggan.simpan') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                nama_pelanggan: nama,
                no_hp: hp,
                alamat: alamat
            })
        });

        const data = await response.json();
        console.log(data);

        if (!response.ok) {
            alert(data.message || "Nomor HP sudah terdaftar");
            return;
        }

        if (data.success) {
            document.getElementById('customer_name').value = data.data.nama_pelanggan;
            document.getElementById('id_pelanggan').value = data.data.id_pelanggan;

            renderCart();
            closeCustomerPopup();

            document.getElementById('nama_pelanggan').value = '';
            document.getElementById('no_hp').value = '';
            document.getElementById('alamat').value = '';

            alert("Pelanggan berhasil disimpan");
        }

    } catch (error) {
        console.error(error);
        alert("Terjadi kesalahan saat menyimpan pelanggan");
    }
}

});
</script>
@endsection