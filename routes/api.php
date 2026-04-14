<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;


// route untuk bookmark, hanya bisa diakses oleh user yang sudah login, menggunakan middleware auth:sanctum untuk mengecek apakah user sudah login atau belum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('auth/logout', [AuthController::class, 'logout']); // Logout
});
Route::post('auth/register', [AuthController::class, 'register']); // Register
Route::post("auth/login", [AuthController::class, "authenticate"]);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
