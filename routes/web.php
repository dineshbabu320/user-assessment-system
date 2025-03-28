<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return view('login');
})->name('front');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//All other routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function() { 
        return view('dashboard'); 
    })->name('dashboard');
});