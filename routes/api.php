<?php

use App\Http\Controllers\BoxController;
use Illuminate\Support\Facades\Route;

Route::get('/boxes', [BoxController::class, 'index']);
