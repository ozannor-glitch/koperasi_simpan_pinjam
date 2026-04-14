<?php

use Illuminate\Support\Facades\Route;

// CONTROLLERS VISITOR
use App\Http\Controllers\visitor\HomeController;
use App\Http\Controllers\visitor\AuthController;


Route::get('/', [HomeController::class, 'index'])->name('home');





// ROUTE LOGIN
// Route::get('/login', [AuthController::class, 'index'])->name('login');


Route::get('/login',[AuthController::class,'login'])->name('login');
Route::post('/login',[AuthController::class,'loginPost']);

Route::get('/register',[AuthController::class,'register'])->name('register');
Route::post('/register',[AuthController::class,'registerPost']);

Route::post('/logout',[AuthController::class,'logout'])->name('logout');


Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard');
