@extends("superadmin.components.template")

@section('content')
    <h3>Laporan Anggota</h3>

<table border="1" width="100%" cellpadding="5">
    <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Status</th>
    </tr>

    @foreach($data as $u)
    <tr>
        <td>{{ $u->name }}</td>
        <td>{{ $u->email }}</td>
        <td>{{ $u->status }}</td>
    </tr>
    @endforeach
</table>
@endsection
