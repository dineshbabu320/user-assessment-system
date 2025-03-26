<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () { return view('login'); })->name('login');

//OAuth Routes
Route::get('/auth/redirect', [AuthController::class, 'redirectToVatsim'])->name('auth.redirect');
Route::get('/auth/callback', [AuthController::class, 'handleVatsimCallback'])->name('auth.callback');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

//All other routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function() { return view('dashboard'); })->name('dashboard');
});