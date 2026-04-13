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
    $validator = Validator::make($request->all(), [
        "email" => "required|email",
        "password" => "required"
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $credentials = $request->only("email", "password");

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate(); // 🔥 penting untuk security

        return redirect()->to("/superadmin/dashboard")
            ->withSuccess("Selamat Datang! " . Auth::user()->name);
    }

    return redirect()->back()->withErrors("Email atau password salah!");
}
public function logout()
    {
        Auth::logout();
        return redirect()->to("/auth/login")->withSuccess("Logout berhasil!");
    }
}

