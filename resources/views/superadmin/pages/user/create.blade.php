@extends('superadmin.components.template')

@section('title')
    Tambah User
@endsection

@section('content')



<div class="card mt-4 shadow">
    <div class="card-header">
        Form Tambah User
    </div>

    <div class="card-body">

        <form action="{{ route('superadmin.user.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group mb-3">
            <label for="KTP">Foto KTP <span class="text-danger">*</span></label>
            <input type="file" class="form-control @error('KTP') is-invalid @enderror" id="KTP" name="KTP"
                required>
            @error('KTP')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>

            <div class="form-group mb-3">
            <label for="nik">NIK <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik"
                value="{{ old('nik') }}" required>
            @error('nik')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>

            <div class="form-group mb-3">
            <label for="name">Nama <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                value="{{ old('name') }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>

            <div class="form-group mb-3">
            <label for="email">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>

            <div class="form-group mb-3">
            <label for="password">Password <span class="text-danger">*</span></label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                value="{{ old('password') }}" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="admin_keuangan">Admin Keuangan</option>
                    <option value="teller">Teller</option>
                    <option value="anggota">Anggota</option> <!-- 🔥 tambah ini -->
                </select>
            </div>

            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control">
                    <option value="calon">Calon</option>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option> <!-- 🔥 tambah ini -->
                </select>
            </div>

            <button class="btn btn-primary">Simpan</button>
            <a href="{{ route('superadmin.user.index') }}" class="btn btn-secondary">Kembali</a>

        </form>

    </div>
</div>

@endsection
