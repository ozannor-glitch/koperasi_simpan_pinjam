@extends('superadmin.components.template')

@section('title')
    User Admin
@endsection

@section('content')

<div class="card mt-4 shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Data User</span>

        <a href="{{ route('superadmin.user.create') }}" class="btn btn-primary btn-sm">
            + Tambah User
        </a>
    </div>

    <div class="card-body">

        <table class="table table-striped table-hover">
            <thead class="table-dark">
                <tr>
                    <th>KTP</th>
                    <th>KK</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th width="150">Aksi</th>
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

            {{-- KOLOM AKSI --}}
            <td>
                @if($user->role != 'super_admin')

                    <a href="{{ route('superadmin.user.edit', $user->id) }}"
                    class="btn btn-warning btn-sm">
                        Edit
                    </a>

                    <form action="{{ route('superadmin.user.destroy', $user->id) }}"
                        method="POST"
                        style="display:inline;">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm"
                                onclick="return confirm('Yakin hapus?')">
                            Hapus
                        </button>
                    </form>

                @endif
            </td>

        </tr>
        @endforeach
        </tbody>
        </table>

    </div>
</div>

@endsection
