@extends('superadmin.components.template')

@section('title')
    Ajukan Pinjaman
@endsection

@section('content')


<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        💰 Input Pinjaman Offline
    </div>

    {{-- Pesan Error --}}
    <div class="card-body">
        @if(session('error'))
    <div class="alert alert-danger">
        <ul>
            {{ session('error') }}
        </ul>
    </div>
        @endif

        <form action="{{ route('superadmin.pinjaman.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card mt-3 p-3 bg-light">
                <h6>📊 Estimasi Cicilan</h6>
                <p>Total Pokok: <strong id="estimasi_pokok">Rp 0</strong></p>
                <p>Total Bunga: <strong id="estimasi_total_bunga">Rp 0</strong></p>
                <p>Angsuran / bulan: <strong id="estimasi_angsuran">Rp 0</strong></p>
                <p>Total Bayar: <strong id="estimasi_total">Rp 0</strong></p>
            </div>

            <hr>
            <h6>🏦 Data Jaminan</h6>

            <div class="mb-3">
                <label>Nama Jaminan</label>
                <input type="text" name="collateral_name" class="form-control" placeholder="Nama Jaminan" required>
            </div>

            <div class="mb-3">
                <label>Nilai Jaminan</label>
                <input type="text" name="collateral_value" class="form-control" required>
            </div>

             <div class="mb-3">
                <label>Foto Jaminan</label>
                <input type="file" name="collateral_photo">
            </div>


            {{-- ANGGOTA --}}
            <div class="mb-3">
                <label>Anggota</label>
                <select name="user_id" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- JENIS PINJAMAN --}}
            <div class="mb-3">
                <label>Jenis Pinjaman</label>
               <select name="loan_type_id" id="loan_type" class="form-control">
                    @foreach($loanTypes as $type)
                        <option value="{{ $type->id }}"
                                data-tenor="{{ $type->max_tenor_months }}"
                                data-bunga="{{ $type->interest_rate_percent }}"
                                data-method="{{ $type->interest_method }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- JUMLAH --}}
            <div class="mb-3">
                <label>Jumlah Pinjaman</label>
                <input type="text" id="jumlah" name="total_amount" class="form-control">
            </div>
            <div class="invalid-feedback" id="errorJumlah">
                Maksimal pinjaman adalah 70% dari nilai jaminan
            </div>

            {{-- TENOR --}}
            <div class="mb-3">
                <label>Tenor (bulan)</label>
                <input type="number" name="tenor" id="tenor" class="form-control">
            </div>


            <button class="btn btn-success">Ajukan</button>
             <a href="{{ route('superadmin.pinjaman.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
        </form>

    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const jaminanEl = document.querySelector('[name="collateral_value"]');
    const jumlahEl = document.getElementById('jumlah');
    const tenorEl = document.getElementById('tenor');
    const loanTypeEl = document.getElementById('loan_type');

    // 🔥 FORMAT RUPIAH
    function formatRupiah(angka) {
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // 🔥 CONVERT KE ANGKA
    function toNumber(val) {
        return parseInt(val.replace(/[^\d]/g,'')) || 0;
    }

    // 🔥 HITUNG PINJAMAN (70% DARI JAMINAN)
    function hitungPinjaman() {
        if (!jaminanEl) return;

        let nilai = toNumber(jaminanEl.value);

        if (nilai === 0) {
            jumlahEl.value = '';
            return;
        }

        let hasil = nilai * 0.7;

        jumlahEl.value = formatRupiah(Math.round(hasil));
        jumlahEl.dataset.max = hasil;

        hitungSimulasi();
    }

    // 🔥 HITUNG SIMULASI CICILAN
    function hitungSimulasi() {

        let jumlah = toNumber(jumlahEl.value);
        let tenor = parseInt(tenorEl.value) || 0;

        if (jumlah <= 0 || tenor <= 0) return;

        let selected = loanTypeEl.selectedOptions[0];

        let bunga = parseFloat(selected.getAttribute('data-bunga')) || 0;
        if (bunga > 1) bunga = bunga / 100;

        let method = selected.getAttribute('data-method') || 'flat';

        let pokok = jumlah / tenor;

        let bungaBulanan = 0;
        let cicilanPerBulan = 0;
        let total = 0;

        let totalPokok = 0;
        let totalBunga = 0;

        // 🔥 FLAT
        if (method === 'flat') {
            bungaBulanan = jumlah * bunga;
            cicilanPerBulan = pokok + bungaBulanan;

            totalPokok = pokok * tenor;
            totalBunga = bungaBulanan * tenor;

            total = totalPokok + totalBunga;
        }

        // 🔥 ANUITAS
        else if (method === 'anuitas') {
            let A = jumlah * (bunga * Math.pow(1+bunga, tenor)) /
                    (Math.pow(1+bunga, tenor) - 1);

            cicilanPerBulan = A;
            total = A * tenor;

            bungaBulanan = jumlah * bunga;

            totalPokok = jumlah;
            totalBunga = total - totalPokok;
        }

        // 🔥 DEFAULT
        else {
            bungaBulanan = jumlah * bunga;
            cicilanPerBulan = pokok + bungaBulanan;

            total = cicilanPerBulan * tenor;

            totalPokok = jumlah;
            totalBunga = total - totalPokok;
        }

        // 🔥 OUTPUT (AMAN)
        const setText = (id, val) => {
            let el = document.getElementById(id);
            if (el) el.innerText = 'Rp ' + Math.round(val).toLocaleString('id-ID');
        };

        setText('estimasi_pokok', totalPokok);
        setText('estimasi_total_bunga', totalBunga);
        setText('estimasi_angsuran', cicilanPerBulan);
        setText('estimasi_total', total);
        setText('estimasi_bunga', bungaBulanan);
    }

    // 🔥 EVENT JAMINAN
    if (jaminanEl) {
        jaminanEl.addEventListener('input', function () {
            let angka = toNumber(this.value);
            this.value = formatRupiah(angka);
            hitungPinjaman();
        });
    }

    // 🔥 EVENT JUMLAH
    jumlahEl.addEventListener('input', function () {
        let angka = toNumber(this.value);
        let max = parseInt(this.dataset.max) || 0;

        if (angka > max) {
            this.classList.add('is-invalid');
        } else {
            this.classList.remove('is-invalid');
        }

        this.value = formatRupiah(angka);
        hitungSimulasi();
    });

    // 🔥 EVENT TENOR
    tenorEl.addEventListener('input', function () {
        if (this.value > 36) {
            this.value = 36;
            alert('Tenor maksimal 36 bulan');
        }
        hitungSimulasi();
    });

    // 🔥 EVENT JENIS PINJAMAN
    loanTypeEl.addEventListener('change', function () {
        let selected = this.options[this.selectedIndex];
        tenorEl.value = selected.getAttribute('data-tenor');
        hitungSimulasi();
    });

    // 🔥 INIT
    if (loanTypeEl.selectedOptions.length > 0) {
        tenorEl.value = loanTypeEl.selectedOptions[0].getAttribute('data-tenor');
    }

    // 🔥 AUTO HITUNG SAAT LOAD
    setTimeout(() => {
        hitungSimulasi();
    }, 100);

});
</script>

@endsection
