@extends('superadmin.components.template')

@section('title')
    User Admin
@endsection

@section('content')

<div class="card card-modern mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">👤 Data User</h5>

        <a href="{{ route('superadmin.user.create') }}" class="btn btn-green btn-sm">
            + Tambah User
        </a>
    </div>

    <div class="card-body">

        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>KTP</th>
                    <th>Status</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>
        @foreach($users as $user)
        <tr>

            {{-- KOLOM KTP --}}
            <td>
                @if($user->KTP)
                    <img src="{{ asset('storage/' . $user->KTP) }}"
                        style="width:70px; height:90px; object-fit:cover;"
                        class="img-thumbnail">
                @else
                    <span class="text-muted">Tidak ada</span>
                @endif
            </td>

            {{-- KTP Status --}}
            <td>
            <span class="badge
            bg-{{ $user->ktp_status == 'verified' ? 'success' : ($user->ktp_status == 'rejected' ? 'danger' : 'warning') }}">
                {{ $user->ktp_status }}
            </span>
            </td>

            {{-- KOLOM NIK --}}
            <td>{{ $user->nik }}</td>

            {{-- KOLOM NAMA --}}
            <td>{{ $user->name }}</td>

            {{-- KOLOM EMAIL --}}
            <td>{{ $user->email }}</td>

            {{-- KOLOM ROLE --}}
            <td>
                <span class="badge bg-info">
                    {{ $user->role }}
                </span>
            </td>

            {{-- KOLOM Status --}}
            <td>
                <span class="badge bg-info">
                    {{ $user->status }}
                </span>
            </td>

            {{-- KOLOM AKSI --}}
            <td>
                <div class="d-flex flex-wrap gap-1">

                <a href="{{ route('superadmin.user.edit', $user->id) }}"
                class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('superadmin.user.destroy', $user->id) }}" method="POST">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm">Hapus</button>
                </form>

                <form action="{{ route('superadmin.user.verify', $user->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-success btn-sm">✔</button>
                </form>

                <form action="{{ route('superadmin.user.reject', $user->id) }}" method="POST">
                    @csrf
                    <button class="btn btn-secondary btn-sm">✖</button>
                </form>

                <button class="btn btn-info btn-sm"
                    data-bs-toggle="modal"
                    data-bs-target="#modalUser{{ $user->id }}">
                    Riwayat
                </button>

                </div>
                </td>
        </tr>

        @endforeach
        </tbody>
        </table>
        @foreach($users as $user)

        {{-- Modal Popup Riwayat --}}
        <div class="modal fade" id="modalUser{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content card-modern">

                    <div class="modal-header" style="background:#dcfce7;">
                        <h5 class="modal-title">
                            📜 Riwayat {{ $user->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($user->transactions as $trx)
                                <tr>
                                    <td>
                                        <span class="badge
                                            bg-{{ $trx->transaction_type == 'setor' ? 'success' :
                                                ($trx->transaction_type == 'tarik' ? 'danger' : 'warning') }}">
                                            {{ $trx->transaction_type }}
                                        </span>
                                    </td>

                                    <td>Rp {{ number_format($trx->amount) }}</td>

                                    <td>
                                        <span class="badge
                                            bg-{{ $trx->status == 'pending' ? 'warning' : 'success' }}">
                                            {{ $trx->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Tidak ada transaksi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>

@endforeach
    </div>
</div>

@endsection
