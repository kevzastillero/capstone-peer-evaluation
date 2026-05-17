<?php

use App\Http\Controllers\BPMcontroller;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if (auth()->user()->role === 'student') {
            return redirect()->route('student.dashboard');
        }

        return redirect()->route('login');
    }

    return view('welcome');
})->name('home');

Route::get('/login', [BPMcontroller::class, 'login'])->name('login');
Route::post('/login', [BPMcontroller::class, 'loginpost'])->name('login.post');
Route::get('/registration', [BPMcontroller::class, 'registration'])->name('registration');
Route::post('/registration', [BPMcontroller::class, 'registrationpost'])->name('registration.post');
Route::get('logout', [BPMcontroller::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::put('/admin/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.update');
    Route::put('/admin/password', [AdminController::class, 'updatePassword'])->name('admin.password.update');
    Route::get('/admin/blocks/{block}', [AdminController::class, 'showBlock'])->name('admin.blocks.show');
    Route::get('/admin/students', [AdminController::class, 'students'])->name('admin.students');
    Route::post('/admin/students', [AdminController::class, 'storeStudent'])->name('admin.students.store');
    Route::put('/admin/students/{student}', [AdminController::class, 'updateStudent'])->name('admin.students.update');
    Route::delete('/admin/students/{student}', [AdminController::class, 'destroyStudent'])->name('admin.students.destroy');
    Route::post('/admin/students/import', [AdminController::class, 'importStudents'])->name('admin.students.import');
    Route::get('/admin/evaluation-form', [AdminController::class, 'evaluationForm'])->name('admin.evaluation-form');
    Route::post('/admin/evaluation-form/questions', [AdminController::class, 'storeQuestion'])->name('admin.evaluation-form.questions.store');
    Route::put('/admin/evaluation-form/questions/{question}', [AdminController::class, 'updateQuestion'])->name('admin.evaluation-form.questions.update');
    Route::put('/admin/evaluation-form/scales/{scale}', [AdminController::class, 'updateScale'])->name('admin.evaluation-form.scales.update');
    Route::get('/admin/reports', [AdminController::class, 'report'])->name('admin.reports');
    Route::get('/admin/reports/export', [AdminController::class, 'exportReport'])->name('admin.reports.export');
});

Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::put('/student/profile', [StudentController::class, 'updateProfile'])->name('student.profile.update');
    Route::put('/student/password', [StudentController::class, 'updatePassword'])->name('student.password.update');
    Route::get('/student/evaluate/{student}', [StudentController::class, 'create'])->name('student.evaluations.create');
    Route::post('/student/evaluate/{student}', [StudentController::class, 'store'])->name('student.evaluations.store');
});
