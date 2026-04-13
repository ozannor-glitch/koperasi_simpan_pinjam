<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

//Bagian Admin
Route::prefix('auth')->group(function () {
    // login
    Route::get('login', [AuthController::class, 'login']);
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::get('logout', [AuthController::class, 'logout']);
});

Route::middleware([CheckAdmin::class])->prefix('superadmin')->name('superadmin.')->group(function () {

Route::get('dashboard', [DashboardController::class,'index']);

});

