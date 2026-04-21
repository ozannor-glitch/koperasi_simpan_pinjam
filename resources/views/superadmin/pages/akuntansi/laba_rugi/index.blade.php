@extends('superadmin.components.template')

@section('content')

<h4>Laporan Laba Rugi</h4>

<table class="table table-bordered">

    <thead class="table-dark">
        <tr>
            <th>Akun</th>
            <th class="text-end">Jumlah</th>
        </tr>
    </thead>

    <tbody>

    <tr class="table-success">
        <td colspan="2"><strong>PENDAPATAN</strong></td>
    </tr>

    @foreach($data as $row)
        @if($row['account']->type == 'income')
        <tr>
            <td>{{ $row['account']->name }}</td>
            <td class="text-end">
                {{ number_format($row['amount'],0,',','.') }}
            </td>
        </tr>
        @endif
    @endforeach

    <tr class="fw-bold">
        <td>Total Pendapatan</td>
        <td class="text-end">
            {{ number_format($totalIncome,0,',','.') }}
        </td>
    </tr>

    <tr class="table-danger">
        <td colspan="2"><strong>BEBAN</strong></td>
    </tr>

    @foreach($data as $row)
        @if($row['account']->type == 'expense')
        <tr>
            <td>{{ $row['account']->name }}</td>
            <td class="text-end">
                {{ number_format($row['amount'],0,',','.') }}
            </td>
        </tr>
        @endif
    @endforeach

    <tr class="fw-bold">
        <td>Total Beban</td>
        <td class="text-end">
            {{ number_format($totalExpense,0,',','.') }}
        </td>
    </tr>

    <tr class="table-primary fw-bold">
        <td>LABA BERSIH</td>
        <td class="text-end">
            {{ number_format($netIncome,0,',','.') }}
        </td>
    </tr>

    </tbody>

</table>

@endsection
