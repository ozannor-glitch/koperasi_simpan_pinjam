@extends('superadmin.components.template')

@section('title')
    Buku Besar
@endsection

@section('content')
<h4>Buku Besar: {{ $account->name }}</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Keterangan</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Saldo</th>
        </tr>
    </thead>
    <tbody>
        @php $saldo = 0; @endphp

        @foreach($items as $item)
        <tr>
            <td>{{ $item->journal->date }}</td>
            <td>{{ $item->journal->description }}</td>
            <td>{{ number_format($item->debit, 0, ',', '.') }}</td>
            <td>{{ number_format($item->credit, 0, ',', '.') }}</td>
            <td>{{ number_format($item->running_balance, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
