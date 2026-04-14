<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view("superadmin.pages.auth.login");
    }


   public function authenticate(Request $request)
{
    $credentials= $request->only("email","password");
        if(Auth::attempt($credentials)){
            $request->session()->regenerate();
            $user=Auth::user();
            switch($user->role){
                case'super_admin':
                    return redirect('/superadmin/dashboard');
                case'admin':
                    return redirect('/adminkeuangan/dashboard');
                case'teller':
                    return redirect('/teller/dashboard');
                default:
                    Auth::logout();
                    return redirect('/auth/login')->withErrors('Role tidak Valid');
            }
                return back()->withErrors('Email atau Password salah');
            }
}
public function logout(Request $request)
{
    Auth::logout();

    $request->session()->invalidate(); // 🔥 hapus session
    $request->session()->regenerateToken(); // 🔥 keamanan CSRF

    return redirect('/auth/login')->with('success', 'Logout berhasil!');
}
}

