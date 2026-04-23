@extends('superadmin.components.template')

@section('title','Tambah Penarikan')

@section('content')
<div class="card p-4">
    <form action="{{ route('superadmin.penarikan.store') }}" method="POST">
        @csrf
    <div class="mb-3">
        <label>Anggota</label>
        <select id="user_id" class="form-control">
            <option value="">-- Pilih Anggota --</option>
            @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
    </div>
        <label>Simpanan</label>
       <select id="saving_id" name="source_id" class="form-control mb-2" disabled>

    <option value="">-- Pilih Sumber Dana --</option>

    {{-- 🔵 SIMPANAN --}}
    <optgroup label="Simpanan">

        @foreach($savings as $s)
            <option value="saving_{{ $s->id }}"
                data-user="{{ $s->user_id }}"
                {{ strtolower($s->savingType->name) == 'wajib' ? 'disabled' : '' }}>

                {{ $s->user->name }} - {{ $s->savingType->name }}
                (Saldo: Rp {{ number_format($s->balance,0,',','.') }}
                | Tersedia: Rp {{ number_format($s->available_balance ?? $s->balance,0,',','.') }})

            </option>
        @endforeach

    </optgroup>

    {{-- 🟢 PINJAMAN --}}
    <optgroup label="Pinjaman">

        @foreach($loans as $l)
            @php
                $sisa = $l->withdrawable_amount > 0
                    ? $l->withdrawable_amount
                    : $l->total_amount;
            @endphp

            <option value="loan_{{ $l->id }}"
                data-user="{{ $l->user_id }}">

                {{ $l->user->name }} - Pinjaman
                (Sisa: Rp {{ number_format($sisa,0,',','.') }})

            </option>
        @endforeach

    </optgroup>

</select>

        <input type="text" id="jumlah" name="jumlah" class="form-control mb-2" placeholder="Nominal">

        <input type="text" name="bank" class="form-control mb-2" placeholder="Bank">
        <input type="text" name="no_rekening" class="form-control mb-2" placeholder="No Rekening">
        <input type="text" name="nama_rekening" class="form-control mb-2" placeholder="Nama Rekening">

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('superadmin.penarikan.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const userSelect = document.getElementById('user_id');
    const savingSelect = document.getElementById('saving_id');

    userSelect.addEventListener('change', function () {
        let userId = this.value;

        // reset pilihan
        savingSelect.value = "";

        if (!userId) {
            savingSelect.disabled = true;
            return;
        }

        // 🔥 AKTIFKAN DROPDOWN
        savingSelect.disabled = false;

        let options = savingSelect.querySelectorAll('option');

        options.forEach(opt => {
            if (!opt.value) return;

            if (opt.dataset.user == userId) {
                opt.style.display = 'block';
            } else {
                opt.style.display = 'none';
            }
        });

    });

});
</script>

{{-- Script Nominal --}}
<script>
const jumlahInput = document.getElementById('jumlah');

jumlahInput.addEventListener('input', function(e) {
    let angka = this.value.replace(/[^\d]/g, '');
    this.value = formatRupiah(angka);
});

function formatRupiah(angka) {
    return angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}
</script>
@endsection
