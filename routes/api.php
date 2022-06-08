<?php

use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\TeacherController;
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

Route::post('/student-add', [StudentController::class, 'student_add']);
Route::post('/student-login', [StudentController::class, 'student_login']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('/student-detail', [StudentController::class, 'student_detail']);
    Route::post('/student-update', [StudentController::class, 'student_update']);
    Route::delete('/student-delete', [StudentController::class, 'student_delete']);
});



Route::post('/assign-teacher', [StudentController::class, 'assign']);



Route::post('/teacher-login', [TeacherController::class, 'teacher_login']);
Route::post('/teacher-add', [TeacherController::class, 'teacher_add']);

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('/teacher-detail', [TeacherController::class, 'teacher_detail']);
    Route::post('/teacher-update', [TeacherController::class, 'teacher_update']);
    Route::delete('/teacher-delete', [TeacherController::class, 'teacher_delete']);
});
