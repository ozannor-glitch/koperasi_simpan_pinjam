@extends('superadmin.components.template')

@section('title','Tambah Penarikan')

@section('content')
<div class="card p-4">
    <form action="{{ route('superadmin.penarikan.store') }}" method="POST">
        @csrf

        <label>Simpanan</label>
        <select name="member_saving_id" class="form-control mb-2">
            @foreach($savings as $s)
                <option value="{{ $s->id }}"
                    {{ strtolower($s->savingType->name) == 'wajib' ? 'disabled' : '' }}>
                    {{ $s->user->name }} - {{ $s->savingType->name }}
                    (Saldo: Rp {{ number_format($s->balance,0,',','.') }}
                    | Tersedia: Rp {{ number_format($s->available_balance ?? $s->balance,0,',','.') }})
                </option>
            @endforeach
        </select>

        <input type="number" name="jumlah" class="form-control mb-2" placeholder="Jumlah">

        <input type="text" name="bank" class="form-control mb-2" placeholder="Bank">
        <input type="text" name="no_rekening" class="form-control mb-2" placeholder="No Rekening">
        <input type="text" name="nama_rekening" class="form-control mb-2" placeholder="Nama Rekening">

        <button class="btn btn-success">Simpan</button>
        <a href="{{ route('superadmin.penarikan.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </form>
</div>
@endsection
