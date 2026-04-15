<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController as ApiAuthController;


// route untuk bookmark, hanya bisa diakses oleh user yang sudah login, menggunakan middleware auth:sanctum untuk mengecek apakah user sudah login atau belum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/logout', [ApiAuthController::class, 'logout']); // Logout
});
Route::post('auth/register', [ApiAuthController::class, 'register']); // Register
Route::post("auth/login", [ApiAuthController::class, "authenticate"]);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
