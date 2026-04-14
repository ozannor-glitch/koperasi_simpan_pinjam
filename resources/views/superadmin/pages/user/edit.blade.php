@extends('superadmin.components.template')

@section('title')
    Edit User
@endsection

@section('content')

    @if(session('success'))
    <div class="toast show position-fixed top-0 end-0 m-3 bg-success text-white">
        <div class="p-3">
            {{ session('success') }}
        </div>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul style="margin-bottom:0;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

   <form action="{{ route('user.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')

         <div class="form-group">
                        <label for="KTP">Foto KTP</label>
                        <input type="file" class="form-control-file @error('KTP') is-invalid @enderror" id="KTP" name="KTP">
                        <small class="form-text text-muted">Biarkan Kosong jika tidak ingin mengganti foto.</small>
                        @if($user->KTP)
                            <div class="mt-2">
                                <label>gambar saat ini:</label>
                                <img src="{{ Storage::url($user->KTP) }}" alt="Current Logo" class="img-thumbnail" style="max-height: 100px;">
                            </div>
                        @endif
                        @error('KTP')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
        </div>

         <div class="form-group mb-3">
            <label for="nik">NIK <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nik') is-invalid @enderror" id="nik" name="nik"
                value="{{ old('nik', $user->nik) }}" required>
            @error('nik')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         <div class="form-group mb-3">
            <label for="name">Nama<span class="text-danger">*</span></label>
            <textarea class="form-control @error('name') is-invalid @enderror" id="name" name="name" rows="10"
                required>{{ old('name', $user->name ?? '') }}</textarea>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group mb-3">
            <label for="email">email <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                value="{{ old('email', $user->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

         <div class="form-group mb-3">
            <label for="password">password <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('password') is-invalid @enderror" id="password" name="password"
                value="{{ old('password', $user->password) }}" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <select name="role">
        <option value="admin_keuangan" {{ $user->role == 'admin_keuangan' ? 'selected' : '' }}>Admin Keuangan</option>
        <option value="admin_umum" {{ $user->role == 'admin_umum' ? 'selected' : '' }}>Admin Umum</option>
        <option value="teller" {{ $user->role == 'teller' ? 'selected' : '' }}>Teller</option>
        <option value="anggota" {{ $user->role == 'anggota' ? 'selected' : '' }}>anggota</option>
        </select>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="/superadmin/user/" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
@endsection

