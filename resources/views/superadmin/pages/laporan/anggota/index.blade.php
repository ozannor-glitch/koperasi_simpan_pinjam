@extends('superadmin.components.template')

@section('content')
    <h4>Laporan Anggota</h4>

<table class="table">
<tr>
    <th>Nama</th>
    <th>Email</th>
    <th>Status</th>
</tr>

@foreach($users as $u)
<tr>
    <td>{{ $u->name }}</td>
    <td>{{ $u->email }}</td>
    <td>{{ $u->status }}</td>
</tr>
@endforeach
</table>
@endsection
