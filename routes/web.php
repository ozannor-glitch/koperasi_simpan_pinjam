<?php
//Admin Controller
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SavingController as AdminSavingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\CheckAdmin;
use Illuminate\Support\Facades\Route;

// CONTROLLERS VISITOR
use App\Http\Controllers\visitor\HomeController;
use App\Http\Controllers\visitor\AuthController as VisitorAuthController;
use SavingController as GlobalSavingController;

Route::get('/', [HomeController::class, 'index'])->name('home');





// ROUTE LOGIN
// Route::get('/login', [AuthController::class, 'index'])->name('login');


Route::get('/login', [VisitorAuthController::class, 'login'])->name('login');
Route::post('/login', [VisitorAuthController::class, 'loginPost']);

Route::get('/register', [VisitorAuthController::class, 'register'])->name('register');
Route::post('/register', [VisitorAuthController::class, 'registerPost']);

Route::post('/logout', [VisitorAuthController::class, 'logout'])->name('logout');


Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard');


//Bagian Admin
// web.php (Bagian Admin)
Route::prefix('superadmin/auth')->group(function () {
    Route::get('login', [AdminAuthController::class, 'login'])->name('admin.login'); // Ganti namanya
    Route::post('login', [AdminAuthController::class, 'authenticate'])->name('admin.login.post');
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
});

//Super Admin
Route::middleware(['auth', 'role:super_admin'])->group(function () {
    Route::get('/superadmin/dashboard', function () {
        return view('superadmin.pages.dashboard.index');
    })->name('superadmin.dashboard');
});
//Admin Keuangan
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/adminkeuangan/dashboard', function () {
        return view('adminkeuangan.pages.dashboard.index');
    });
});
//Teller
Route::middleware(['auth', 'role:teller'])->group(function () {
    Route::get('/teller/dashboard', function () {
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
Route::middleware(['auth', 'role:super_admin'])->prefix('superadmin')
->name('superadmin.')->group(function () {
    Route::get('/user', [UserController::class, 'index'])->name('user.index');
    Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::post('/user/{id}/verify', [UserController::class, 'verify'])->name('user.verify');
    Route::post('/user/{id}/reject', [UserController::class, 'reject'])->name('user.reject');
    Route::get('user/{id}/edit', [UserController::class, 'edit'])->name('user.edit');
    Route::put('update/{id}', [UserController::class, 'update'])->name('user.update');
    Route::delete('destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');
});

//CRUD Saving
Route::middleware(['auth', 'role:super_admin,admin'])->prefix('superadmin')
    ->name('superadmin.')->group(function () {

    Route::get('/saving', [AdminSavingController::class, 'index'])->name('saving.index');
    Route::get('/saving/create', [AdminSavingController::class, 'create'])->name('saving.create');
    Route::post('/saving/store', [AdminSavingController::class, 'store'])->name('saving.store');
    Route::post('/saving/withdraw', [AdminSavingController::class, 'withdraw'])->name('saving.withdraw');
    Route::get('/saving/transactions', [AdminSavingController::class, 'transactions'])->name('saving.transactions');
    Route::post('/saving/{id}/approve', [AdminSavingController::class, 'approve'])->name('superadmin.saving.approve');
    Route::post('/saving/{id}/reject', [AdminSavingController::class, 'reject'])->name('superadmin.saving.reject');
    Route::delete('/saving/{id}', [AdminSavingController::class, 'destroy'])->name('saving.destroy');
});

