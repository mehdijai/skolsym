<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
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
        Route::post('/destroy', [TeacherController::class, 'destroy'])->name("destroy");
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
        Route::post('/destroy', [CourseController::class, 'destroy'])->name("destroy");
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
        Route::get('/{id}', [GroupController::class, 'view'])->name("view");
        Route::post('/destroy', [GroupController::class, 'destroy'])->name("destroy");
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
        Route::get('/{id}', [StudentController::class, 'view'])->name("view");
        Route::post('/destroy', [StudentController::class, 'destroy'])->name("destroy");
    });

    // Payment Routes
    Route::name('payments.')->prefix('payments')->group(function () {
        Route::get('/', [PaymentController::class, 'index'])->name('index');
        Route::post('/store', [PaymentController::class, 'store'])->name('store');
        Route::get('/create', [PaymentController::class, 'create'])->name('create');
        Route::get('/update/{id}', [PaymentController::class, 'update'])->name('update');
        Route::post('/edit', [PaymentController::class, 'edit'])->name('edit');
        Route::post('/delete', [PaymentController::class, 'delete'])->name("delete");
        Route::post('/pay', [PaymentController::class, 'pay'])->name("pay");
        Route::post('/destroy', [PaymentController::class, 'destroy'])->name("destroy");
    });

    // Users Routes
    Route::name('users.')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        // Route::post('/store', [UserController::class, 'store'])->name('store');
        // Route::get('/create', [UserController::class, 'create'])->name('create');
        // Route::get('/update/{id}', [UserController::class, 'update'])->name('update');
        // Route::post('/edit', [UserController::class, 'edit'])->name('edit');
        // Route::post('/delete', [UserController::class, 'delete'])->name("delete");
        // Route::post('/pay', [UserController::class, 'pay'])->name("pay");
        // Route::post('/destroy', [UserController::class, 'destroy'])->name("destroy");
    });

    // Accounting Routes
    Route::get('/accounting', function () {
        return Inertia::render('Dashboard');
    })->name('accounting.index');

});

Route::get('/test', function () {
    return csrf_token();
});
