<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentTokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\ManualEvaluationController;
use App\Http\Controllers\Admin\RatingUpgradeController;
use App\Http\Controllers\StudentHistoryController;
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
    Route::get('/faq', function() { return view('faq'); })->name('faq');

    Route::middleware(['roleAboveStudent'])->group(function(){
        //Issue Token
        Route::get('/students/search', [StudentTokenController::class, 'search'])->name('students.search');
        Route::post('/students/issue-token', [StudentTokenController::class, 'issueToken'])->name('students.issueToken');
        Route::get('/student/manual-evaluation', [ManualEvaluationController::class, 'index'])->name('student.manualEvaluation');
        Route::get('/student/manual-evaluation/{cid}', [ManualEvaluationController::class, 'show'])->name('student.manualEvaluation.show');
        Route::post('/student/manual-evaluation/{cid}/evaluate-all', [ManualEvaluationController::class, 'evaluateAll'])->name('student.evaluateAll');
        Route::get('/student/{cid}', [StudentHistoryController::class, 'index'])->name('student.studentHistory');
    });

    Route::get('/my-exams', [ExamController::class, 'myExams'])->name('my-exams');

    Route::middleware(['hasExamToken'])->group(function () {
        //Exam routes
        Route::get('/exam/{examName}/instructions', [ExamController::class, 'instructions'])->name('exam.instructions');
        Route::get('/exam/{examName}', [ExamController::class, 'takeExam'])->name('exam.take');
        Route::post('/exam/{examName}/submit', [ExamController::class, 'submitExam'])->name('exam.submit');
    });

    Route::get('/exam/{attemptId}/review', [ExamController::class, 'reviewExam'])->name('exam.review');

    Route::middleware(['admin'])->group(function() {
        Route::get('/admin/questions', [QuestionController::class, 'index'])->name('admin.questions.index');
        Route::get('/admin/questions/create/{exam_id}', [QuestionController::class, 'create'])->name('admin.questions.create');
        Route::post('/admin/questions/store', [QuestionController::class, 'store'])->name('admin.questions.store');
        Route::get('/admin/questions/{id}/edit', [QuestionController::class, 'edit'])->name('admin.questions.edit');
        Route::put('/admin/questions/{id}', [QuestionController::class, 'update'])->name('admin.questions.update');
        Route::post('/api/exams/questions', [QuestionController::class, 'getQuestions'])->name('api.exams.questions');
        Route::post('/api/questions/get', [QuestionController::class, 'getQuestion'])->name('api.questions.get');
        Route::get('/admin/rating-upgrade-requests', [RatingUpgradeController::class, 'index'])->name('admin.ratingUpgradeRequests');
        Route::post('/admin/rating-upgrade-requests/{id}/complete', [RatingUpgradeController::class, 'markAsCompleted'])->name('admin.markRatingUpgradeCompleted');
    });
});