@extends('superadmin.components.template')

@section('title', 'Data Penarikan')

@section('content')
<div class="container mt-4">

    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Data Penarikan</h5>
        </div>

        <div class="card-body">

            {{-- ALERT --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- HEADER: BUTTON + FILTER --}}
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">

            {{-- LEFT --}}
            <a href="{{ route('superadmin.penarikan.create') }}" class="btn btn-success">
                + Tambah Penarikan
            </a>

            {{-- RIGHT --}}
            <form method="GET"
                action="{{ route('superadmin.penarikan.index') }}"
                class="d-flex align-items-center gap-2">

                <select name="bank" class="form-select" style="width:160px;">
                    <option value="">Semua Bank</option>
                    <option value="BCA" {{ request('bank')=='BCA'?'selected':'' }}>BCA</option>
                    <option value="BRI" {{ request('bank')=='BRI'?'selected':'' }}>BRI</option>
                    <option value="BNI" {{ request('bank')=='BNI'?'selected':'' }}>BNI</option>
                    <option value="Mandiri" {{ request('bank')=='Mandiri'?'selected':'' }}>Mandiri</option>
                </select>

                <select name="status" class="form-select" style="width:160px;">
                    <option value="all">Semua Status</option>
                    <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
                    <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Approved</option>
                    <option value="rejected" {{ request('status')=='rejected'?'selected':'' }}>Rejected</option>
                    <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
                </select>

                <button class="btn btn-primary">Filter</button>

            </form>

        </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>User</th>
                        <th>Saldo</th>
                        <th>Jumlah</th>
                        <th>Diterima</th>
                        <th>Bank</th>
                        <th>Rekening</th>
                        <th>Status</th>
                        <th>Aksi</th>

                        </tr>
                    </thead>

                    <tbody>
                        @foreach($data as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->kode_penarikan }}</td>
                            <td>{{ $item->user->name }}</td>
                            <td>Rp {{ number_format($item->memberSaving->balance, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->jumlah_diterima, 0, ',', '.') }}</td>
                          <td>
                                <span class="badge bg-dark">
                                    {{ $item->bank ?: '-' }}
                                </span>
                            </td>

                            <td>
                                <div>
                                    <strong>{{ $item->no_rekening ?: '-' }}</strong><br>
                                    <small class="text-muted">
                                        {{ $item->nama_rekening ?: '-' }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                @if($item->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($item->status == 'approved')
                                    <span class="badge bg-info">Approved</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge bg-danger">Rejected</span>
                                @elseif($item->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @endif
                            </td>

                            <td>
                                {{-- APPROVE --}}
                                @if($item->status == 'pending')
                                    <form action="{{ route('superadmin.penarikan.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button onclick="return confirm('Approve penarikan ini?')" class="btn btn-success btn-sm">
                                            Approve
                                        </button>
                                    </form>

                                    {{-- REJECT --}}
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                        Reject
                                    </button>
                                @endif

                                {{-- COMPLETE --}}
                                @if($item->status == 'approved')
                                    <form action="{{ route('superadmin.penarikan.complete', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button onclick="return confirm('Tandai selesai?')" class="btn btn-primary btn-sm">
                                            Complete
                                        </button>
                                    </form>
                                @endif

                                     <a href="{{ route('superadmin.penarikan.edit',$item->id) }}" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <form action="{{ route('superadmin.penarikan.destroy',$item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Hapus</button>
                                </form>

                            </td>
                        </tr>

                        {{-- MODAL REJECT --}}
                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form action="{{ route('superadmin.penarikan.reject', $item->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Alasan Penolakan</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <textarea name="catatan" class="form-control" required placeholder="Masukkan alasan..."></textarea>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<script>
$(document).ready(function() {
    $('.table').DataTable();
});

$('.table').DataTable({
    "order": [],
});
</script>
@endsection
