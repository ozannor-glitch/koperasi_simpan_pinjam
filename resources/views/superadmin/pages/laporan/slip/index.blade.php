@extends('superadmin.components.template')

@section('content')
    <h3>Slip Angsuran</h3>

<p>Nama: {{ $loan->user->name }}</p>
<p>Total: Rp {{ number_format($loan->total_amount) }}</p>
<p>Tenor: {{ $loan->tenor }} bulan</p>
@endsection
