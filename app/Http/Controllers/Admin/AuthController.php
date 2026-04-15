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
        $credentials = $request->only("email", "password");

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Menggunakan redirect()->intended() sangat disarankan agar user kembali
            // ke halaman yang mereka tuju sebelum dipaksa login
            switch ($user->role) {
                case 'super_admin':
                    return redirect()->intended('/superadmin/dashboard');
                case 'admin':
                    return redirect()->intended('/adminkeuangan/dashboard');
                case 'teller':
                    return redirect()->intended('/teller/dashboard');
                default:
                    Auth::logout();
                    return redirect()->route('admin.login')->withErrors('Role tidak Valid');
            }
        }

        // Pindahkan ini ke luar blok IF agar jika login gagal, user mendapat feedback
        return back()->withErrors(['email' => 'Email atau Password salah'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Sesuaikan redirect logout ke rute login admin
        return redirect()->route('admin.login')->with('success', 'Logout berhasil!');
    }
}
