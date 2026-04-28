@extends('layouts.kasir')

@section('content')
@if(session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:12px;border-radius:10px;margin-bottom:15px;">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:12px;border-radius:10px;margin-bottom:15px;">
        {{ session('success') }}
    </div>
@endif

<div class="pos-wrapper">
    <main class="main-content">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h1>Transaksi Homestay</h1>
                <p>Booking kamar homestay</p>
            </div>
        </div>

        <hr class="section-divider">

        <form action="{{ route('kasir.homestay.simpan') }}" method="POST">
            @csrf

            <div class="content-grid">

                {{-- LEFT --}}
                <div>
                    <div class="search-wrapper-row">
                        <div class="search-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" placeholder="Cari kamar...">
                        </div>
                    </div>

                    <div class="menu-grid">
                        @forelse($layanan as $item)
                            <div class="menu-card homestay-card"
                                data-search="{{ strtolower($item->nama_layanan) }}"
                                onclick="pilihKamar('{{ $item->nama_layanan }}', {{ $item->harga }}, {{ $item->id_layanan }})">

                                <div class="menu-image">
                                    <img src="{{ asset('storage/layanan/'.$item->gambar) }}">
                                </div>

                                <h4>{{ $item->nama_layanan }}</h4>

                                <div class="price">
                                    Rp {{ number_format($item->harga,0,',','.') }}
                                </div>
                            </div>
                        @empty
                            <p>Kamar tidak tersedia</p>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="cart-box">

                    <div class="cart-header">
                        🏠 Keranjang Homestay
                    </div>

                    <div id="cartItems">
                        <p class="text-muted small">Belum pilih kamar</p>
                    </div>

                    {{-- FORM --}}
                    <div class="booking-grid">

                        <div class="booking-field full">
                            <label>Nama Pelanggan</label>

                            <div class="customer-cart-wrapper">
                                <input type="hidden" name="id_pelanggan" id="id_pelanggan">

                                <input type="text"
                                    name="nama_pelanggan"
                                    id="customer_name_cart"
                                    placeholder="Ketik atau pilih pelanggan">

                                <div id="customer_suggestions_cart" class="customer-suggestions-cart"></div>
                            </div>
                        </div>

                        <div class="booking-field full">
                            <label>No. Telepon</label>
                            <input type="text" id="no_telp" readonly>
                        </div>

                        <div class="booking-field">
                            <label>Check-in</label>
                            <input type="date" name="tanggal_checkin" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="booking-field">
                            <label>Check-out</label>
                            <input type="date" name="tanggal_checkout" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="booking-field">
                            <label>Metode Bayar</label>
                            <select name="metode_bayar" id="metode_bayar" required>
                                <option value="tunai">Tunai</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                            </select>
                        </div>

                        <div class="booking-field">
                            <label>Nominal Bayar</label>
                            <input type="number" name="bayar" id="bayar" placeholder="Masukkan nominal">
                        </div>

                        <div class="booking-field full">
                            <label>Keterangan</label>
                            <textarea name="keterangan" placeholder="Catatan tambahan (opsional)"></textarea>
                        </div>

                        <div class="booking-field full">
                            <label>Kembalian</label>
                            <input type="text" id="kembalian" readonly>
                        </div>

                    </div>

                    <input type="hidden" name="id_layanan" id="id_layanan">
                    <input type="hidden" name="harga" id="harga">

                    <div class="cart-summary">
                        <div class="total">
                            <span>Total</span>
                            <span id="totalDisplay">Rp 0</span>
                        </div>

                        <button type="submit" class="btn-pay">
                            Booking
                        </button>
                    </div>

                </div>
            </div>

            {{-- KALENDER --}}
            <div class="calendar-box mt-4">

                <!-- HEADER -->
                <div class="calendar-title d-flex justify-content-between align-items-center">

                    <button type="button" onclick="prevMonth()">⬅</button>

                    <div class="d-flex align-items-center gap-2">
                        <select id="inputMonth" style="width:120px">
                            <option value="0">Januari</option>
                            <option value="1">Februari</option>
                            <option value="2">Maret</option>
                            <option value="3">April</option>
                            <option value="4">Mei</option>
                            <option value="5">Juni</option>
                            <option value="6">Juli</option>
                            <option value="7">Agustus</option>
                            <option value="8">September</option>
                            <option value="9">Oktober</option>
                            <option value="10">November</option>
                            <option value="11">Desember</option>
                        </select>

                        <input type="number" id="inputYear" style="width:90px">

                        <button type="button" onclick="goToDate()">Go</button>
                    </div>

                    <button type="button" onclick="nextMonth()">➡</button>
                </div>

                <span id="calendarTitle" class="d-block text-center mt-2 fw-bold"></span>

                <!-- BODY -->
                <div class="calendar-wrapper mt-3">
                    <div class="calendar-table">
                        
                        @foreach($layanan as $item)
                            <div class="calendar-row">

                                <!-- KIRI -->
                                <div class="calendar-room">
                                    {{ $item->nama_layanan }}
                                </div>

                                <!-- KANAN -->
                                <div class="calendar-days" data-id="{{ $item->id_layanan }}"></div>

                            </div>
                        @endforeach

                    </div>
                </div>

                <div class="calendar-legend mt-2">
                    <span class="legend-booked"></span> Kamar Terisi
                </div>

            </div>
            
        </form>
    </main>

    {{-- ================== MODAL STRUK ===================== --}}
    @if(session('struk'))
    <div class="popup-overlay show" id="strukPopup">
        <div class="popup-card">

            <div class="popup-header">
                <h3>Struk Transaksi</h3>
            </div>

            <div class="popup-body" id="printArea">

                <h3 class="text-center">STRUK PEMBAYARAN</h3>

                <div class="struk-row">
                    <span>No. Transaksi</span>
                    <strong>{{ session('struk.kode') }}</strong>
                </div>

                <div class="struk-row">
                    <span>Pelanggan</span>
                    <span>{{ session('struk.nama') }}</span>
                </div>

                <div class="struk-row">
                    <span>Tanggal</span>
                    <span>{{ now()->format('d M Y H:i') }}</span>
                </div>

                <hr>

                <div class="struk-row">
                    <span>Kamar</span>
                    <span>{{ session('struk.kamar') }}</span>
                </div>

                <div class="struk-row">
                    <span>Check-in</span>
                    <span>{{ \Carbon\Carbon::parse(session('struk.checkin'))->format('d M Y H:i') }}</span>
                </div>

                <div class="struk-row">
                    <span>Check-out</span>
                    <span>{{ \Carbon\Carbon::parse(session('struk.checkout'))->format('d M Y H:i') }}</span>
                </div>

                <hr>

                <div class="struk-row total">
                    <span>TOTAL</span>
                    <strong>Rp {{ number_format(session('struk.total')) }}</strong>
                </div>

                <div class="struk-row">
                    <span>Bayar</span>
                    <span>Rp {{ number_format(session('struk.bayar')) }}</span>
                </div>

                <div class="struk-row">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format(session('struk.kembalian')) }}</span>
                </div>

                <p class="thanks">Terima kasih 🙏</p>

            </div>

            <div class="popup-footer">
                <button class="btn-print" onclick="printStruk()">Cetak</button>
                <button class="btn-close" onclick="closeStruk()">Tutup</button>
            </div>

        </div>
    </div>
    @endif

