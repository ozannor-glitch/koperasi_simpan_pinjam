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

<h4>Data Saldo Anggota</h4>

<table class="table table-bordered">
    <tr>
        <th>Nama</th>
        <th>Jenis</th>
        <th>Saldo</th>
        <th>Aksi</th> {{-- 🔥 baru --}}
    </tr>

   @foreach($savings as $s)
<tr>
    <td>{{ $s->user->name }}</td>
    <td>{{ $s->savingType->name }}</td>
    <td>Rp {{ number_format($s->balance) }}</td>

    <!-- 🔥 FORM TARIK -->
    <td>
        @if($errors->any())
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ $errors->first() }}',
                confirmButtonColor: '#d33'
            });
        </script>
        @endif
        <form action="{{ route('superadmin.saving.withdraw') }}" method="POST" style="display:flex; gap:5px;">
            @csrf

            <input type="hidden" name="user_id" value="{{ $s->user_id }}">
            <input type="hidden" name="saving_type_id" value="{{ $s->saving_type_id }}">

            <input type="number"
       name="amount"
       class="form-control form-control-sm"
       style="width:100px;"
       min="{{ $s->savingType->minimum_withdraw }}"
       max="{{ $s->balance }}"
       required>

            <button class="btn btn-warning btn-sm">Tarik</button>
        </form>
    </td>

    <!-- 🔥 STATUS / APPROVE -->
    <td>
        @if($s->status == 'pending')

            <form action="{{ route('superadmin.saving.approve', $s->id) }}" method="POST" style="display:inline;">
                @csrf
                <button class="btn btn-success btn-sm">Approve</button>
            </form>

            <form action="{{ route('superadmin.saving.reject', $s->id) }}" method="POST" style="display:inline;">
                @csrf
                <button class="btn btn-secondary btn-sm">Reject</button>
            </form>

        @else
            <span class="badge
                bg-{{ $s->status == 'approved' ? 'success' : ($s->status == 'rejected' ? 'danger' : 'warning') }}">
                {{ $s->status }}
            </span>
        @endif
    </td>
</tr>
@endforeach
</table>
@endsection
