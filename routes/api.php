<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\AuthController;

Route::resource('/media', MediaController::class);

// Login Contoller
// Auth Controller
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
