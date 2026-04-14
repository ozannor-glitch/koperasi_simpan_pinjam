<?php

namespace App\Http\Controllers\visitor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Hash;

class AuthController extends Controller
{

    public function index()
    {
        return view('visitor.auth.login'); // sesuaikan dengan view kamu
    }

    public function login()
    {
        return view('visitor.auth.login');
    }

    public function register()
    {
        return view('visitor.auth.register');
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'ktp' => 'required|image'
        ]);

        $ktp = $request->file('ktp')->store('ktp', 'public');

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'ktp' => $ktp
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil');
    }

    public function loginPost(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Email atau password salah');
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
}
