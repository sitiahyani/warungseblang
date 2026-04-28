@extends('layouts.kasir')

@section('content')

@if(session('error'))
    <div style="
        background:#fee2e2;
        color:#991b1b;
        padding:12px 16px;
        border-radius:12px;
        margin-bottom:15px;
        font-size:14px;
        font-weight:500;
        border:1px solid #fecaca;
    ">
        ❌ {{ session('error') }}
    </div>
@endif

<div class="pos-wrapper">
    <main class="main-content">

        {{-- HEADER --}}
        <div class="page-header">
            <div>
                <h1>Transaksi Sewa Aula atau Wedding</h1>
                <p>Kelola pesanan wedding dan booking acara</p>
            </div>

            @php
                $color = '#000000';
                if(isset($shift) && $shift){
                    $now = now()->format('H:i:s');
                    $selesai = $shift->waktu_selesai;
                    if($now > $selesai){
                        $color = '#ef4444';
                    }
                }
            @endphp

            <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
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

                <div class="lh-sm">
                    <div class="fw-semibold">
                        {{ $karyawan->nama_karyawan ?? 'Kasir' }}
                    </div>
                    <small style="color: {{ $color }}">
                        @if($shift)
                            Shift {{ \Carbon\Carbon::parse($shift->waktu_mulai)->format('H.i') }}
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

        <form action="{{ route('penjualan.wedding.simpan') }}" method="POST">
            @csrf

            {{-- TOP FORM --}}
            <div class="content-grid">

                {{-- LEFT --}}
                <div>
                    <div class="search-wrapper-row">
                        <div class="search-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search" placeholder="Cari paket wedding...">
                        </div>

                        <button type="button"
                                class="btn-pelanggan"
                                onclick="openCustomerModal()">
                            + Pelanggan
                        </button>
                    </div>

                    <div class="menu-grid">
                        @forelse($layanan as $item)
                            <div class="menu-card wedding-card"
                                data-search="{{ strtolower($item->nama_layanan . ' ' . $item->deskripsi) }}"
                                onclick="pilihPaket('{{ $item->nama_layanan }}', {{ $item->harga }}, {{ $item->id_layanan }})">

                                <div class="menu-image">
                                    <img src="{{ asset('storage/layanan/'.$item->gambar) }}"
                                        alt="{{ $item->nama_layanan }}">
                                </div>

                                <h4>{{ $item->nama_layanan }}</h4>

                                <div class="price">
                                    Rp {{ number_format($item->harga,0,',','.') }}
                                </div>

                                <small>{{ $item->deskripsi }}</small>
                            </div>
                        @empty
                            <p>Paket wedding kosong.</p>
                        @endforelse
                    </div>
                </div>

                {{-- RIGHT --}}
                <div class="cart-box">
                    <div class="cart-header">
                        <span>💍 Keranjang Wedding</span>
                    </div>

                    <div id="cartItems">
                        <p class="text-muted small">Belum ada paket dipilih</p>
                    </div>

                    {{-- FORM BOOKING --}}
                    <div class="booking-grid">

                        <div class="booking-field full">
                            <label>Nama Pelanggan</label>
                            <div class="customer-cart-wrapper position-relative">
                                <input type="hidden" name="id_pelanggan" id="id_pelanggan">

                                <input type="text"
                                    id="customer_name_cart"
                                    placeholder="Ketik nama pelanggan"
                                    autocomplete="off">

                                <div id="customer_suggestions_cart" class="customer-suggestions-cart"></div>
                            </div>
                        </div>

                        <div class="booking-field full">
                            <label>No. Telepon</label>
                            <input type="text"
                                name="no_telp"
                                id="no_telp"
                                readonly>
                        </div>

                        <div class="booking-field">
                            <label>Tanggal Acara</label>
                            <input type="date" name="tanggal_acara" min="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="booking-field">
                            <label>Jam Acara</label>
                            <input type="time" name="jam_acara" required>
                        </div>

                        <div class="booking-field full">
                            <label>Tipe Acara</label>
                            <select name="id_tipe" required>
                                @foreach($tipe as $t)
                                    <option value="{{ $t->id_tipe }}">{{ $t->nama_tipe }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="booking-field full">
                            <label>Keterangan</label>
                            <textarea name="keterangan" rows="3"
                                placeholder="Catatan khusus acara"></textarea>
                        </div>

                        <div class="booking-field">
                            <label>Metode Bayar</label>
                            <select name="metode_bayar">
                                <option value="tunai">Tunai</option>
                                <option value="kredit">Kredit</option>
                            </select>
                        </div>

                        <div class="booking-field">
                            <label>Nominal Bayar</label>
                            <input type="number"
                                name="nominal_bayar"
                                placeholder="Masukkan nominal bayar">
                        </div>

                    </div>

                    <input type="hidden" name="harga_paket" id="harga_paket">
                    <input type="hidden" name="id_layanan" id="id_layanan">

                    {{-- TOTAL + TOMBOL MASIH DI DALAM CART --}}
                    <div class="cart-summary">
                        <div class="total">
                            <span>Total</span>
                            <span id="totalDisplay">Rp 0</span>
                        </div>

                        <button type="submit" class="btn-pay">
                            Simpan & Bayar
                        </button>
                    </div>
                </div>
            </div>
        </form>

        {{-- KALENDER --}}
        <div class="calendar-box mt-4">

            <div class="calendar-title d-flex justify-content-between align-items-center">

                <button onclick="prevMonth()">⬅</button>

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
                    <button onclick="goToDate()">Go</button>
                </div>

                <button onclick="nextMonth()">➡</button>

            </div>

            <span id="calendarTitle" class="d-block text-center mt-2"></span>

            <div class="calendar-grid" id="calendarGrid"></div>

            <div class="calendar-legend">
                <div class="legend-item">
                    <span class="legend-box available"></span>
                    <span>Tersedia</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box indoor"></span>
                    <span>Indoor Terbooking</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box outdoor"></span>
                    <span>Outdoor Terbooking</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box full"></span>
                    <span>Full Booking (Indoor + Outdoor)</span>
                </div>
                <div class="legend-item">
                    <span class="legend-box past"></span>
                    <span>Lewat</span>
                </div>
            </div>

        </div>

    </main>
</div>

@if(session('print_data'))
<div id="receiptModal" class="popup-overlay" style="display:flex;">
    <div class="popup-card" id="print-area">

        <!-- HEADER -->
        <div class="popup-header text-center">
            <h2>Warung Seblang</h2>
            <small>Wedding & Event</small>
        </div>

        <!-- BODY -->
        <div class="popup-body">

            <h3 style="text-align:center;margin-bottom:10px;">STRUK PEMBAYARAN</h3>

            <div class="info-row">
                <span>No. Transaksi</span>
                <span>{{ session('print_data.kode') }}</span>
            </div>

            <div class="info-row">
                <span>Pelanggan</span>
                <span>{{ session('print_data.pelanggan') }}</span>
            </div>

            <div class="info-row">
                <span>Tanggal</span>
                <span>{{ \Carbon\Carbon::parse(session('print_data.tanggal'))->format('d M Y H:i') }}</span>
            </div>

            <div class="info-row">
                <span>Kasir</span>
                <span>{{ $karyawan->nama_karyawan ?? 'Kasir' }}</span>
            </div>

            <hr>

            <!-- 🔥 DETAIL ITEM -->
            <div class="info-row">
                <span>Paket Wedding</span>
                <span>Rp {{ number_format(session('print_data.total'),0,',','.') }}</span>
            </div>

            <hr>

            <!-- 🔥 TOTAL -->
            <div class="info-row total-row">
                <span>TOTAL</span>
                <span>Rp {{ number_format(session('print_data.total'),0,',','.') }}</span>
            </div>

            <div class="info-row">
                <span>Bayar</span>
                <span>Rp {{ number_format(session('print_data.bayar'),0,',','.') }}</span>
            </div>

            <div class="info-row">
                <span>Sisa</span>
                <span>Rp {{ number_format(session('print_data.sisa'),0,',','.') }}</span>
            </div>

            <div class="info-row">
                <span>Status</span>
                <span>{{ strtoupper(session('print_data.status')) }}</span>
            </div>

            <p class="thanks">Terima kasih atas kepercayaan Anda 🙏</p>

        </div>

        <!-- FOOTER -->
        <div class="popup-footer no-print">
            <button class="btn-print" onclick="printStruk()">Cetak</button>
            <button class="btn-close" onclick="closeStruk()">Tutup</button>
        </div>

    </div>
</div>
@endif

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
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Inter',sans-serif;background:#f4f6f9;color:#111827;line-height:1.5;}
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:12px;}
.page-header h1{font-size:28px;font-weight:700;margin:0;}
.page-header p{font-size:14px;color:#6b7280;margin:4px 0 0;}
.section-divider{border:none;border-top:2px solid #e5e7eb;margin:15px 0 18px;}
.content-grid{display:grid;grid-template-columns:1fr 380px;gap:22px;align-items:start;}
.search-wrapper-row{display:flex;gap:12px;margin-bottom:18px;}
.search-wrapper{flex:1;position:relative;}
.search-wrapper i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:14px;}
.search-wrapper input{width:100%;height:42px;padding:10px 14px 10px 38px;border-radius:14px;border:1px solid #d1d5db;font-size:14px;background:#fff;transition:.2s;}
.search-wrapper input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.btn-pelanggan{height:42px;padding:0 20px;border:none;border-radius:14px;background:#2563eb;color:#fff;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap;transition:.2s;}
.btn-pelanggan:hover{background:#1d4ed8;transform:translateY(-1px);}
.btn-pay{margin-top:14px;width:100%;padding:12px;border:none;border-radius:14px;background:#16a34a;color:#fff;font-size:14px;font-weight:600;cursor:pointer;transition:.2s;}
.btn-pay:hover{background:#15803d;transform:translateY(-1px);}
.btn-print{flex:1;background:#2563eb;color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer;font-weight:500;}
.btn-print:hover{opacity:.9;}
.btn-close{flex:1;background:#e5e7eb;border:none;padding:10px;border-radius:8px;cursor:pointer;font-weight:500;}
.btn-close:hover{background:#d1d5db;}
.menu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:18px;}
.menu-card{background:#fff;padding:14px;border-radius:18px;text-align:center;box-shadow:0 6px 18px rgba(0,0,0,.06);transition:.25s;cursor:pointer;border:1px solid #f1f5f9;}
.menu-card:hover{transform:translateY(-4px);box-shadow:0 12px 24px rgba(0,0,0,.1);}
.menu-image{width:100%;height:120px;border-radius:14px;overflow:hidden;margin-bottom:12px;background:#f1f5f9;}
.menu-image img{width:100%;height:100%;object-fit:cover;transition:.3s;}
.menu-card:hover .menu-image img{transform:scale(1.05);}
.menu-card h4{font-size:15px;margin:8px 0 4px;font-weight:600;}
.price{color:#2563eb;font-weight:700;margin-bottom:6px;font-size:15px;}
.cart-box{background:#fff;padding:20px;border-radius:20px;box-shadow:0 8px 24px rgba(0,0,0,.06);position:sticky;top:20px;font-size:13px;border:1px solid #eef2f7;}
.cart-header{font-size:18px;font-weight:700;margin-bottom:16px;padding-bottom:12px;border-bottom:2px solid #f1f5f9;}
.cart-item-selected{display:flex;justify-content:space-between;align-items:center;padding:12px;border:1px solid #e5e7eb;border-radius:14px;background:#f9fafb;margin-bottom:14px;}
.cart-summary{margin-top:16px;padding-top:14px;border-top:2px dashed #d1d5db;}
.total{display:flex;justify-content:space-between;align-items:center;padding-top:10px;font-size:18px;font-weight:800;}
.booking-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;}
.booking-field{display:flex;flex-direction:column;gap:6px;}
.booking-field.full{grid-column:span 2;}
.booking-field label{font-size:12px;font-weight:600;color:#374151;}
.booking-field input,.booking-field select,.booking-field textarea{width:100%;padding:10px 12px;border-radius:12px;border:1px solid #d1d5db;font-size:13px;background:#fff;transition:.2s;}
.booking-field input:focus,.booking-field select:focus,.booking-field textarea:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.booking-field textarea{resize:vertical;min-height:70px;}
#customer_name_cart,#no_telp{background:#f9fafb;}
.customer-cart-wrapper{position:relative;}
.customer-cart-wrapper input{width:100%;padding:10px 12px;border-radius:12px;border:1px solid #d1d5db;font-size:13px;background:#f9fafb;}
.customer-cart-wrapper input:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.customer-suggestions-cart{position:absolute;top:calc(100% + 6px);left:0;width:100%;background:#fff;border:1px solid #e5e7eb;border-radius:14px;max-height:220px;overflow-y:auto;z-index:99999;box-shadow:0 10px 25px rgba(0,0,0,.08);display:none;}
.customer-suggestions-cart.show{display:block;}
.customer-suggestions-cart .suggestion-item{padding:12px;cursor:pointer;transition:.2s;border-bottom:1px solid #f3f4f6;}
.customer-suggestions-cart .suggestion-item:last-child{border-bottom:none;}
.customer-suggestions-cart .suggestion-item:hover{background:#eff6ff;}
.customer-name{font-size:14px;font-weight:600;color:#111827;}
.customer-phone{font-size:12px;color:#6b7280;margin-top:3px;}
.popup-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;align-items:center;justify-content:center;z-index:99999;}
.popup-card{width:400px;max-width:90%;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.2);}
.popup-header{padding:18px 20px;background:#1e293b;color:#fff;}
.popup-header h2{font-size:18px;margin:0;}
.popup-body{padding:20px;display:flex;flex-direction:column;gap:14px;}
.popup-body input,.popup-body textarea{width:100%;padding:12px 14px;border-radius:12px;border:1px solid #d1d5db;font-size:14px;}
.popup-body input:focus,.popup-body textarea:focus{outline:none;border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,.1);}
.popup-footer{display:flex;gap:12px;padding:16px 20px 20px;}
.calendar-box{background:#fff;border-radius:20px;padding:20px;box-shadow:0 8px 20px rgba(0,0,0,.06);}
.calendar-title{font-size:16px;font-weight:700;margin-bottom:20px;}
.calendar-title button{background:#f1f5f9;border:none;width:36px;height:36px;border-radius:10px;cursor:pointer;font-size:18px;}
.calendar-title button:hover{background:#e2e8f0;}
.calendar-grid{display:grid;grid-template-columns:repeat(7,1fr);gap:10px;margin-top:15px;}
.calendar-day{background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:10px;text-align:center;min-height:85px;font-size:12px;}
.calendar-day.indoor-booked{background:#22c55e;color:#fff;border:none;}
.calendar-day.outdoor-booked{background:#3b82f6;color:#fff;border:none;}
.calendar-day.full-booked{background:#ef4444;color:#fff;border:none;}
.calendar-day.past{background:#e5e7eb;color:#9ca3af;pointer-events:none;opacity:.6;}
.day-number{font-size:16px;font-weight:700;margin-bottom:6px;}
.booking-info{font-size:10px;margin-top:6px;font-weight:500;}
.calendar-legend{margin-top:20px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:center;padding-top:16px;border-top:1px solid #e5e7eb;}
.legend-item{display:flex;align-items:center;gap:8px;font-size:12px;}
.legend-box{width:20px;height:20px;border-radius:6px;}
.legend-box.available{background:#fff;border:2px solid #d1d5db;}
.legend-box.indoor{background:#22c55e;}
.legend-box.outdoor{background:#3b82f6;}
.legend-box.full{background:#ef4444;}
.legend-box.past{background:#9ca3af;}
.receipt-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);display:flex;align-items:center;justify-content:center;z-index:9999;}
.receipt-box{width:380px;max-width:90%;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,.2);}
.receipt-header{background:#1e293b;color:#fff;text-align:center;padding:20px;}
.receipt-header h2{margin:0 0 5px;font-size:20px;}
.receipt-body{padding:20px;font-size:13px;}
.info-row{display:flex;justify-content:space-between;margin-bottom:10px;padding:4px 0;}
.total-row{font-weight:800;font-size:16px;border-top:2px dashed #e5e7eb;padding-top:12px;margin-top:8px;}
.thanks{text-align:center;margin-top:20px;padding-top:16px;border-top:1px solid #e5e7eb;font-size:12px;color:#6b7280;}
.receipt-footer{display:flex;gap:12px;padding:16px 20px 20px;}
@media (max-width:1024px){.content-grid{grid-template-columns:1fr;}.cart-box{position:relative;top:0;}}
@media (max-width:640px){.menu-grid{grid-template-columns:repeat(auto-fill,minmax(140px,1fr));}.booking-grid{grid-template-columns:1fr;}.booking-field.full{grid-column:span 1;}.calendar-grid{gap:6px;}.calendar-day{min-height:65px;padding:6px;}.day-number{font-size:14px;}}
.text-muted{color:#6b7280;}.small{font-size:11px;}.fw-bold{font-weight:700;}.text-center{text-align:center;}.d-flex{display:flex;}.align-items-center{align-items:center;}.justify-content-between{justify-content:space-between;}.gap-2{gap:8px;}.mt-2{margin-top:8px;}.mt-4{margin-top:16px;}.mb-2{margin-bottom:8px;}.rounded-circle{border-radius:50%;}.shadow-sm{box-shadow:0 1px 2px 0 rgba(0,0,0,.05);}.bg-white{background:#fff;}.px-3{padding-left:12px;padding-right:12px;}.py-2{padding-top:8px;padding-bottom:8px;}.me-2{margin-right:8px;}.lh-sm{line-height:1.2;}.fw-semibold{font-weight:600;}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const customerCart = document.getElementById('customer_name_cart');
    const suggestionsCart = document.getElementById('customer_suggestions_cart');
    const customerId = document.getElementById('id_pelanggan');
    const noTelp = document.getElementById('no_telp');

    const customerSearchUrl = "{{ route('kasir.pelanggan.cari') }}";

    customerCart?.addEventListener('input', async function () {
        let keyword = this.value.trim();

        customerId.value = '';
        noTelp.value = '';

        if (keyword.length < 1) {
            suggestionsCart.innerHTML = '';
            suggestionsCart.classList.remove('show');
            return;
        }

        try {
            const response = await fetch(
                `${customerSearchUrl}?keyword=${encodeURIComponent(keyword)}`
            );

            const data = await response.json();

            let html = '';

            if (!data.length) {
                html = `
                    <div class="suggestion-item">
                        <div class="customer-name">Pelanggan tidak ditemukan</div>
                    </div>
                `;
            } else {
                data.forEach(item => {
                    html += `
                        <div class="suggestion-item"
                            data-id="${item.id_pelanggan}"
                            data-name="${item.nama_pelanggan}"
                            data-phone="${item.no_hp ?? ''}">
                            <div class="customer-name">${item.nama_pelanggan}</div>
                            <div class="customer-phone">${item.no_hp ?? '-'}</div>
                        </div>
                    `;
                });
            }

            suggestionsCart.innerHTML = html;
            suggestionsCart.classList.add('show');

        } catch (error) {
            console.error('Autocomplete wedding error:', error);
        }
    });

    suggestionsCart?.addEventListener('click', function (e) {
        const item = e.target.closest('.suggestion-item');

        if (!item || !item.dataset.id) return;

        customerId.value = item.dataset.id;
        customerCart.value = item.dataset.name;
        noTelp.value = item.dataset.phone;

        suggestionsCart.innerHTML = '';
        suggestionsCart.classList.remove('show');
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.customer-cart-wrapper')) {
            suggestionsCart.classList.remove('show');
        }
    });

});

// ================= PILIH PAKET =================
let selectedPackage = null;
let totalHarga = 0;

function pilihPaket(nama, harga, id){
    selectedPackage = {
        nama: nama,
        harga: harga,
        id: id
    };

    totalHarga = harga;

    document.getElementById('id_layanan').value = id;

    const cartItems = document.getElementById('cartItems');
    const totalDisplay = document.getElementById('totalDisplay');
    const hargaPaket = document.getElementById('harga_paket');

    if(cartItems){
        cartItems.innerHTML = `
            <div class="cart-item-selected">
                <div>
                    <strong>${nama}</strong>
                    <div class="small text-muted">Paket Wedding</div>
                </div>
                <div class="fw-bold">
                    Rp ${harga.toLocaleString('id-ID')}
                </div>
            </div>
        `;
    }

    totalDisplay.innerText = `Rp ${harga.toLocaleString('id-ID')}`;
    hargaPaket.value = harga;
}

// cari
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search');

    searchInput?.addEventListener('keyup', function () {
        const keyword = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.wedding-card');

        cards.forEach(card => {
            const text = card.dataset.search || '';

            if (keyword === '' || text.includes(keyword)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

function closeStruk(){
    const modal = document.getElementById('receiptModal');
    if(modal) modal.remove();
}

function printStruk(){
    const printArea = document.getElementById('print-area').innerHTML;
    const old = document.body.innerHTML;

    document.body.innerHTML = printArea;
    window.print();
    document.body.innerHTML = old;
    location.reload();
}

function openCustomerModal() {
    const modal = document.getElementById('customer-popup');
    if (modal) {
        modal.style.display = 'flex';
    }
}

function closeCustomerPopup() {
    const modal = document.getElementById('customer-popup');
    if (modal) {
        modal.style.display = 'none';
    }
}

async function saveCustomer() {
    const nama = document.getElementById('nama_pelanggan').value.trim();
    const noHp = document.getElementById('no_hp').value.trim();
    const alamat = document.getElementById('alamat').value.trim();

    if (!nama) {
        alert('Nama pelanggan wajib diisi');
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
                no_hp: noHp,
                alamat: alamat
            })
        });

        const result = await response.json();

        if (result.success) {
            document.getElementById('id_pelanggan').value = result.data.id_pelanggan;
            document.getElementById('customer_name_cart').value = result.data.nama_pelanggan;
            document.getElementById('no_telp').value = result.data.no_hp ?? '';

            closeCustomerPopup();

            document.getElementById('nama_pelanggan').value = '';
            document.getElementById('no_hp').value = '';
            document.getElementById('alamat').value = '';
        } else {
            alert(result.message || 'Gagal menyimpan pelanggan');
        }

    } catch (error) {
        console.error(error);
        alert('Terjadi error saat menyimpan pelanggan');
    }
}

let currentDate = new Date();
let bookingDates = @json($bookingDates);

function renderCalendar() {

    const grid = document.getElementById('calendarGrid');
    const title = document.getElementById('calendarTitle');

    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();

    document.getElementById('inputMonth').value = month;
    document.getElementById('inputYear').value = year;

    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    const monthName = currentDate.toLocaleString('id-ID', { month: 'long' });

    title.innerText = `${monthName} ${year}`;

    grid.innerHTML = '';

    // spasi awal
    for (let i = 0; i < firstDay; i++) {
        grid.innerHTML += `<div></div>`;
    }

    for (let i = 1; i <= totalDays; i++) {

        let day = i.toString().padStart(2, '0');
        let monthFix = (month + 1).toString().padStart(2, '0');

        let fullDate = `${year}-${monthFix}-${day}`;

        let today = new Date().toISOString().split('T')[0];
        let isPast = fullDate < today;
        
        // Cek booking untuk tanggal ini
        let bookings = bookingDates[fullDate] || [];
        let hasIndoor = bookings.includes('indor');
        let hasOutdoor = bookings.includes('outdor');
        
        let statusClass = '';
        let statusText = '';
        
        if (isPast) {
            statusClass = 'past';
            statusText = 'Lewat';
        } else if (hasIndoor && hasOutdoor) {
            statusClass = 'full-booked';
            statusText = 'Full Booking';
        } else if (hasIndoor) {
            statusClass = 'indoor-booked';
            statusText = 'Indoor Terbooking';
        } else if (hasOutdoor) {
            statusClass = 'outdoor-booked';
            statusText = 'Outdoor Terbooking';
        } else {
            statusClass = '';
            statusText = 'Tersedia';
        }

        grid.innerHTML += `
            <div class="calendar-day ${statusClass}">
                <div class="day-number">${i}</div>
                <small>${statusText}</small>
                ${!isPast && (hasIndoor || hasOutdoor) ? 
                    `<div class="booking-info">
                        ${hasIndoor ? '🏠 Indoor<br>' : ''}
                        ${hasOutdoor ? '🌲 Outdoor' : ''}
                    </div>` : ''}
            </div>
        `;
    }
}

function prevMonth(){
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth(){
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

document.addEventListener('DOMContentLoaded', function(){
    renderCalendar();
});

function goToDate(){
    let month = parseInt(document.getElementById('inputMonth').value);
    let year = document.getElementById('inputYear').value;

    if(month >= 0 && month <= 11 && year){
        currentDate = new Date(year, month, 1);
        renderCalendar();
    }
}

function hitungKembalian() {

    let bayar = document.getElementById('bayar').value;
    let totalText = document.getElementById('totalDisplay').innerText;

    // ambil angka dari "Rp 500.000"
    let total = totalText.replace(/[^\d]/g, '');

    bayar = parseInt(bayar || 0);
    total = parseInt(total || 0);

    let kembali = bayar - total;

    document.getElementById('kembalian').value =
        "Rp " + (kembali > 0 ? kembali : 0).toLocaleString('id-ID');
}

// trigger
document.getElementById('bayar')?.addEventListener('input', hitungKembalian);

</script>

@endsection