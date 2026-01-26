<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BoxController;
use Illuminate\Support\Facades\Route;

Route::get('/boxes', [BoxController::class, 'index']);
Route::get('/boxes', [BoxController::class, 'index']);
Route::post('/boxes', [BoxController::class, 'store']);
Route::put('/boxes/{id}', [BoxController::class, 'update']);
