<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Student;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Teacher Routes
    Route::name('teachers.')->prefix('teachers')->group(function () {
        Route::get('/', [TeacherController::class, 'index'])->name('index');
        Route::get('/create', [TeacherController::class, 'create'])->name('create');
        Route::post('/store', [TeacherController::class, 'store'])->name('store');
        Route::get('/update/{id}', [TeacherController::class, 'update'])->name('update');
        Route::post('/edit', [TeacherController::class, 'edit'])->name('edit');
        Route::get('/archive/{id}', [TeacherController::class, 'archive'])->name('archive');
        Route::post('/delete', [TeacherController::class, 'delete'])->name("delete");
        Route::get('/{id}', [TeacherController::class, 'view'])->name('view');
    });

    // Course Routes
    Route::name('courses.')->prefix('courses')->group(function () {
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/create', [CourseController::class, 'create'])->name('create');
        Route::post('/store', [CourseController::class, 'store'])->name('store');
        Route::get('/update/{id}', [CourseController::class, 'update'])->name('update');
        Route::post('/edit', [CourseController::class, 'edit'])->name('edit');
        Route::get('/archive/{id}', [CourseController::class, 'archive'])->name('archive');
        Route::post('/delete', [CourseController::class, 'delete'])->name("delete");
    });

    // Group Routes
    Route::name('groups.')->prefix('groups')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/create', [GroupController::class, 'create'])->name('create');
        Route::post('/store', [GroupController::class, 'store'])->name('store');
        Route::get('/update/{id}', [GroupController::class, 'update'])->name('update');
        Route::post('/edit', [GroupController::class, 'edit'])->name('edit');
        Route::get('/archive/{id}', [GroupController::class, 'archive'])->name('archive');
        Route::post('/delete', [GroupController::class, 'delete'])->name("delete");
    });

    // Student Routes
    Route::name('students.')->prefix('students')->group(function () {
        Route::get('/', [StudentController::class, 'index'])->name('index');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/store', [StudentController::class, 'store'])->name('store');
        Route::get('/update/{id}', [StudentController::class, 'update'])->name('update');
        Route::post('/edit', [StudentController::class, 'edit'])->name('edit');
        Route::get('/archive/{id}', [StudentController::class, 'archive'])->name('archive');
        Route::post('/delete', [StudentController::class, 'delete'])->name("delete");
    });

    // Payment Routes
    Route::name('payments.')->prefix('payments')->group(function () {
        Route::get('/', function () {
            $student = Student::first();
            dd(Payment::get_status($student, $student->groups()->first()->course));
        })->name('index');
        Route::post('/store', [PaymentController::class, 'store'])->name('store');
    });

    // Accounting Routes
    Route::get('/accounting', function () {
        return Inertia::render('Dashboard');
    })->name('accounting.index');

});
