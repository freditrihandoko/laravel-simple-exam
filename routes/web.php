<?php

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserExamController;
use App\Http\Middleware\HasRoleAdminMiddleware;
use App\Http\Controllers\AdminExamResultController;
use App\Http\Controllers\GroupExamExportController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('user_exams/start/{exam}', [UserExamController::class, 'startExam'])->name('user_exams.start');
    Route::post('user_exams/submit/{exam}', [UserExamController::class, 'submitExam'])->name('user_exams.submit');
    Route::post('user_exams.save_answer', [UserExamController::class, 'saveAnswer'])
        ->name('user_exams.save_answer');
    Route::post('/user_exams/check_status', [UserExamController::class, 'checkStatus'])->name('user_exams.check_status');
    Route::resource('user_exams', UserExamController::class)->only(['index', 'show']);

    Route::middleware(HasRoleAdminMiddleware::class)->group(function () {
        Route::post('topics/{id}/questions', [TopicController::class, 'addQuestion'])->name('topics.addQuestion');
        Route::delete('topics/{topic}/questions/{question}', [TopicController::class, 'deleteQuestion'])
            ->name('topics.deleteQuestion');
        Route::get('topics/{topic}/questions/{question}', [TopicController::class, 'editQuestion'])->name('topics.editQuestion');
        Route::put('topics/{topic}/questions/{question}', [TopicController::class, 'updateQuestion'])
            ->name('topics.updateQuestion');
        Route::post('/topics/{id}/import-questions', [TopicController::class, 'importQuestions'])->name('topics.importQuestions');
        Route::post('groups/{group}/addUsers', [GroupController::class, 'addUsers'])->name('groups.addUsers');
        Route::post('groups/{group}/importUsers', [GroupController::class, 'importUsers'])->name('groups.importUsers');
        Route::delete('groups/{group}/removeUser/{user}', [GroupController::class, 'removeUser'])->name('groups.removeUser');
        Route::post('users/{user}/add-to-group', [UserController::class, 'addToGroup'])->name('users.addToGroup');
        Route::post('users/import', [UserController::class, 'import'])->name('users.import');

        Route::post('exams.bulkReschedule', [ExamController::class, 'bulkReschedule'])->name('exams.bulkReschedule');

        Route::post('exam_results/bulk_action', [AdminExamResultController::class, 'bulkAction'])
            ->name('exam_results.bulk_action');
        Route::get('exam_results/export', [AdminExamResultController::class, 'export'])->name('exam_results.export');

        Route::get('group_exam_export', [GroupExamExportController::class, 'index'])->name('group_exam_export.index');
        Route::get('group_exam_export/export', [GroupExamExportController::class, 'export'])->name('group_exam_export.export');

        Route::resource('topics', TopicController::class);
        Route::resource('users', UserController::class);
        Route::resource('groups', GroupController::class);
        Route::resource('exams', ExamController::class);
        Route::resource('exam_results', AdminExamResultController::class);
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
