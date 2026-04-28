<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MentorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::prefix('registration')->group(function () {
        Route::post('student', [RegistrationController::class, 'registerStudent']);
        Route::post('company', [RegistrationController::class, 'registerCompany']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        // TODO
    });
});

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);
Route::get('/all-mentors-info', [MentorController::class, 'index']);
Route::get('/all-companies-info', [CompanyController::class, 'index']);
