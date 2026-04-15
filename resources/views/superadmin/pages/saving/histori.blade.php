@extends('superadmin.components.template')

@section('title')
    Histori Transaksi
@endsection

@section('content')
    <table class="table">
    <tr>
        <th>Nama</th>
        <th>Jenis</th>
        <th>Tipe</th>
        <th>Jumlah</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>

    @foreach($transactions as $trx)
    <tr>
        <td>{{ $trx->user->name }}</td>
        <td>{{ $trx->savingType->name }}</td>
        <td>{{ $trx->transaction_type }}</td>
        <td>{{ number_format($trx->amount) }}</td>
        <td>{{ $trx->status }}</td>
        <td>
            <form action="{{ route('saving.destroy', $trx->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">Hapus</button>
            </form>
        </td>
    </tr>
    @endforeach
</table>
@endsection
