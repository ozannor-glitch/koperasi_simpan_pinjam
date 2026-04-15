<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     public function index()
    {
        $users = User::latest()->get();
        return view('superadmin.pages.user.index', compact('users'));
    }

    public function create()
    {
        return view('superadmin.pages.user.create');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        if ($user->role == 'super_admin') {
            return back()->withErrors('Super Admin tidak bisa diedit');
        }

        return view('superadmin.pages.user.edit', compact('user'));
    }

public function store(Request $request)
{
    // validasi umum
    $rules = [
        'name' => 'required',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'role' => 'required',
    ];

    // 🔥 tambahan kalau anggota
    if ($request->role == 'anggota') {
        $rules['KTP'] = 'required|image|mimes:jpeg,png,jpg|max:2048';
        $rules['nik'] = 'required|string|max:200';
    }

    $request->validate($rules);

    // upload KTP
    $ktpPath = null;
    if ($request->hasFile('KTP')) {
        $ktpPath = $request->file('KTP')->store('ktp', 'public');
    }

    User::create([
        'KTP' => $ktpPath,
        'nik' => $request->nik,
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role' => $request->role,
        'status' => 'active'
    ]);

    return redirect()->route('superadmin.user.index')->with('success', 'User berhasil ditambahkan');
}

public function update(Request $request, $id)
{
    $user = User::findOrFail($id);

    if ($user->role == 'super_admin') {
    return back()->withErrors('Super Admin tidak bisa diedit');
}

    $rules = [
        'name' => 'required',
        'email' => "required|email|unique:users,email,$id",
        'role' => 'required',
    ];

    // 🔥 kalau anggota
    if ($request->role == 'anggota') {
        $rules['nik'] = 'required|string|max:200';
        $rules['KTP'] = 'nullable|image|mimes:jpeg,png,jpg|max:2048';
    }

    $request->validate($rules);

    $data = [
        'nik' => $request->nik,
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
    ];

    if ($request->hasFile('KTP')) {
        $data['KTP'] = $request->file('KTP')->store('ktp', 'public');
    }

    if (!empty($request->password)) {
        $data['password'] = Hash::make($request->password);
    }

    $user->update($data);

    return redirect()->route('superadmin.user.index')->with('success', 'User berhasil diupdate');
}

public function destroy($id)
{
        $user = User::findOrFail($id);

        if (Auth::id() == $id) {
            return back()->withErrors('Tidak bisa hapus akun sendiri');
        }

        if ($user->role == 'super_admin') {
            return back()->withErrors('Super Admin tidak bisa dihapus');
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }
}
