<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

// CONTROLLERS VISITOR
use App\Http\Controllers\visitor\HomeController;
use App\Http\Controllers\visitor\AuthController as VisitorAuthController;


Route::get('/', [HomeController::class, 'index'])->name('home');





// ROUTE LOGIN
// Route::get('/login', [AuthController::class, 'index'])->name('login');


Route::get('/login',[VisitorAuthController::class,'login'])->name('login');
Route::post('/login',[VisitorAuthController::class,'loginPost']);

Route::get('/register',[VisitorAuthController::class,'register'])->name('register');
Route::post('/register',[VisitorAuthController::class,'registerPost']);

Route::post('/logout',[VisitorAuthController::class,'logout'])->name('logout');


Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard');


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
// ANGGOTA
Route::middleware(['auth', 'role:anggota'])->group(function () {
    Route::get('/anggota/dashboard', function () {
        return view('anggota.pages.dashboard.index');
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


