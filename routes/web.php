<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloWorldController;
use App\Http\Controllers\UserController;

Route::get('/hello', [HelloWorldController::class, 'index']);

Route::get('/', function () {
    return view('welcome');
});

// User CRUD Routes
Route::resource('users', UserController::class);
