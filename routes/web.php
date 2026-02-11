<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxController;


Route::get('/', function () {
    return view('map');
})->name('home');

Route::get('/map', function () {
    return view('map');
})->name('map');

Route::get('/signup', [AuthController::class, 'showSignupForm'])->name('signup.form');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/import', [BoxController::class, 'showImportForm'])->name('import.form');
    Route::post('/import', [BoxController::class, 'import'])->name('import.process');
});
