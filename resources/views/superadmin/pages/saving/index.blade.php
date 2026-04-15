@extends('superadmin.components.template')

@section('title')
    Simpanan
@endsection

@section('content')

<h3>Setor Simpanan</h3>

<form action="{{ route('superadmin.saving.store') }}" method="POST">
    @csrf

    <select name="user_id" class="form-control mb-2">
        @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
        @endforeach
    </select>

    <select name="saving_type_id" class="form-control mb-2">
         @foreach($savingTypes as $type)
        <option value="{{ $type->id }}">{{ $type->name }}</option>
    @endforeach
    </select>

    <input type="number" name="amount" class="form-control mb-2" placeholder="Jumlah">

    <button class="btn btn-primary">Setor</button>
</form>

<hr>

{{-- 🔥 TOTAL SALDO --}}
<h4>Total Saldo: Rp {{ number_format($savings->sum('balance')) }}</h4>

{{-- 🔥 TABEL SALDO --}}
<h4>Data Saldo Anggota</h4>

<table class="table table-bordered">
    <tr>
        <th>Nama</th>
        <th>Jenis</th>
        <th>Saldo</th>
    </tr>

    @foreach($savings as $s)
    <tr>
        <td>{{ $s->user->name }}</td>
        <td>{{ $s->savingType->name }}</td>
        <td>Rp {{ number_format($s->balance) }}</td>
    </tr>
    @endforeach
</table>

<hr>

{{-- 🔥 HISTORI TRANSAKSI --}}
<h4>Histori Transaksi</h4>

<table class="table table-striped">
    <tr>
        <th>Nama</th>
        <th>Jenis</th>
        <th>Tipe</th>
        <th>Jumlah</th>
        <th>Aksi</th>
    </tr>

    @foreach($transactions as $trx)
    <tr>
        <td>{{ $trx->user->name }}</td>
        <td>{{ $trx->savingType->name }}</td>
        <td>
            <span class="badge bg-{{ $trx->transaction_type == 'setor' ? 'success' : 'danger' }}">
                {{ $trx->transaction_type }}
            </span>
        </td>
        <td>Rp {{ number_format($trx->amount) }}</td>
        <td>
            <form action="{{ route('superadmin.saving.destroy', $trx->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
{{ $savings->links() }}
@endsection
