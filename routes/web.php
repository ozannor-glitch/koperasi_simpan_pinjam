<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

//Bagian Admin
Route::prefix('auth')->group(function () {
    // login
    Route::get('login', [AuthController::class, 'login']);
    Route::post('login', [AuthController::class, 'authenticate']);
    Route::get('logout', [AuthController::class, 'logout']);
});

//Super Admin
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/superadmin/dashboard',function(){
    return view('superadmin.pages.dashboard.index');
    });
});
//Admin Keuangan
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/adminkeuangan/dashboard',function(){
    return view('adminkeuangan.pages.dashboard.index');
    });
});
//Teller
Route::middleware(['auth', 'role:teller'])->group(function () {
    Route::get('/teller/dashboard',function(){
    return view('teller.pages.dashboard.index');
    });
});

//CRUD Super Admin
Route::middleware(['auth', 'role:super_admin'])->prefix('superadmin')->group(function () {
            Route::get('/user', [UserController::class, 'index'])->name('user.index');
            Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
            Route::post('user', [UserController::class, 'store'])->name('user.store');
            Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
            Route::put('update/{id}', [UserController::class,'update'])->name('user.update');
            Route::delete('destroy/{id}', [UserController::class,'destroy'])->name('user.destroy');
});


