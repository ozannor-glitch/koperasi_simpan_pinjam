@extends('superadmin.components.template')

@section('title','Edit Penarikan')

@section('content')
<div class="card p-4">
    <form action="{{ route('superadmin.penarikan.update',$penarikan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="number" name="jumlah" class="form-control mb-2"
               value="{{ $penarikan->jumlah }}">

        <input type="text" name="bank" class="form-control mb-2"
               value="{{ $penarikan->bank }}">

        <input type="text" name="no_rekening" class="form-control mb-2"
               value="{{ $penarikan->no_rekening }}">

        <input type="text" name="nama_rekening" class="form-control mb-2"
               value="{{ $penarikan->nama_rekening }}">

        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
