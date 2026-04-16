@extends('superadmin.components.template')

@section('title')
    Simpanan
@endsection

@section('content')

{{-- ================= FORM SETOR ================= --}}
<div class="card card-custom p-4 mb-4">
    <h4 class="mb-3">💰 Setor Simpanan</h4>

    <form action="{{ route('superadmin.saving.store') }}" method="POST">
        @csrf

        <div class="mb-2">
            <label>Anggota</label>
            <select name="user_id" class="form-control">
                @foreach($users as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-2">
            <label>Jenis Simpanan</label>
            <select name="saving_type_id" class="form-control">
                @foreach($savingTypes as $type)
                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" name="amount" class="form-control" placeholder="Masukkan jumlah">
        </div>

        <button class="btn btn-green w-100">Setor</button>
    </form>
</div>

{{-- ================= TOTAL ================= --}}
<div class="row mb-4">

    <div class="col-md-6">
        <div class="card card-custom p-3">
            <h6 class="text-muted">Total Saldo</h6>
            <h3 class="text-success fw-bold">
                Rp {{ number_format($savings->sum('balance')) }}
            </h3>
        </div>
    </div>

    <div class="col-md-6 d-flex align-items-center justify-content-end">
        <form action="{{ route('superadmin.saving.bunga') }}" method="POST"
            onsubmit="return confirm('Generate bunga untuk semua anggota?')">
            @csrf
            <button class="btn btn-warning">💰 Generate Bunga</button>
        </form>
    </div>

</div>

{{-- ALERT --}}
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

{{-- ================= SALDO ================= --}}
<div class="card card-custom p-3 mb-4">
    <h5 class="mb-3">📊 Data Saldo Anggota</h5>

    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Saldo</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        @foreach($savings as $s)
        <tr>
            <td>{{ $s->user->name }}</td>

            <td>
                <span class="badge badge-green">
                    {{ $s->savingType->name }}
                </span>
            </td>

            <td class="fw-bold text-success">
                Rp {{ number_format($s->balance) }}
            </td>

            <td>
                <form action="{{ route('superadmin.saving.withdraw') }}" method="POST" class="d-flex gap-2">
                    @csrf

                    <input type="hidden" name="user_id" value="{{ $s->user_id }}">
                    <input type="hidden" name="saving_type_id" value="{{ $s->saving_type_id }}">

                    <input type="number"
                        name="amount"
                        class="form-control form-control-sm"
                        style="width:100px;"
                        min="{{ $s->savingType->minimum_amount }}"
                        max="{{ $s->balance }}"
                        required>

                    <button class="btn btn-warning btn-sm">Tarik</button>
                </form>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

{{-- ================= TRANSAKSI ================= --}}
<div class="card card-custom p-3">
    <h5 class="mb-3">📜 Histori Transaksi</h5>

    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Jenis</th>
                <th>Tipe</th>
                <th>Jumlah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        @foreach($transactions as $trx)
        <tr>
            <td>{{ $trx->user->name }}</td>
            <td>{{ $trx->savingType->name }}</td>

            <td>
                <span class="badge
                    bg-{{ $trx->transaction_type == 'setor' ? 'success' :
                          ($trx->transaction_type == 'tarik' ? 'danger' : 'warning') }}">
                    {{ $trx->transaction_type }}
                </span>
            </td>

            <td>Rp {{ number_format($trx->amount) }}</td>

            <td>
                <span class="badge bg-{{ $trx->status == 'pending' ? 'warning' : 'success' }}">
                    {{ $trx->status }}
                </span>
            </td>

            <td>
                @if($trx->status == 'pending')
                    <form action="{{ route('superadmin.saving.approve', $trx->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success btn-sm">Approve</button>
                    </form>

                    <form action="{{ route('superadmin.saving.reject', $trx->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-secondary btn-sm">Reject</button>
                    </form>
                @else
                    <span class="text-muted">✔ selesai</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

@endsection
