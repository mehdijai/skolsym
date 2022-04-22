<?php

use App\Http\Controllers\TeacherController;
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
    Route::get('/teachers', function () {
        return Inertia::render('Teachers');
    })->name('teachers.index');

    // Course Routes
    Route::get('/courses', function () {
        return Inertia::render('Dashboard');
    })->name('courses.index');

    // Group Routes
    Route::get('/groups', function () {
        return Inertia::render('Dashboard');
    })->name('groups.index');

    // Student Routes
    Route::get('/students', function () {
        return Inertia::render('Dashboard');
    })->name('students.index');

    // Payment Routes
    Route::get('/payments', function () {
        return Inertia::render('Dashboard');
    })->name('payments.index');

    // Accounting Routes
    Route::get('/accounting', function () {
        return Inertia::render('Dashboard');
    })->name('accounting.index');

});
