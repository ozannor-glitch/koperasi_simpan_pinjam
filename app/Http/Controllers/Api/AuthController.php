<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // ambil data email dan password dari request

        $credentials = $request->only("email", "password");
        // cari user berdasarkan email yang diinputkan
        $user = User::where("email", $credentials["email"])->first();

        if ($user) {
            // verifikasi password yang diinputkan dengan password yang ada di database
            if(password_verify($request->password, $user->password)) {
                // jika verifikasi berhasil, buat token untuk user dan kembalikan token tersebut dalam response
                $token = $user->createToken('user-token');
                return response()->json([
                    'token' => $token,
                    'message' => 'Login berhasil.',
                ], 200);
            }else {
                // jika verifikasi gagal, kembalikan response dengan status 401 (Unauthorized) dan pesan error
                return response()->json([
                    'token' => null,
                    'message' => 'Email atau password salah.',
                ], 401);
            }
        } else {
            return response()->json([
                'token' => null,
                'message' => 'Email atau password salah.',
            ], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:6",
            "nik" => "required|unique:users,nik",
            "KTP" => "required|unique:users,KTP",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'nik' => $request->nik,
            'KTP' => $request->KTP,
            'role' => 'user',
            'status' => 'active',
        ]);

        $token = $user->createToken('user-token');

        return response()->json([
            'token' => $token,
            'message' => 'Registrasi berhasil.',
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Anda berhasil logout.'
        ]);
    }

}
//cek
