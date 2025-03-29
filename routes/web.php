<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentTokenController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return view('login');
})->name('front');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//All other routes
Route::middleware(['auth'])->group(function () {

    //DashBoard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware(['roleAboveStudent'])->group(function(){
        //Issue Token
        Route::get('/students/search', [StudentTokenController::class, 'search'])->name('students.search');
        Route::post('/students/issue-token', [StudentTokenController::class, 'issueToken'])->name('students.issueToken');
    });
});