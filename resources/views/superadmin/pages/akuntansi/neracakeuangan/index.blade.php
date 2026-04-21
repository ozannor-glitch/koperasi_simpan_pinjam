@extends('superadmin.components.template')

@section('content')

<h4>Neraca Keuangan</h4>

<div class="row">

<!-- ASET -->
<div class="col-md-6">
    <h5>Aset</h5>
    <table class="table table-bordered">
        @foreach($assets as $row)
        <tr>
            <td>{{ $row['account']->name }}</td>
            <td class="text-end">
                {{ number_format($row['balance'],0,',','.') }}
            </td>
        </tr>
        @endforeach

        <tr class="fw-bold">
            <td>Total Aset</td>
            <td class="text-end">
                {{ number_format($totalAsset,0,',','.') }}
            </td>
        </tr>
    </table>
</div>

<!-- LIABILITAS + EKUITAS -->
<div class="col-md-6">

    <h5>Liabilitas</h5>
    <table class="table table-bordered">
        @foreach($liabilities as $row)
        <tr>
            <td>{{ $row['account']->name }}</td>
            <td class="text-end">
                {{ number_format($row['balance'],0,',','.') }}
            </td>
        </tr>
        @endforeach

        <tr class="fw-bold">
            <td>Total Liabilitas</td>
            <td class="text-end">
                {{ number_format($totalLiability,0,',','.') }}
            </td>
        </tr>
    </table>

    <h5>Ekuitas</h5>
    <table class="table table-bordered">
        @foreach($equities as $row)
        <tr>
            <td>{{ $row['account']->name }}</td>
            <td class="text-end">
                {{ number_format($row['balance'],0,',','.') }}
            </td>
        </tr>
        @endforeach

        <tr>
            <td>Laba Tahun Berjalan</td>
            <td class="text-end">
                {{ number_format($laba,0,',','.') }}
            </td>
        </tr>

        <tr class="fw-bold">
            <td>Total Ekuitas</td>
            <td class="text-end">
                {{ number_format($totalEquity,0,',','.') }}
            </td>
        </tr>
    </table>

</div>

</div>

@endsection
