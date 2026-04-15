<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

<<<<<<< HEAD
use App\Http\Controllers\Api\AuthController as ApiAuthController;
=======
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SavingController;
>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)

// Route untuk bookmark, hanya bisa diakses oleh user yang sudah login, menggunakan middleware auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
<<<<<<< HEAD
    Route::get('auth/logout', [ApiAuthController::class, 'logout']); // Logout
});
Route::post('auth/register', [ApiAuthController::class, 'register']); // Register
Route::post("auth/login", [ApiAuthController::class, "authenticate"]);
=======
    // User Profile Routes
    Route::get('profile', [UserController::class, 'profile']);
    Route::put('users/{id}', [UserController::class, 'update']);
    Route::get('auth/logout', [AuthController::class, 'logout']);
    Route::put('change-password', [UserController::class, 'changePassword']);

    // Saving Routes (Tabungan)
    Route::get('saving-types', [SavingController::class, 'getSavingTypes']);
    Route::get('my-savings', [SavingController::class, 'getMySavings']);
    Route::get('saving-history', [SavingController::class, 'getSavingHistory']);
    Route::get('saving-summary', [SavingController::class, 'getSavingSummary']);
    Route::get('saving-detail/{id}', [SavingController::class, 'getSavingDetail']);
    Route::post('deposit', [SavingController::class, 'deposit']);
    Route::post('withdraw', [SavingController::class, 'withdraw']);
});

// Public Routes (tanpa authentication)
Route::post('auth/register', [AuthController::class, 'register']);
Route::post("auth/login", [AuthController::class, "authenticate"]);

>>>>>>> d21c2f7 (tambah api saving, data profil user, dan perbaikan register)
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//
