@extends('superadmin.components.template')

@section('title')
    Detail Pinjaman
@endsection

@section('content')

<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        📄 Detail Pinjaman
    </div>

    <div class="card-body">

        <table class="table table-bordered">
            <tr>
                <th>Nama Anggota</th>
                <td>{{ $loan->user->name }}</td>
            </tr>

            <tr>
                <th>Jenis Pinjaman</th>
                <td>{{ $loan->loanType->name }}</td>
            </tr>

            <tr>
                <th>Total Pinjaman</th>
                <td>Rp {{ number_format($loan->total_amount) }}</td>
            </tr>

            <tr>
                <th>Tenor</th>
                <td>{{ $loan->tenor }} bulan</td>
            </tr>

            <tr>
                <th>Status</th>
                <td>
                    <span class="badge bg-{{ $loan->status == 'approved' ? 'success' : 'warning' }}">
                        {{ $loan->status }}
                    </span>
                </td>
            </tr>

            <tr>
                <th>Tanggal</th>
                <td>{{ $loan->created_at->format('d M Y') }}</td>
            </tr>
        </table>

        <a href="{{ route('superadmin.pinjaman.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>

                {{-- 🔥 AKSI ADMIN --}}
        <div class="mt-3 d-flex gap-2">

            @if($loan->status == 'pending')

                {{-- APPROVE --}}
                <form action="{{ route('superadmin.pinjaman.updateStatus', $loan->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="approved">
                    <button class="btn btn-success">✅ Setujui</button>
                </form>

                {{-- REJECT --}}
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $loan->id }}">
                        Tolak
                    </button>


            @endif

        </div>

        <hr>

<h5>📄 Upload Dokumen Akad</h5>

<form action="{{ route('superadmin.pinjaman.uploadAkad', $loan->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <input type="file" name="akad_file" class="form-control mb-2" required>

    <button class="btn btn-primary">Upload Akad</button>
</form>

{{-- tampilkan file --}}
@if($loan->akad_file)
    <div class="mt-2">
        <a href="{{ asset('storage/'.$loan->akad_file) }}" target="_blank" class="btn btn-info btn-sm">
            📂 Cetak Surat Dokumen
        </a>
    </div>
@endif
<hr>

<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        📊 Jadwal Angsuran
    </div>

    <div class="card-body p-0">
        <table class="table table-bordered table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Pokok</th>
                    <th>Bunga</th>
                    <th>Total</th>
                    <th>Sisa</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>

            @forelse($loan->installments as $installment)
                <tr>
                    <td>{{ $installment->installment_number }}</td>

                    <td>Rp {{ number_format($installment->principal,0,',','.') }}</td>
                    <td>Rp {{ number_format($installment->interest,0,',','.') }}</td>
                    <td><strong>Rp {{ number_format($installment->amount_due,0,',','.') }}</strong></td>
                    <td>Rp {{ number_format($installment->remaining_balance,0,',','.') }}</td>

                    <td>
                        @if($installment->status == 'paid')
                            <span class="badge bg-success">Paid</span>
                        @else
                            <span class="badge bg-warning text-dark">Unpaid</span>
                        @endif
                    </td>

                    <td class="text-center">
                        @if($installment->status == 'unpaid')
                            <form action="{{ route('superadmin.pay', $installment->id) }}" method="POST">
                                @csrf
                                <button class="btn btn-success btn-sm">
                                    💰 Bayar
                                </button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        Belum ada angsuran
                    </td>
                </tr>
            @endforelse

            </tbody>
        </table>
    </div>
</div>

    </div>
</div>

<!-- MODAL Penolakan -->
<div class="modal fade" id="rejectModal{{ $loan->id }}">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('superadmin.pinjaman.updateStatus', $loan->id) }}">
            @csrf

            <input type="hidden" name="status" value="rejected">

            <div class="modal-content">
                <div class="modal-header">
                    <h5>Tolak Pinjaman</h5>
                </div>

                <div class="modal-body">
                    <label>Alasan Penolakan</label>
                    <textarea name="rejection_reason" class="form-control" required></textarea>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-danger">Kirim</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
