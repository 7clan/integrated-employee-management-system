<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RagController;

Route::get('/', fn () => view('welcome'));
Route::get('/rag-chat', function () { return view('chat'); });

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('show.register');
    Route::get('/login',    [AuthController::class, 'showLoginForm'])->name('show.login');

    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login',    [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/ninjas',               [EmployeeController::class,'index'])->name('employee.index');
    Route::get('/ninjas/create',        [EmployeeController::class,'create'])->name('employee.create');
    Route::post('/ninjas',              [EmployeeController::class,'store'])->name('employee.store');
    Route::get('/ninjas/{employee}',    [EmployeeController::class,'show'])->name('employee.show');
    Route::delete('/ninjas/{employee}', [EmployeeController::class,'destroy'])->name('employee.destroy');
});

