<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     * Endpoint: GET /api/users
     * Header: Authorization: Bearer {token}
     */
    public function index(Request $request)
    {
        try {
            // Query builder
            $query = User::query();

            // Filter berdasarkan role
            if ($request->has('role')) {
                $query->where('role', $request->role);
            }

            // Filter berdasarkan status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Search berdasarkan name, email, atau nik
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->input('per_page', 10);
            $users = $query->latest()->paginate($perPage);

            // Format response tanpa password
            $usersData = $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nik' => $user->nik,
                    'role' => $user->role,
                    'status' => $user->status,
                    'ktp_url' => $user->KTP ? asset('storage/' . $user->KTP) : null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => [
                    'current_page' => $users->currentPage(),
                    'data' => $usersData,
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user by ID
     * Endpoint: GET /api/users/{id}
     * Header: Authorization: Bearer {token}
     *
     * Perbedaan dengan profile:
     * - show: mengambil data user lain berdasarkan ID (untuk admin/melihat user lain)
     * - profile: mengambil data user yang sedang login (token punya siapa)
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Detail user berhasil diambil',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nik' => $user->nik,
                    'role' => $user->role,
                    'status' => $user->status,
                    'ktp_url' => $user->KTP ? asset('storage/' . $user->KTP) : null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    /**
     * Get current authenticated user profile
     * Endpoint: GET /api/profile
     * Header: Authorization: Bearer {token}
     *
     * Perbedaan dengan show:
     * - profile: otomatis mengambil data dari token yang dikirim
     * - show: harus mengirim ID user yang ingin dilihat
     */
    public function profile()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Profile user berhasil diambil',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'nik' => $user->nik,
                    'role' => $user->role,
                    'status' => $user->status,
                    'ktp_url' => $user->KTP ? asset('storage/' . $user->KTP) : null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil profile'
            ], 500);
        }
    }

    /**
     * Update the specified user (only for own profile)
     * Endpoint: PUT /api/users/{id}
     * Header: Authorization: Bearer {token}
     *
     * Untuk update profile sendiri, gunakan ID yang sama dengan token
     */
    public function update(Request $request, $id)
    {
        try {
            // Cek apakah user mengupdate profile sendiri
            $user = Auth::user();
            if ($user->id != $id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda hanya bisa mengupdate profile sendiri'
                ], 403);
            }

            $targetUser = User::findOrFail($id);

            // Validasi input
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|unique:users,email,' . $id,
                'nik' => 'sometimes|required|string|max:200|unique:users,nik,' . $id,
                'KTP' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:5120',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Data yang akan diupdate
            $data = [];

            if ($request->has('name')) {
                $data['name'] = $request->name;
            }

            if ($request->has('email')) {
                $data['email'] = $request->email;
            }

            if ($request->has('nik')) {
                $data['nik'] = $request->nik;
            }

            // Handle upload KTP
            if ($request->hasFile('KTP')) {
                // Hapus file KTP lama jika ada
                if ($targetUser->KTP && Storage::disk('public')->exists($targetUser->KTP)) {
                    Storage::disk('public')->delete($targetUser->KTP);
                }

                // Upload file baru
                $ktpPath = $request->file('KTP')->store('ktp', 'public');
                $data['KTP'] = $ktpPath;
            }

            // Update user
            $targetUser->update($data);

            // Ambil data user yang sudah diupdate
            $updatedUser = User::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Profile berhasil diupdate',
                'data' => [
                    'id' => $updatedUser->id,
                    'name' => $updatedUser->name,
                    'email' => $updatedUser->email,
                    'nik' => $updatedUser->nik,
                    'role' => $updatedUser->role,
                    'status' => $updatedUser->status,
                    'ktp_url' => $updatedUser->KTP ? asset('storage/' . $updatedUser->KTP) : null,
                    'created_at' => $updatedUser->created_at,
                    'updated_at' => $updatedUser->updated_at,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     public function changePassword(Request $request)
    {
        try {
            $authUser = Auth::user();

            if (!$authUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 401);
            }

            // Validasi input
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'password' => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek current password
            if (!Hash::check($request->current_password, $authUser->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini salah'
                ], 400);
            }

            // Update password
            $user = User::findOrFail($authUser->id);
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
