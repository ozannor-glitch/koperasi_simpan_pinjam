@extends('superadmin.components.template')

@section('title')
    Tarik Tunai
@endsection

@section('content')
    <form action="{{ route('saving.withdraw') }}" method="POST">
    @csrf

    <input type="hidden" name="user_id" value="{{ $user->id }}">

    <input type="number" name="amount" placeholder="Jumlah tarik">

    <button class="btn btn-danger">Tarik</button>
</form>
@endsection
