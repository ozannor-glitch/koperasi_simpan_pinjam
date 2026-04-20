@extends('superadmin.components.template')

@section('title')
    Tabel Pinjaman
@endsection

@section('content')

<div class="card shadow mt-4">
    <div class="card-header d-flex justify-content-between">
        <span>Data Pinjaman</span>
        <a href="{{ route('superadmin.pinjaman.create') }}" class="btn btn-primary btn-sm">
            + Ajukan Pinjaman
        </a>
    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>User</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Tenor</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($loans as $loan)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $loan->user->name ?? '-' }}</td>
                    <td>{{ $loan->loanType->name ?? '-' }}</td>
                    <td>Rp {{ number_format($loan->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $loan->tenor }} bulan</td>

                    <td>
                        @if($loan->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($loan->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @else
                            <span class="badge bg-danger">Rejected</span>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('superadmin.pinjaman.show', $loan->id) }}" class="btn btn-info btn-sm">
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center">Belum ada data</td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>

@endsection
