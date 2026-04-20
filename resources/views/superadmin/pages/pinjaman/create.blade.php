@extends('superadmin.components.template')

@section('title')
    Ajukan Pinjaman
@endsection

@section('content')


<div class="card shadow-sm">
    <div class="card-header bg-success text-white">
        💰 Input Pinjaman Offline
    </div>

    {{-- Pesan Error --}}
    <div class="card-body">
        @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

        <form action="{{ route('superadmin.pinjaman.store') }}" method="POST">
            @csrf

            {{-- ANGGOTA --}}
            <div class="mb-3">
                <label>Anggota</label>
                <select name="user_id" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- JENIS PINJAMAN --}}
            <div class="mb-3">
                <label>Jenis Pinjaman</label>
                <select name="loan_type_id" class="form-control" required>
                    @foreach($loanTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- JUMLAH --}}
            <div class="mb-3">
                <label>Jumlah Pinjaman</label>
                <input type="number" name="total_amount" class="form-control" required>
            </div>

            {{-- TENOR --}}
            <div class="mb-3">
                <label>Tenor (bulan)</label>
                <input type="number" name="tenor" class="form-control" required>
            </div>

            <button class="btn btn-success">Ajukan</button>
             <a href="{{ route('superadmin.pinjaman.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
        </form>

    </div>
</div>

@endsection
