@extends('layouts.kasir')

@section('title', 'Cash Drawer')

@section('content')
<div class="pos-wrapper">
    <main class="main-content">

        <div class="page-header">
            <div>
                <h1>Cash Drawer</h1>
                <p>Isikan cash drawer disini</p>
            </div>

            @php
                $color = '#000000';
                if(isset($shift) && $shift && $shiftAktif){
                    $now = now()->format('H:i:s');
                    $selesai = $shift->waktu_selesai;
                    if($now > $selesai){
                        $color = '#ef4444';
                    }
                }
            @endphp

            <div class="d-flex align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                @php
                    $namaKasir = $namaKasir ?? ($karyawan->nama ?? $karyawan->username ?? 'Kasir');
                    $fotoKasir = $karyawan->foto ?? null;
                @endphp

                @if($fotoKasir)
                    <img src="{{ asset('storage/'.$fotoKasir) }}"
                        style="width:40px;height:40px;border-radius:50%;object-fit:cover;"
                        class="me-2">
                @else
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                        style="width:40px;height:40px;">
                        {{ strtoupper(substr($namaKasir, 0, 1)) }}
                    </div>
                @endif

                <div class="lh-sm">
                    <div class="fw-semibold">
                        {{ $namaKasir }}
                    </div>

                    @if($shiftAktif && $shift)
                        <small style="color: {{ $color }}">
                            Shift Aktif
                            ({{ \Carbon\Carbon::parse($shift->waktu_mulai)->format('H.i') }} -
                            {{ \Carbon\Carbon::parse($shift->waktu_selesai)->format('H.i') }})
                        </small>
                    @endif
                </div>
            </div>
        </div>

        <hr class="section-divider">

        @if(session('success'))
            <div class="alert alert-success rounded-4">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger rounded-4">{{ session('error') }}</div>
        @endif

        {{-- BELUM BUKA SHIFT --}}
        @if(!$shiftAktif)
            <div class="d-flex justify-content-center">
                <div class="cash-card text-center">
                    <div class="cash-title-row justify-content-center">
                        <div class="cash-icon">🕒</div>
                        <h3>Buka Shift</h3>
                    </div>

                    <p class="text-muted mb-4">
                        Klik tombol di bawah untuk memulai shift kasir.
                    </p>

                    <form action="{{ route('cashdrawer.bukaShift') }}" method="POST">
                        @csrf
                        <button class="btn-save-cash">
                            🔓 Buka Shift Sekarang
                        </button>
                    </form>
                </div>
            </div>
        @else

        {{-- SHIFT AKTIF --}}
        <div class="d-flex justify-content-center">
            <div class="cash-card">
                <div class="cash-title-row">
                    <div class="cash-icon">💵</div>
                    <h3>{{ ($drawer && $drawer->cash_awal > 0) ? 'Cash Akhir Kas' : 'Modal Awal Kas' }}</h3>
                </div>

                <hr>

                <form action="{{ route('cashdrawer.simpan') }}" method="POST">
                    @csrf

                    <div class="cash-grid">
                        <div>
                            <label>👤 Nama Kasir</label>
                            <input type="text" readonly value="{{ $namaKasir }}">
                        </div>
                        <div>
                            <label>📅 Tanggal & Jam</label>
                            <input type="text" readonly value="{{ now()->translatedFormat('l, d F Y \\p\\u\\k\\u\\l H.i') }}">
                        </div>
                    </div>

                    @if($drawer)
                        <div class="cash-awal-box">
                            Cash Awal:
                            <strong>Rp {{ number_format($drawer->cash_awal,0,',','.') }}</strong>
                        </div>
                    @endif

                    <label>💵 Masukkan Nominal Cash</label>
                    <input type="number"
                        name="nominal"
                        id="nominalInput"
                        placeholder="Contoh: 500000"
                        required>

                    <label class="mt-3">⚡ Pilih Nominal Cepat</label>
                    <div class="quick-grid">
                        @foreach([50000,100000,200000,300000,500000,1000000] as $nominal)
                            <button type="button"
                                class="quick-btn"
                                data-value="{{ $nominal }}">
                                Rp {{ number_format($nominal,0,',','.') }}
                            </button>
                        @endforeach
                    </div>

                    <div class="total-box">
                        <span>Total Cash Drawer:</span>
                        <strong id="totalCash">Rp 0</strong>
                    </div>

                    <button class="btn-save-cash">
                        💾 Simpan Cash Drawer
                    </button>
                </form>

                <form action="{{ route('cashdrawer.tutupShift') }}"
                    method="POST"
                    class="mt-3">
                    @csrf
                    <button class="btn-close-shift">
                        🔒 Tutup Shift
                    </button>
                </form>

                <div class="info-note">
                    ℹ️ Pastikan jumlah uang fisik sesuai nominal yang diinput sebelum menyimpan.
                </div>
            </div>
        </div>
        @endif
    </main>
</div>

<style>
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;}
.page-header h1{font-size:22px;margin:0;}
.page-header p{font-size:13px;color:#6b7280;margin:3px 0 0;}
.section-divider{border:none;border-top:1px solid #e5e7eb;margin:15px 0 25px;}
.cash-card{width:100%;max-width:560px;background:#fff;padding:24px;border-radius:20px;box-shadow:0 6px 18px rgba(0,0,0,.06);}
.cash-title-row{display:flex;align-items:center;gap:12px;margin-bottom:18px;}
.cash-icon{width:44px;height:44px;background:#16a34a;color:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;}
.cash-title-row h3{margin:0;font-size:22px;}
.cash-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;}
.cash-grid input,input[type=number]{width:100%;padding:12px;border:1px solid #d1d5db;border-radius:12px;}
.quick-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:12px 0 18px;}
.quick-btn{border:none;background:#f3f4f6;padding:12px;border-radius:12px;font-weight:600;}
.total-box{display:flex;justify-content:space-between;align-items:center;background:#f0fdf4;padding:18px;border-radius:14px;margin-bottom:16px;}
.total-box strong{font-size:34px;color:#16a34a;}
.btn-save-cash,.btn-close-shift{width:100%;border:none;padding:14px;border-radius:14px;color:#fff;font-weight:700;}
.btn-save-cash{background:#16a34a;}
.btn-close-shift{background:#dc2626;}
.cash-awal-box{background:#fef3c7;padding:12px;border-radius:12px;margin-bottom:16px;}
.info-note{margin-top:14px;background:#f9fafb;padding:12px;border-radius:12px;text-align:center;font-size:12px;color:#6b7280;}
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
    const nominalInput = document.getElementById('nominalInput');
    const totalCash = document.getElementById('totalCash');

    function updateCash(){
        let value = nominalInput ? (parseInt(nominalInput.value) || 0) : 0;
        if(totalCash){
            totalCash.innerText = 'Rp ' + value.toLocaleString('id-ID');
        }
    }

    if(nominalInput){
        nominalInput.addEventListener('input', updateCash);
    }

    document.querySelectorAll('.quick-btn').forEach(btn => {
        btn.addEventListener('click', function(){
            if(nominalInput){
                nominalInput.value = this.dataset.value;
                updateCash();
            }
        });
    });
});
</script>
@endsection