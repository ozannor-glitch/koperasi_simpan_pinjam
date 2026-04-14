@extends('visitor.layout.app')

@section('content')

        @include('visitor.components.navbar')


        @include('visitor.pages.hero')


    <div class="section">
        @include('visitor.components.tentangkami_2')
    </div>

    <div class="section">
        @include('visitor.components.simpan_pinjam')
    </div>

        @include('visitor.components.FAQ_2')


        @include('visitor.components.tentangkami_3')


@endsection
