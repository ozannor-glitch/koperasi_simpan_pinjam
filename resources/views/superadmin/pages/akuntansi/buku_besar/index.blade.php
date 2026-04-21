@extends('superadmin.components.template')

@section('content')

<h4>Buku Besar</h4>

<form method="GET">
    <select name="account_id" onchange="this.form.submit()" class="form-control mb-3">
        <option value="">-- Pilih Akun --</option>
        @foreach($accounts as $acc)
            <option value="{{ $acc->id }}"
                {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                {{ $acc->code }} - {{ $acc->name }}
            </option>
        @endforeach
    </select>
</form>

@if($account)

<h5>{{ $account->name }}</h5>

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

    @foreach($items as $item)
    <tr>
        <td>{{ $item->journal->date }}</td>
        <td>{{ $item->journal->description }}</td>

        <td>
            {{ $item->debit > 0 ? number_format($item->debit,0,',','.') : '' }}
        </td>

        <td>
            {{ $item->credit > 0 ? number_format($item->credit,0,',','.') : '' }}
        </td>

        <td>
            <strong>
                {{ number_format($item->running_balance,0,',','.') }}
            </strong>
        </td>
    </tr>
    @endforeach

    </tbody>
</table>

@endif

@endsection
