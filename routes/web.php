<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentTokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\QuestionController;
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

    Route::middleware(['admin'])->group(function() {
        Route::get('/admin/questions', [QuestionController::class, 'index'])->name('admin.questions.index');
        Route::get('/admin/questions/create/{exam_id}', [QuestionController::class, 'create'])->name('admin.questions.create');
        Route::post('/admin/questions/store', [QuestionController::class, 'store'])->name('admin.questions.store');
        Route::get('/admin/questions/{id}/edit', [QuestionController::class, 'edit'])->name('admin.questions.edit');
        Route::put('/admin/questions/{id}', [QuestionController::class, 'update'])->name('admin.questions.update');
        Route::post('/api/exams/questions', [QuestionController::class, 'getQuestions'])->name('api.exams.questions');
        Route::post('/api/questions/get', [QuestionController::class, 'getQuestion'])->name('api.questions.get');
    });
});