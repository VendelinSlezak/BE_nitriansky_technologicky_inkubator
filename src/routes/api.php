<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\ProgramAController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\FaqQuestionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\CommissionMemberController;

Route::prefix('auth')->group(function () {
    // Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1'); // úmyselne zakomentované aby to neznemožňovalo množstvo prihlásení pri vývoji
    Route::post('/login', [AuthController::class, 'login']);
    Route::prefix('registration')->group(function () {
        Route::post('student', [RegistrationController::class, 'registerStudent']);
        Route::post('company', [RegistrationController::class, 'registerCompany']);
    });
    Route::post('/reset-password-request', [AuthController::class, 'resetPasswordRequest']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-from-all-devices', [AuthController::class, 'logoutAll']);

        Route::middleware('student')->group(function () {
            Route::get('/student', [StudentController::class, 'dashboard']);
            Route::post('/program-a/create-challenge', [ChallengeController::class, 'createProgramAChallenge']);
            Route::post('/student/accept-invitation', [StudentController::class, 'acceptTeamInvitation']);
            Route::post('/student/reject-invitation', [StudentController::class, 'rejectTeamInvitation']);
            Route::post('/student/create-team', [TeamController::class, 'store']);
            Route::get('/program-a/categories', [StudentController::class, 'getProgramACategories']);

            Route::post('/team/{team}/invite-member', [TeamController::class, 'inviteMember']);
            Route::delete('/team/{team}/remove-member/{student}', [TeamController::class, 'deleteMember']);
            Route::post('/team/{team}/send-for-approval', [TeamController::class, 'completeTeam']);
        });

        Route::middleware('admin_or_student')->group(function () {
            Route::get('/program-a', [ChallengeController::class, 'getProgramAChallenges']);
            Route::get('/program-b', [ChallengeController::class, 'getProgramBChallenges']);
            Route::post('/student/can-be-invited', [StudentController::class, 'canBeInvited']);
            Route::delete('/team/{team}', [TeamController::class, 'destroy']);
        });

        Route::middleware('admin')->group(function () {
            Route::get('/users', [UserController::class, 'index']);
            Route::get('/user/{user}', [UserController::class, 'getUserAccount']);
            Route::post('/user/{user}', [UserController::class, 'updateUserAccount']);
            Route::delete('/user/{user}', [UserController::class, 'deleteUserAccount']);

            Route::post('/milestone/{milestone}', [ChallengeController::class, 'updateMilestone']);
            Route::post('/challenge/{id}/add-milestone', [ChallengeController::class, 'addMilestone']);
            Route::delete('/milestone/{milestone}', [ChallengeController::class, 'destroyMilestone']);

            Route::post('/challenge/{challenge}/move-back-to-open', [ChallengeController::class, 'moveBackToOpen']);
            Route::post('challenge/{id}/send-to-commission', [ChallengeController::class, 'sendToCommission']);
            Route::post('challenge/{id}/start-realisation', [ChallengeController::class, 'startRealisation']);
            Route::get('/challenges', [ChallengeController::class, 'adminChallengesInfo']);
            Route::post('/challenge/{challenge}/accept', [ChallengeController::class, 'acceptChallenge']);
            Route::post('/challenge/{challenge}/close', [ChallengeController::class, 'closeChallenge']);
            Route::get('challenges/registration-requests', [ChallengeController::class, 'getRegistrationRequests'])->name('challenges.registration-requests');
            Route::post('/create-challenge', [ChallengeController::class, 'createChallenge']);
            Route::post('/challenge/{challenge}/reject', [ChallengeController::class, 'destroyChallenge']);
            Route::get('/challenge-details/{challenge}', [ChallengeController::class, 'getChallenge'])->name('challenge-get');
            Route::get('/accounts/committee-members', [CommissionMemberController::class, 'index']);

            Route::get('/program-a/all-categories', [ProgramAController::class, 'index'])->name('admin.index');
            Route::post('/program-a/create-category', [ProgramAController::class, 'store']);
            Route::post('/program-a/category/{id}', [ProgramAController::class, 'update']);
            Route::delete('/program-a/category/{id}', [ProgramAController::class, 'destroy']);

            Route::delete('/student/{id}', [StudentController::class, 'destroy']);
            Route::post('/create-team', [TeamController::class, 'createTeam']);
            Route::post('/team/{team}', [TeamController::class, 'updateTeam']);
            Route::get('/team/{team}', [TeamController::class, 'show']);
            Route::get('/teams', [TeamController::class, 'index'])->name('admin.teams');

            Route::get('/students/registration-requests', [StudentController::class, 'getRegistrationRequests']);
            Route::post('/student/{student}/approve-registration', [StudentController::class, 'approveRegistration']);
            Route::post('/student/{student}/reject-registration', [StudentController::class, 'rejectRegistration']);

            Route::get('/companies/registration-requests', [CompanyController::class, 'getRegistrationRequests']);
            Route::post('/company/{company}/approve-registration', [CompanyController::class, 'approveRegistration']);
            Route::post('/company/{company}/reject-registration', [CompanyController::class, 'rejectRegistration']);

            Route::post('/create-account', [UserController::class, 'createAccount']);
        });

        Route::middleware('admin_or_commission_member')->group(function () {
            Route::get('/accounts/mentors', [MentorController::class, 'getAllMentorsShortInfo']);
        });

        Route::middleware('mentor')->group(function () {
            Route::get('/mentor/all-challenges', [MentorController::class, 'mentorChallengesInfo']);
            Route::post('/challenge/{challenge}/set-milestone-comment/{milestone}', [ChallengeController::class, 'setMilestoneComment']);
        });

        Route::middleware('commission_member_or_mentor')->group(function () {
            Route::get('/challenge/{challenge}', [ChallengeController::class, 'getFullChallengeInfo']);
        });

        Route::middleware('commission_member')->group(function () {
            Route::get('/commission-member/all-challenges', [CommissionMemberController::class, 'commissionMemberChallengesInfo']);
            Route::post('/challenge/{challenge}/set-commission-decision', [ChallengeController::class, 'setCommissionDecision']);
        });

        Route::middleware('admin_or_web_editor')->group(function () {
            Route::post('/article/create', [ArticleController::class, 'store']);
            Route::post('/article/{article}', [ArticleController::class, 'update']);
            Route::delete('/article/{article}', [ArticleController::class, 'destroy']);
            Route::post('/faq/create', [FaqQuestionController::class, 'store']);
            Route::post('/faq/{faqQuestion}', [FaqQuestionController::class, 'update']);
            Route::delete('/faq/{faqQuestion}', [FaqQuestionController::class, 'destroy']);
        });

        Route::middleware('company_admin')->group(function () {
            Route::get('/company/members', [CompanyController::class, 'companyMembersInfo']);
            Route::post('/company/create-member', [CompanyController::class, 'storeCompanyMember']);
            Route::delete('/company/member/{member}', [CompanyController::class, 'deleteCompanyMember']);
            Route::post('/program-b/create', [ChallengeController::class, 'createProgramBChallenge']);
        });

        Route::middleware('company_admin_or_company_member')->group(function () {
            Route::get('/company/challenges', [CompanyController::class, 'getAllChallenges']);
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
