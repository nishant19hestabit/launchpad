<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;


Route::get('/login', [LoginController::class, 'login']);
Route::post('/login-check', [LoginController::class, 'login_check']);

Route::group(['middleware' => ['login_check','prevent']], function () {
    Route::get('/', [LoginController::class, 'dashboard']);
    Route::get('/logout', [LoginController::class, 'logout']);

    Route::get('/student-list', [StudentController::class, 'student_list']);
});
