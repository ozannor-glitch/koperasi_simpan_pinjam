@extends('superadmin.components.template')

@section('title','Setor Simpanan')

@section('content')

<div class="card shadow mt-4">
    <div class="card-header">
        <strong>💰 Setor Simpanan</strong>
    </div>

        <div class="card-body">
            @if ($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first() }}
        </div>
    @endif
        <form action="{{ route('superadmin.saving.store') }}" method="POST">
            @csrf

            {{-- 🔥 PILIH ANGGOTA --}}
            <div class="mb-3">
                <label>Anggota</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">
                            {{ $u->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔥 JENIS SIMPANAN --}}
            <div class="mb-3">
                <label>Jenis Simpanan</label>
                <select id="saving_type_id" name="saving_type_id" class="form-control" required disabled>
                    <option value="">-- Pilih Jenis --</option>

                    @foreach($savingTypes as $type)
                        <option value="{{ $type->id }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- 🔥 NOMINAL --}}
            <div class="mb-3">
                <label>Jumlah Setoran</label>
                <input type="text" id="amount" name="amount" class="form-control" placeholder="Masukkan nominal" required>
            </div>

            {{-- 🔥 BUTTON --}}
            <button class="btn btn-success">Simpan</button>
            <a href="{{ route('superadmin.saving.index') }}" class="btn btn-secondary">Kembali</a>

        </form>

    </div>
</div>

{{-- ================= SCRIPT ================= --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    const userSelect = document.getElementById('user_id');
    const typeSelect = document.getElementById('saving_type_id');

    userSelect.addEventListener('change', function () {

        if (this.value) {
            typeSelect.disabled = false;
        } else {
            typeSelect.disabled = true;
            typeSelect.value = "";
        }
    });

});
</script>

{{-- 🔥 FORMAT RUPIAH --}}
<script>
const input = document.getElementById('amount');

input.addEventListener('input', function () {
    let angka = this.value.replace(/[^\d]/g, '');
    this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
});
</script>

@endsection
