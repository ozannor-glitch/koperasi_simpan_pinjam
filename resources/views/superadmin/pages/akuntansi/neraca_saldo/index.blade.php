@extends('superadmin.components.template')

@section('content')

<h4>Neraca Saldo</h4>

<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Kode</th>
            <th>Akun</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
        </tr>
    </thead>

    <tbody>

    @foreach($data as $row)
    <tr>
        <td>{{ $row['account']->code }}</td>
        <td>{{ $row['account']->name }}</td>

        <td class="text-end">
            {{ $row['debit'] > 0 ? number_format($row['debit'],0,',','.') : '' }}
        </td>

        <td class="text-end">
            {{ $row['credit'] > 0 ? number_format($row['credit'],0,',','.') : '' }}
        </td>
    </tr>
    @endforeach

    </tbody>

    <tfoot>
        <tr class="fw-bold">
            <td colspan="2">TOTAL</td>
            <td class="text-end">{{ number_format($totalDebit,0,',','.') }}</td>
            <td class="text-end">{{ number_format($totalCredit,0,',','.') }}</td>
        </tr>
    </tfoot>

</table>

@endsection
