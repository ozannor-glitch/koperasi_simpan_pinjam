@extends('superadmin.components.template')

@section('title','Edit Penarikan')

@section('content')
<div class="card p-4">

    <form action="{{ route('superadmin.penarikan.update',$penarikan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Jumlah Penarikan</label>
            <input type="number" name="jumlah" class="form-control"
                   value="{{ $penarikan->jumlah }}">
        </div>

        <div class="mb-3">
            <label>Bank</label>
            <input type="text" name="bank" class="form-control"
                   value="{{ $penarikan->bank }}">
        </div>

        <div class="mb-3">
            <label>No Rekening</label>
            <input type="text" name="no_rekening" class="form-control"
                   value="{{ $penarikan->no_rekening }}">
        </div>

        <div class="mb-3">
            <label>Nama Rekening</label>
            <input type="text" name="nama_rekening" class="form-control"
                   value="{{ $penarikan->nama_rekening }}">
        </div>

        <button class="btn btn-primary">Update</button>
         <a href="{{ route('superadmin.penarikan.index') }}" class="btn btn-secondary">
            ← Kembali
        </a>
    </form>

</div>
@endsection
