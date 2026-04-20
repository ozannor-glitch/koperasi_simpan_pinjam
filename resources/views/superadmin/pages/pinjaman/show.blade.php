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
                <form action="{{ route('superadmin.pinjaman.updateStatus', $loan->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status" value="rejected">
                    <button class="btn btn-danger">❌ Tolak</button>
                </form>

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
            📂 Lihat Dokumen
        </a>
    </div>
@endif
<hr>

<h5>📊 Jadwal Angsuran</h5>

<table class="table table-bordered table-striped">
    <thead class="table-success">
        <tr>
            <th>Angsuran ke</th>
            <th>Pokok</th>
            <th>Bunga</th>
            <th>Total</th>
            <th>Sisa</th>
            <th>Status</th>
        </tr>
    </thead>

    <tbody>
        @forelse($loan->installments as $i)
        <tr>
            <td>{{ $i->installment_number }}</td>
            <td>Rp {{ number_format($i->principal ?? 0,0,',','.') }}</td>
            <td>Rp {{ number_format($i->interest ?? 0,0,',','.') }}</td>
            <td>Rp {{ number_format($i->amount_due,0,',','.') }}</td>
            <td>Rp {{ number_format($i->remaining_balance ?? 0,0,',','.') }}</td>

            <td>
                <span class="badge bg-{{ $i->status == 'paid' ? 'success' : 'warning' }}">
                    {{ $i->status }}
                </span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center text-muted">
                Belum ada jadwal angsuran
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

    </div>
</div>

@endsection
