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

        public function store(Request $request)
        {
            $request->validate([
                'KTP' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                'nik' => 'required|string|max:200',
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role' => 'required',
            ]);

            // 🔥 upload file
            $ktpPath = null;
            if ($request->hasFile('KTP')) {
                $ktpPath = $request->file('KTP')->store('ktp', 'public');
            }

            User::create([
                'KTP' => $ktpPath, // ✅ simpan path file
                'nik' => $request->nik,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'status' => 'active'
            ]);

            return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan');
        }

    public function edit($id)
        {
            $user = User::findOrFail($id);
            return view('superadmin.pages.user.edit', compact('user'));
        }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'KTP' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'nik' => 'required|string|max:200',
            'name' => 'required',
            'email' => "required|email|unique:users,email,$id",
            'role' => 'required',
        ]);

        $data = [
            'nik' => $request->nik,
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // 🔥 update KTP kalau ada
        if ($request->hasFile('KTP')) {
            $data['KTP'] = $request->file('KTP')->store('ktp', 'public');
        }

        if (!empty($request->password)) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')->with('success', 'User berhasil diupdate');
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
