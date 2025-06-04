<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\StudentTokenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\ManualEvaluationController;
use App\Http\Controllers\StudentHistoryController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\RoleController;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Tracing\Middleware;

Route::get('/', function() {
    return view('login');
})->name('front');
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

//All other routes
Route::middleware(['auth', Middleware::class])->group(function () {

    //DashBoard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/faq', function() { return view('faq'); })->name('faq');
    
    Route::middleware(['examiner'])->group( function() {
        //Examiner stuff
        Route::get('/examiner/manual-evaluation', [ManualEvaluationController::class, 'index'])->name('student.manualEvaluation');
        Route::get('/examiner/manual-evaluation/{cid}', [ManualEvaluationController::class, 'show'])->name('student.manualEvaluation.show');
        Route::post('/examiner/manual-evaluation/{cid}/evaluate-all', [ManualEvaluationController::class, 'evaluateAll'])->name('student.evaluateAll');
    });

    Route::middleware(['subdivisionStaff'])->group(function(){
        //Issue Token
        Route::get('/students/search', [StudentTokenController::class, 'index'])->name('students.search');
        Route::post('/students/issue-token', [StudentTokenController::class, 'issueToken'])->name('students.issueToken');
        Route::get('/student/{cid}', [StudentHistoryController::class, 'index'])->name('student.studentHistory');
        Route::patch('/tokens/{token}/revoke', [StudentTokenController::class, 'revokeToken'])->name('tokens.revoke');
        Route::get('/students/find', [StudentTokenController::class, 'search'])->name('students.find');
    });

    Route::middleware(['mentor'])->group(function() {
        Route::get('/mentor/students/search', [StudentTokenController::class, 'index'])->name('mentor.studentSearch');
        Route::get('/mentor/student/{cid}', [StudentHistoryController::class, 'index'])->name('mentor.studentHistory');
        Route::get('/mentor/students/find', [StudentTokenController::class, 'search'])->name('mentors.find');
    });


    Route::get('/my-exams', [ExamController::class, 'myExams'])->name('my-exams');

    Route::middleware(['hasExamToken'])->group(function () {
        //Exam routes
        Route::get('/exam/{examName}/instructions', [ExamController::class, 'instructions'])->name('exam.instructions');
        Route::post('/exam/{examName}/start', [ExamController::class, 'startExam'])->name('exam.start');
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
        Route::get('/admin/activity-log', [ActivityLogController::class, 'index'])->name('admin.activity-log');
        Route::get('/admin/roles', [RoleController::class, 'index'])->name('admin.roles.index');
        Route::post('/admin/roles/assign', [RoleController::class, 'assignRole'])->name('admin.roles.assign');
        Route::delete('/admin/questions/{id}', [QuestionController::class, 'destroy'])->name('admin.questions.destroy');
        Route::get('/manual-evaluation-admin/{attemptId}', [ManualEvaluationController::class, 'manualEvaluate'])->name('manual-evaluation-admin.show');
        Route::post('/manual-evaluation-admin/{attemptId}', [ManualEvaluationController::class, 'submitManualEvaluation'])->name('manual-evaluation-admin.submit');
        Route::get('/admin/settings', [ExamController::class, 'examSettings'])->name('admin.settings');
        Route::put('/admin/update-settings', [ExamController::class, 'updateExamSettings'])->name('admin.updateSettings');
        Route::post('/admin/questions/{id}/remove-file', [QuestionController::class, 'removeFile'])->name('admin.questions.remove-file');
        Route::post('/admin/roles/adjust', [RoleController::class, 'adjustRoles'])->name('admin.roles.adjust');
    });
});