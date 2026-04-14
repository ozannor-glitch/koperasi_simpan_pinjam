@extends('superadmin.components.template')

@section('title')
    User Admin
@endsection

@section('content')

<div class="card mt-4 shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Data User</span>

        <a href="{{ route('user.create') }}" class="btn btn-primary btn-sm">
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
                    <td>{{ $user->KTP }}</td>
                    <td>{{ $user->nik }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <span class="badge bg-info">
                            {{ $user->role }}
                        </span>
                    </td>
                    <td>

                        <a href="{{ route('user.edit', $user->id) }}"
                           class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        @if($user->role != 'super_admin')
                        <form action="{{ route('user.destroy', $user->id) }}"
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
