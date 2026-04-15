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
            if (password_verify($request->password, $user->password)) {
                // jika verifikasi berhasil, buat token untuk user dan kembalikan token tersebut dalam response
                $token = $user->createToken('user-token');
                return response()->json([
                    'token' => $token,
                    'message' => 'Login berhasil.',
                ], 200);
            } else {
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
            // Validasi file: harus gambar, maksimal 2MB
            "KTP" => "required|image|mimes:jpeg,png,jpg|max:2048|unique:users,KTP",
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Proses Upload Gambar
        $ktpPath = null;
        if ($request->hasFile('KTP')) {
            // Menyimpan file ke folder 'public/ktp'
            $ktpPath = $request->file('KTP')->store('ktp', 'public');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'nik' => $request->nik,
            'KTP' => $ktpPath, // Simpan path/nama filenya saja di database
            'role' => 'user',
            'status' => 'Menunggu Verifikasi',
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

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
