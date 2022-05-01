<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupStudentsController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// // Teacher Routes
// Route::prefix('teachers')->name('teachers.')->group(function () {
//     Route::get('/', [TeacherController::class, 'get_teachers'])->name("all");
//     Route::get('/{id}', [TeacherController::class, 'get_teacher'])->name("find");
//     Route::post('/create', [TeacherController::class, 'store'])->name("create");
//     Route::post('/edit', [TeacherController::class, 'edit'])->name("edit");
//     Route::post('/delete', [TeacherController::class, 'delete'])->name("delete");
// });

// // Courses Routes
// Route::prefix('courses')->name('courses.')->group(function () {
//     Route::get('/', [CourseController::class, 'get_courses'])->name("all");
//     Route::get('/{id}', [CourseController::class, 'get_course'])->name("find");
//     Route::post('/create', [CourseController::class, 'store'])->name("create");
//     Route::post('/edit', [CourseController::class, 'edit'])->name("edit");
//     Route::post('/delete', [CourseController::class, 'delete'])->name("delete");
// });

// // Groups Routes
Route::prefix('groups')->name('groups.')->group(function () {
    Route::get('/', [GroupController::class, 'get_groups'])->name("all");
    // Route::get('/{id}', [GroupController::class, 'get_group'])->name("find");
    // Route::post('/create', [GroupController::class, 'store'])->name("create");
    // Route::post('/edit', [GroupController::class, 'edit'])->name("edit");
    // Route::post('/delete', [GroupController::class, 'delete'])->name("delete");
    // Route::post('/add-student', [GroupStudentsController::class, 'assign_student'])->name("assign");
    // Route::post('/remove-student', [GroupStudentsController::class, 'detach_student'])->name("detach");
});

// // Student Routes
Route::prefix('students')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'get_students'])->name("all");
    // Route::get('/{id}', [StudentController::class, 'get_student'])->name("find");
    // Route::post('/create', [StudentController::class, 'store'])->name("create");
    // Route::post('/edit', [StudentController::class, 'edit'])->name("edit");
    // Route::post('/delete', [StudentController::class, 'delete'])->name("delete");
    Route::post('/add-to-group', [StudentController::class, 'assign_to_group'])->name("assign");
    // Route::post('/detach-from-group', [StudentController::class, 'detach_from_group'])->name("detach");
});
