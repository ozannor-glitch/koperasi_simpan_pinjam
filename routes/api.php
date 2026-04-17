<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SavingController;
use App\Http\Controllers\Api\PaymentController;

// Route untuk bookmark, hanya bisa diakses oleh user yang sudah login, menggunakan middleware auth:sanctum
Route::middleware('auth:sanctum')->group(function () {
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

    // Midtrans Routes
     // Payment routes
    Route::post('payment/request', [PaymentController::class, 'requestPayment']);
    Route::get('payment/status/{orderId}', [PaymentController::class, 'checkPaymentStatus']);

    // Callback routes (redirect)
    Route::get('payment/success', [PaymentController::class, 'paymentSuccess']);
    Route::get('payment/failed', [PaymentController::class, 'paymentFailed']);

});

Route::post('payment/webhook', [PaymentController::class, 'webhook']);
// Public Routes (tanpa authentication)
Route::post('auth/register', [AuthController::class, 'register']);
Route::post("auth/login", [AuthController::class, "authenticate"]);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
//
