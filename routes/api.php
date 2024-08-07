<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\FactionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;

Route::resource('/media', MediaController::class);
Route::resource('/factions', FactionController::class);
Route::resource('/genres', GenreController::class);
Route::resource('/users', UserController::class);

// Login Contoller
Route::post('/register', [AuthController::class, 'register'])->name('api.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:api');