</div>

<style>
    body{
        margin:0;
        font-family:'Inter',sans-serif;
        background:#f4f6f9;
        color:#111827;
    }

    .page-header{
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:18px;
    }

    .page-header h1{
        font-size:28px;
        font-weight:700;
        margin:0;
    }

    .page-header p{
        font-size:14px;
        color:#6b7280;
        margin:4px 0 0;
    }

    .section-divider{
        border:none;
        border-top:1px solid #e5e7eb;
        margin:15px 0 18px;
    }

    .top-form{
        display:flex;
        gap:14px;
        margin-bottom:18px;
    }

    .top-form input{
        flex:1;
        padding:12px 14px;
        border-radius:14px;
        border:1px solid #d1d5db;
        font-size:14px;
        background:#fff;
    }

    .content-grid{
        display:grid;
        grid-template-columns:1fr 360px;
        gap:22px;
        align-items:start;
    }

    .search-wrapper-row{
        display:flex;
        gap:12px;
        margin-bottom:18px;
    }

    .search-wrapper{
        flex:1;
        position:relative;
    }

    .search-wrapper i{
        position:absolute;
        left:14px;
        top:50%;
        transform:translateY(-50%);
        color:#9ca3af;
        font-size:14px;
    }

    .search-wrapper input{
        width:100%;
        height:42px;
        padding:10px 14px 10px 38px;
        border-radius:14px;
        border:1px solid #d1d5db;
        font-size:14px;
        background:#fff;
    }

    .btn-pelanggan{
        height:42px;
        padding:0 18px;
        border:none;
        border-radius:14px;
        background:#2563eb;
        color:#fff;
        font-size:13px;
        font-weight:600;
        cursor:pointer;
        white-space:nowrap;
        box-shadow:0 4px 12px rgba(37,99,235,.25);
    }

    .menu-grid{
        display:grid;
        grid-template-columns:repeat(auto-fill,minmax(170px,1fr));
        gap:18px;
    }

    .menu-card{
        background:#fff;
        padding:14px;
        border-radius:18px;
        text-align:center;
        box-shadow:0 6px 18px rgba(0,0,0,0.06);
        transition:.25s;
        cursor:pointer;
        border:1px solid #f1f5f9;
    }

    .menu-card:hover{
        transform:translateY(-4px);
        box-shadow:0 10px 22px rgba(0,0,0,0.08);
    }

    .menu-card.active {
        border:2px solid #16a34a;
    }

    .menu-image{
        width:100%;
        height:120px;
        border-radius:14px;
        overflow:hidden;
        margin-bottom:12px;
        background:#f1f5f9;
    }

    .menu-image img{
        width:100%;
        height:100%;
        object-fit:cover;
        transition:.3s;
    }

    .menu-card:hover .menu-image img{
        transform:scale(1.05);
    }

    .menu-card h4{
        font-size:15px;
        margin:8px 0 4px;
        font-weight:600;
    }

    .price{
        color:#2563eb;
        font-weight:700;
        margin-bottom:6px;
        font-size:15px;
    }

    .cart-box{
        background:#fff;
        padding:20px;
        border-radius:20px;
        box-shadow:0 8px 24px rgba(0,0,0,0.06);
        position:sticky;
        top:20px;
        font-size:13px;
        border:1px solid #eef2f7;
    }

    .cart-header{
        font-size:16px;
        font-weight:700;
        margin-bottom:14px;
    }

    .booking-grid{
        display:grid;
        grid-template-columns:1fr 1fr;
        gap:12px;
        margin-top:12px;
    }

    .booking-field{
        display:flex;
        flex-direction:column;
        gap:6px;
    }

    .booking-field.full{
        grid-column:span 2;
    }

    .booking-field label{
        font-size:12px;
        font-weight:600;
        color:#374151;
    }

    .booking-field input,
    .booking-field select,
    .booking-field textarea{
        width:100%;
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #d1d5db;
        font-size:13px;
        background:#fff;
        transition:.2s;
    }

    .booking-field input:focus,
    .booking-field select:focus,
    .booking-field textarea:focus{
        outline:none;
        border-color:#2563eb;
        box-shadow:0 0 0 3px rgba(37,99,235,.1);
    }

    .booking-field textarea{
        resize:none;
        min-height:70px;
    }

    #customer_name_cart,
    #no_telp{
        background:#f9fafb;
    }

    .cart-item-selected{
        display:flex;
        justify-content:space-between;
        align-items:center;
        padding:12px;
        border:1px solid #e5e7eb;
        border-radius:14px;
        background:#f9fafb;
        margin-bottom:14px;
    }

    .cart-summary{
        margin-top:16px;
        padding-top:14px;
        border-top:1px dashed #d1d5db;
    }

    .total{
        display:flex;
        justify-content:space-between;
        border-top:1px dashed #d1d5db;
        padding-top:10px;
        font-size:14px;
        font-weight:700;
    }

    .btn-pay{
        margin-top:14px;
        width:100%;
        padding:12px;
        border:none;
        border-radius:14px;
        background:#16a34a;
        color:white;
        font-size:14px;
        font-weight:600;
        cursor:pointer;
        box-shadow:0 4px 14px rgba(22,163,74,.25);
    }

    .customer-cart-wrapper{
        position: relative;
    }

    .customer-cart-wrapper input{
        width:100%;
        padding:10px 12px;
        border-radius:12px;
        border:1px solid #d1d5db;
        font-size:13px;
        background:#f9fafb;
        transition:.2s;
    }

    .customer-cart-wrapper input:focus{
        outline:none;
        border-color:#2563eb;
        box-shadow:0 0 0 3px rgba(37,99,235,.1);
    }

    .customer-suggestions-cart{
        position:absolute;
        top:calc(100% + 6px);
        left:0;
        width:100%;
        background:#fff;
        border:1px solid #e5e7eb;
        border-radius:14px;
        max-height:220px;
        overflow-y:auto;
        z-index:99999;
        box-shadow:0 10px 25px rgba(0,0,0,0.08);
        display:none;
    }

    .customer-suggestions-cart.show{
        display:block;
    }

    .customer-suggestions-cart .suggestion-item{
        padding:12px;
        cursor:pointer;
        transition:.2s;
        border-bottom:1px solid #f3f4f6;
    }

    .customer-suggestions-cart .suggestion-item:last-child{
        border-bottom:none;
    }

    .customer-suggestions-cart .suggestion-item:hover{
        background:#eff6ff;
    }

    .customer-name{
        font-size:14px;
        font-weight:600;
        color:#111827;
    }

    .customer-phone{
        font-size:12px;
        color:#6b7280;
        margin-top:3px;
    }

    .receipt-overlay{
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.45);
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
        background:#111827;
        color:#fff;
        text-align:center;
        padding:18px;
    }
    .receipt-body{
        padding:18px;
        font-size:14px;
    }
    .row-line{
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

    .popup-overlay{
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        display: none; /* WAJIB */
        align-items: center;
        justify-content: center;
        z-index: 99999;
    }

    .popup-card{
        width: 380px;
        background: #fff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,.2);
    }

    .popup-header{
        padding: 16px;
        background: #111827;
        color: white;
    }

    .popup-body{
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .popup-body input,
    .popup-body textarea{
        width: 100%;
        padding: 10px 12px;
        border-radius: 12px;
        border: 1px solid #d1d5db;
    }

    .popup-footer{
        display: flex;
        gap: 10px;
        padding: 16px;
    }

    .btn-print{flex:1;background:#2563eb;color:white;border:none;padding:8px;border-radius:8px;}
    .btn-close{flex:1;background:#e5e7eb;border:none;padding:8px;border-radius:8px;}

    .struk-row{
        display:flex;
        justify-content:space-between;
        align-items:flex-start;
        gap:10px;
        margin-bottom:6px;
        font-size:13px;
    }
    .struk-row span:first-child{
        color:#6b7280;
    }
    .struk-row span:last-child,
    .struk-row strong{
        text-align:right;
        max-width:60%;
    }

    .struk-row.total{
        font-weight:700;
        font-size:15px;
        border-top:1px dashed #ccc;
        padding-top:6px;
    }

    .thanks{
        text-align:center;
        margin-top:12px;
        font-size:12px;
        color:#6b7280;
    }
    .calendar-wrapper{
        overflow-x:auto;
    }

    .calendar-row{
        display:flex;
        margin-bottom:6px;
    }

    .calendar-room{
        width:150px;
        background:#f3f4f6;
        padding:10px;
        font-weight:600;
        border:1px solid #e5e7eb;
    }

    .calendar-days{
        display:grid;
        grid-template-columns: repeat(auto-fit, minmax(28px, 1fr));
        gap:6px;
        width:100%;
    }

    .calendar-box{
        background:#fff;
        border-radius:18px;
        padding:18px;
        box-shadow:0 8px 20px rgba(0,0,0,.06);
    }

    .calendar-grid{
        display:grid;
        grid-template-columns:repeat(7,1fr);
        gap:10px;
    }

    .calendar-day{
        background:#f9fafb;
        border:1px solid #e5e7eb;
        border-radius:8px;
        display:flex;
        align-items:center;
        justify-content:center;
        height:32px;
        font-size:12px;
        font-weight:600;
    }

    .calendar-day.booked{
        background:#ef4444;
        color:white;
    }

    .calendar-day.past{
        background:#e5e7eb;
        color:#9ca3af;
    }

    .calendar-day.today{
        border:2px solid #2563eb;
    }

    .calendar-day.checkout{
        background:#facc15;
        color:#000;
    }

    .day-number{
        font-size:16px;
        font-weight:700;
    }

    .past{
        background:#e5e7eb;
        color:#9ca3af;
    }

    @media(max-width:1200px){
        .calendar-day{
            font-size:10px;
            height:28px;
        }
    }
</style>

{{-- ================= SCRIPT ================= --}}
<script>

// =========================
// GLOBAL STATE
// =========================
let selectedRoom = null;


// =========================
// PILIH KAMAR (WAJIB GLOBAL)
// =========================
window.pilihKamar = function(nama, harga, id){

    selectedRoom = { nama, harga, id };

    document.getElementById('id_layanan').value = id;
    document.getElementById('harga').value = harga;

    // tampilkan dulu walaupun belum pilih tanggal
    document.getElementById('cartItems').innerHTML = `
        <div class="cart-item-selected">
            <div>
                <strong>${nama}</strong>
                <div class="small text-muted">Silakan pilih tanggal</div>
            </div>
            <div class="fw-bold">
                Rp ${harga.toLocaleString('id-ID')}
            </div>
        </div>
    `;

    document.getElementById('totalDisplay').innerText =
        "Rp " + harga.toLocaleString('id-ID');

    hitungTotal(); // kalau tanggal sudah ada langsung update
}


// =========================
// HITUNG TOTAL
// =========================
function hitungTotal(){

    if (!selectedRoom) return;

    let total = selectedRoom.harga;

    document.getElementById('cartItems').innerHTML = `
        <div class="cart-item-selected">
            <div>
                <strong>${selectedRoom.nama}</strong>
            </div>
            <div class="fw-bold">
                Rp ${total.toLocaleString('id-ID')}
            </div>
        </div>
    `;

    document.getElementById('totalDisplay').innerText =
        "Rp " + total.toLocaleString('id-ID');
}


// =========================
// DOM READY
// =========================
document.addEventListener("DOMContentLoaded", function(){

    let checkinInput = document.querySelector('[name="tanggal_checkin"]');
    let checkoutInput = document.querySelector('[name="tanggal_checkout"]');

    // =========================
    // EVENT TANGGAL
    // =========================
    if(checkinInput && checkoutInput){

        checkinInput.addEventListener('change', function(){

            // minimal checkout = checkin
            checkoutInput.min = this.value;

            if(!checkoutInput.value){
                checkoutInput.value = this.value;
            }

            hitungTotal();
        });

        checkoutInput.addEventListener('change', hitungTotal);
    }


    // =========================
    // SEARCH
    // =========================
    let searchInput = document.getElementById('search');

    searchInput?.addEventListener('keyup', function(){
        let keyword = this.value.toLowerCase();

        document.querySelectorAll('.homestay-card').forEach(card => {
            let text = card.dataset.search;
            card.style.display = text.includes(keyword) ? '' : 'none';
        });
    });


    // =========================
    // AUTOCOMPLETE CUSTOMER
    // =========================
    const customerInput = document.getElementById('customer_name_cart');
    const suggestionBox = document.getElementById('customer_suggestions_cart');

    customerInput?.addEventListener('input', async function(){

        let keyword = this.value;

        if(keyword.length < 1){
            suggestionBox.innerHTML = '';
            suggestionBox.classList.remove('show');
            return;
        }

        try {
            let res = await fetch(`{{ route('kasir.pelanggan.cari') }}?keyword=${keyword}`);
            let data = await res.json();

            let html = '';

            data.forEach(item => {
                html += `
                    <div class="suggestion-item"
                        onclick="selectCustomer(${item.id_pelanggan}, '${item.nama_pelanggan}', '${item.no_hp ?? ''}')">
                        <div class="customer-name">${item.nama_pelanggan}</div>
                        <div class="customer-phone">${item.no_hp ?? '-'}</div>
                    </div>
                `;
            });

            suggestionBox.innerHTML = html;
            suggestionBox.classList.add('show');

        } catch (err) {
            console.error("Error:", err);
        }
    });

    document.getElementById('customer_name_cart').addEventListener('input', function(){
    document.getElementById('id_pelanggan').value = '';
    });

    // =========================
    // SELECT CUSTOMER
    // =========================
    window.selectCustomer = function(id, nama, hp){
        document.getElementById('id_pelanggan').value = id;
        document.getElementById('customer_name_cart').value = nama;
        document.getElementById('no_telp').value = hp;

        suggestionBox.innerHTML = '';
        suggestionBox.classList.remove('show');
    }

});

function hitungKembalian() {
    let bayar = parseInt(document.getElementById('bayar').value) || 0;

    let totalText = document.getElementById('totalDisplay').innerText
        .replace('Rp','')
        .replace(/\./g,'')
        .trim();

    let total = parseInt(totalText) || 0;

    let kembalian = bayar - total;

    document.getElementById('kembalian').value =
        "Rp " + (kembalian > 0 ? kembalian : 0).toLocaleString('id-ID');
}
document.getElementById('bayar').addEventListener('input', hitungKembalian);
hitungKembalian();


function closeStruk() {
    document.getElementById('strukPopup').style.display = 'none';
}

function printStruk() {
    let printContents = document.getElementById('printArea').innerHTML;
    let originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;

    location.reload();
}

document.addEventListener("DOMContentLoaded", function(){
    let popup = document.getElementById('strukPopup');
    if(popup){
        popup.style.display = 'flex';
    }
});

// KALENDER
let currentDate = new Date();
let bookingGrouped = @json($bookingGrouped);

function renderCalendar(){

    let today = new Date().toISOString().split('T')[0];

    let year = currentDate.getFullYear();
    let monthIndex = currentDate.getMonth();
    let monthNum = (monthIndex + 1).toString().padStart(2,'0');

    document.getElementById('inputMonth').value = monthIndex;
    document.getElementById('inputYear').value = year;

    let monthName = currentDate.toLocaleString('id-ID', { month: 'long' });
    document.getElementById('calendarTitle').innerText = `${monthName} ${year}`;

    document.querySelectorAll('.calendar-days').forEach(container => {

        let id = String(container.dataset.id);
        let bookedDates = bookingGrouped[id] || [];

        let fullDates = bookedDates
            .filter(d => d.type === 'full')
            .map(d => d.date);

        let checkoutDates = bookedDates
            .filter(d => d.type === 'checkout')
            .map(d => d.date);

        container.innerHTML = '';

        let daysInMonth = new Date(year, monthIndex + 1, 0).getDate();

        for (let i = 1; i <= daysInMonth; i++) {

            let day = i.toString().padStart(2, '0');
            let fullDate = `${year}-${monthNum}-${day}`;

            let isFull = fullDates.includes(fullDate);
            let isCheckout = checkoutDates.includes(fullDate);
            let isPast = fullDate < today;
            let isToday = fullDate === today;

            container.innerHTML += `
                <div class="calendar-day 
                    ${isFull ? 'booked' : ''} 
                    ${isCheckout ? 'checkout' : ''}
                    ${isPast ? 'past' : ''}
                    ${isToday ? 'today' : ''}">
                                    
                    ${i}
                </div>
            `;
        }

    });
}

function prevMonth(){
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth(){
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function goToDate(){
    let month = parseInt(document.getElementById('inputMonth').value);
    let year = document.getElementById('inputYear').value;

    if(month >= 0 && month <= 11 && year){
        currentDate = new Date(year, month, 1);
        renderCalendar();
    }
}

document.addEventListener('DOMContentLoaded', function(){
    renderCalendar();
});
</script>

@endsection