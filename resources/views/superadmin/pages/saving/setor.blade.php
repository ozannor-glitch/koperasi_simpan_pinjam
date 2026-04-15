@extends('superadmin.components.template')

@section('title')
    Setor
@endsection

@section('content')
    <form action="{{ route('superadmin.saving.store') }}" method="POST">
    @csrf

    <select name="user_id" class="form-control">
        @foreach($users as $u)
            <option value="{{ $u->id }}">{{ $u->name }}</option>
        @endforeach
    </select>

    <select name="saving_type_id" class="form-control">
        <option value="1">Wajib</option>
        <option value="2">Sukarela</option>
    </select>

    <input type="number" name="amount" class="form-control" placeholder="Jumlah">

    <button class="btn btn-primary mt-2">Setor</button>
</form>
@endsection
