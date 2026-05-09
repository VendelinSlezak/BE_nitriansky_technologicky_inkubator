<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramAController;
use App\Http\Controllers\TeamController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\FaqQuestionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FileController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::prefix('registration')->group(function () {
        Route::post('student', [RegistrationController::class, 'registerStudent']);
        Route::post('company', [RegistrationController::class, 'registerCompany']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-from-all-devices', [AuthController::class, 'logoutAll']);

        Route::middleware('student')->group(function () {
            Route::get('/student', [StudentController::class, 'dashboard']);
            Route::post('/program-a/create', [ChallengeController::class, 'createProgramAChallenge']);
        });

        Route::middleware('admin_or_student')->group(function () {
            Route::get('/program-a', [ChallengeController::class, 'getProgramAChallenges']);
            Route::get('/program-b', [ChallengeController::class, 'getProgramBChallenges']);
        });

        Route::middleware('admin')->group(function () {
            Route::get('/challenges', [ChallengeController::class, 'adminChallengesInfo']);
            Route::get('/accounts/mentors', [MentorController::class, 'adminMentorsInfo']);
            Route::get('/students', [StudentController::class, 'adminStudentsInfo']);
            Route::post('/program-a/create-category', [ProgramAController::class, 'store']);
            Route::delete('/program-a/category/{id}', [ProgramAController::class, 'destroy']);
        });
    });
});

Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);

Route::get('/all-mentors-info', [MentorController::class, 'index']);

Route::get('/all-companies-info', [CompanyController::class, 'index']);
Route::get('/all-companies-logos', [CompanyController::class, 'getAllLogos']);

Route::get('/challenges/preview',[ChallengeController::class,'index'])->name('challenges.preview');
Route::get('/challenges/preview/three-random',[ChallengeController::class,'getThreeRandomChallenges'])->name('challenges.three-random');
Route::get('/challenges/{id}',[ChallengeController::class,'show'])->name('challenges.show');

Route::get('/faq/a', [FaqQuestionController::class, 'getFaqFromProgramA']);
Route::get('/faq/b', [FaqQuestionController::class, 'getFaqFromProgramB']);

Route::get('/files/download/{file}', [FileController::class, 'download'])
    ->name('files.download')
    ->middleware('signed');
