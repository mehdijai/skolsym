<?php

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

Route::get('/teachers', [TeacherController::class, 'get_teachers']);
Route::get('/teacher/{id}', [TeacherController::class, 'get_teacher']);
Route::post('/teacher/create', [TeacherController::class, 'store']);
Route::post('/teacher/edit', [TeacherController::class, 'edit']);
Route::post('/teacher/delete', [TeacherController::class, 'delete']);