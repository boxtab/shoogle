<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\BuddyRequestController;
use App\Http\Controllers\API\V1\CommunityLevelController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\DateNowController;
use App\Http\Controllers\API\V1\DepartmentController;
use App\Http\Controllers\API\V1\InviteController;
use App\Http\Controllers\API\V1\NotificationToUserController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\RewardController;
use App\Http\Controllers\API\V1\SchedulerController;
use App\Http\Controllers\API\V1\ShooglerController;
use App\Http\Controllers\API\V1\ShooglesController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\WebhookController;
use App\Http\Controllers\API\V1\WelbeingScoresController;
use App\Http\Controllers\API\V1\WellbeingCategoryController;
use App\Http\Controllers\API\V1\WellbeingWeekController;
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


/**
 * =====================================================================================================================
 * SHARED
 * =====================================================================================================================
 */
Route::group(['prefix' => 'shared/v1'], function () {

    // POST /api/shared/v1/logout
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');

    // POST /api/shared/v1/login
    Route::post('login', [AuthController::class, 'login']);

    // POST /api/shared/v1/password/forgot
    Route::post('password/forgot', [AuthController::class, 'passwordForgot']);

    // POST /api/shared/v1/password/reset
    Route::post('password/reset', [AuthController::class, 'passwordReset']);

    // POST /api/shared/v1/user/wellbeing-scores/store
    Route::post('user/wellbeing-scores/store', [WelbeingScoresController::class, 'store'])
        ->middleware(['auth:api', 'user_already_logged_in', 'cors']);

    // POST /api/shared/v1/code-validation/:id
    Route::post('code-validation', [AuthController::class, 'codeValidation']);

    Route::post('webhook', [WebhookController::class, 'index']);
});


/**
 * =====================================================================================================================
 * FRONT
 * =====================================================================================================================
 */

// POST /api/front/v1/signup
Route::post('front/v1/signup', [AuthController::class, 'signup']);

Route::group(['prefix' => 'front/v1', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {
    /**
     * Entity: Users
     * Table: users
     */
    Route::group(['prefix' => 'user'], function () {
        // GET /api/front/v1/user/:id
        Route::get('{id}', [UserController::class, 'showFront'])->where('id', '[0-9]+');

        // POST /api/front/v1/user/wellbeing-scores
        Route::post('/wellbeing-scores', [WelbeingScoresController::class, 'averageUserFront']);

        // GET /api/front/v1/user/wellbeing-scores/low
        Route::get('/wellbeing-scores/low', [WelbeingScoresController::class, 'scoresLow']);
    });

    /**
     * Entity: WellbeingCategory
     * Table: wellbeing_categories
     */
    Route::group(['prefix' => 'wellbeing-category'], function () {
        // GET /api/front/v1/wellbeing-category/list
        Route::get('list', [WellbeingCategoryController::class, 'index']);
    });

    /**
     * Entity: Profile
     * Table: users
     */
    Route::group(['prefix' => 'profile'], function () {

        // PUT /api/front/v1/profile
        Route::post('', [ProfileController::class, 'store']);

        // GET /api/front/v1/profile
        Route::get('', [ProfileController::class, 'show']);

    });

    /**
     * Entity: Shoogle
     * Table: shoogles
     */
    Route::group(['prefix' => 'shoogle'], function () {

        // GET /api/front/v1/shoogle/list/:page/:pageSize
        Route::get('list/{page}/{pageSize}', [ShooglesController::class, 'listShoogleOfAuthUser'])
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // POST /api/front/v1/shoogle/search/:page/:pageSize
        Route::post('search/{page}/{pageSize}', [ShooglesController::class, 'search'])
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/shooglers/:page/:pageSize
        Route::post('{id}/shooglers/{page}/{pageSize}', [ShooglerController::class, 'index'])
            ->where('id', '[0-9]+')
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // POST /api/front/v1/shoogle
        Route::post('', [ShooglesController::class, 'create']);

        // GET /api/front/v1/shoogle/:id/views
        Route::get('{id}/views', [ShooglesController::class, 'views'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/solo/1
        Route::post('{id}/solo/1', [ShooglesController::class, 'soloYes'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/solo/0
        Route::post('{id}/solo/0', [ShooglesController::class, 'soloNo'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/entry
        Route::post('entry', [ShooglesController::class, 'entry']);

        // POST /api/front/v1/shoogle/:id/leave
        Route::post('{id}/leave', [ShooglesController::class, 'leave'])->where('id', '[0-9]+');

        // DELETE /api/front/v1/shoogle/:id
        Route::delete('{id}', [ShooglesController::class, 'destroy'])->where('id', '[0-9]+');

        // GET /api/front/v1/shoogle/:id/calendar
        Route::get('{id}/calendar', [ShooglesController::class, 'calendar'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/setting
        Route::post('{id}/setting', [ShooglesController::class, 'setting'])->where('id', '[0-9]+');
    });

    /**
     * Entity: Reward
     * Table: user_has_reward
     */
    Route::group(['prefix' => 'reward'], function () {
        // POST /api/front/v1/reward/user
        Route::post('user', [RewardController::class, 'assign']);

        // GET /api/front/v1/reward/list
        Route::get('list', [RewardController::class, 'listReward']);
    });

    /**
     * Entity: Buddy request
     * Table: buddy_request
     */
    Route::group(['prefix' => 'buddy'], function () {
        // POST /api/front/v1/buddy/request
        Route::post('request', [BuddyRequestController::class, 'buddyRequest']);

        // GET /api/front/v1/buddy/requests-received/:page/:pageSize
        Route::get('requests-received/{page}/{pageSize}', [BuddyRequestController::class, 'buddyReceived'])
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // GET /api/front/v1/buddy/requests-sent/:page/:pageSize
        Route::get('requests-sent/{page}/{pageSize}', [BuddyRequestController::class, 'buddySent'])
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // POST /api/front/v1/buddy/confirm
        Route::post('confirm', [BuddyRequestController::class, 'buddyConfirm']);

        // POST /api/front/v1/buddy/reject
        Route::post('reject', [BuddyRequestController::class, 'buddyReject']);

        // POST /api/front/v1/buddy/disconnect
        Route::post('disconnect', [BuddyRequestController::class, 'buddyDisconnect']);
    });


    /**
     * Entity: Notifications
     * Table: notifications_to_user
     */
    Route::group(['prefix' => 'notification'], function () {

        // GET /api/front/v1/notification/:id
        Route::get('{id}', [NotificationToUserController::class, 'show'])->where('id', '[0-9]+');

        // GET /api/front/v1/notification
        Route::get('', [NotificationToUserController::class, 'viewed']);

        // GET /api/front/v1/notification/list
        Route::get('/list', [NotificationToUserController::class, 'listNotifications']);

        // DELETE /api/front/v1/notification
        Route::delete('', [NotificationToUserController::class, 'delete']);

        // POST /api/front/v1/notification/date
        Route::post('/date', [DateNowController::class, 'edit']);

        // GET /api/front/v1/notification/scheduler
        Route::get('/scheduler', [SchedulerController::class, 'run']);

        // GET /api/front/v1/notification/scheduler/wellbeing
        Route::get('/scheduler/wellbeing', [SchedulerController::class, 'wellbeing']);
    });

});


/**
 * =====================================================================================================================
 * ADMIN
 * =====================================================================================================================
 */
Route::group(['prefix' => 'admin/v1', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {
    /**
     * Entity: Shoogle
     * Table: shoogles
     */
    Route::group(['prefix' => 'shoogle', 'middleware' => ['admin.superadmin']], function () {
        // POST /api/admin/v1/shoogle/list
        Route::post('list', [ShooglesController::class, 'index']);

        // POST /api/admin/v1/shoogle/:id
        Route::post('{id}', [ShooglesController::class, 'turnOnOff'])->where('id', '[0-9]+');

        // POST /api/admin/v1/shoogle/:id/wellbeing-scores
        Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'averageShoogle'])
            ->where('id', '[0-9]+');

        // POST /api/admin/v1/shoogle/wellbeing-scores
        Route::post('wellbeing-scores', [WelbeingScoresController::class, 'averageCompany']);

        // GET /api/admin/v1/shoogle/:id
        Route::get('{id?}', [ShooglesController::class, 'show'])->where('id', '[0-9]+');

        // POST /api/admin/v1/shoogle/:id
        Route::post('{id}', [ShooglesController::class, 'update'])->where('id', '[0-9]+');
    });

    /**
     * Entity: Company
     * Table: companies
     */
    Route::group(['prefix' => 'company', 'middleware' => ['superadmin']], function () {

        // POST /api/admin/v1/company/list
        Route::post('list', [CompanyController::class, 'index']);

        // GET /api/admin/v1/company/:id
        Route::get('{id}', [CompanyController::class, 'show'])->where('id', '[0-9]+');

        // POST /api/admin/v1/company
        Route::post('', [CompanyController::class, 'create']);

        // POST /api/admin/v1/company/:id
        Route::post('{id}', [CompanyController::class, 'update'])->where('id', '[0-9]+');

        // DELETE /api/admin/v1/company/:id
        Route::delete('{id}', [CompanyController::class, 'destroy'])->where('id', '[0-9]+');

        // GET /api/admin/v1/company/:id/get-access-token
        Route::get('{id}/get-access-token', [CompanyController::class, 'entry'])->where('id', '[0-9]+');

    });

    /**
     * Entity: Company
     * Table: companies
     */
    Route::group(['prefix' => 'company', 'middleware' => ['admin']], function () {

        // GET /api/admin/v1/company/own
        Route::get('own', [CompanyController::class, 'own']);

    });

    /**
     * Entity: Company
     * Table: companies
     */
    Route::group(['prefix' => 'company', 'middleware' => ['admin.superadmin']], function () {

        // POST /api/admin/v1/company/:id/wellbeing-scores
        Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'getAverageCompanyId'])
            ->where('id', '[0-9]+');

    });

    /**
     * Entity: Invite
     * Table: invites
     */
    Route::group(['prefix' => 'invite', 'middleware' => ['admin.superadmin']], function () {

        // GET /api/admin/v1/invite/list
        Route::get('list', [InviteController::class, 'index']);

        // POST /api/admin/v1/invite
        Route::post('', [InviteController::class, 'create']);

        // POST /api/admin/v1/invite/:id
        Route::post('{id}', [InviteController::class, 'update'])->where('id', '[0-9]+');

        // POST /api/admin/v1/invite/csv
        Route::post('csv', [InviteController::class, 'upload']);

        // GET /api/admin/v1/invite/:id
        Route::get('{id}', [InviteController::class, 'show'])->where('id', '[0-9]+');

        // DELETE /api/admin/v1/invite/:id
        Route::delete('{id}', [InviteController::class, 'destroy'])->where('id', '[0-9]+');
    });

    /**
     * Entity: User
     * Table: users
     */
    Route::group(['prefix' => 'user'], function () {

        // GET /api/admin/v1/user/list
        Route::get('list', [UserController::class, 'index'])->middleware(['admin.superadmin']);

        // GET /api/admin/v1/user/:id
        Route::get('{id}', [UserController::class, 'showAdmin'])->where('id', '[0-9]+')->middleware(['admin.superadmin']);

        // POST /api/admin/v1/user/:id
        Route::post('{id}', [UserController::class, 'update'])->where('id', '[0-9]+')->middleware(['admin.superadmin']);

        // POST /api/user/admin/v1
        Route::post('', [UserController::class, 'create'])->middleware(['admin.superadmin']);

        // DELETE /api/admin/v1/user/:id
        Route::delete('{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+')->middleware(['admin.superadmin']);

        // POST /api/admin/v1/user/:id/wellbeing-scores
        Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'averageUserAdmin'])
            ->where('id', '[0-9]+')
            ->middleware(['admin.superadmin']);
    });

    /**
     * Entity: WellbeingCategory
     * Table: wellbeing_categories
     */
    Route::group(['prefix' => 'wellbeing-category', 'middleware' => ['admin.superadmin']], function () {

        // GET /api/admin/v1/wellbeing-category/list
        Route::get('list', [WellbeingCategoryController::class, 'index']);

        // GET /api/admin/v1/wellbeing-category/:id
        Route::get('{id}', [WellbeingCategoryController::class, 'show'])->where('id', '[0-9]+');

        // POST /api/admin/v1/wellbeing-category
        Route::post('', [WellbeingCategoryController::class, 'create']);

        // POST /api/admin/v1/wellbeing-category/:id
        Route::post('{id}', [WellbeingCategoryController::class, 'update'])->where('id', '[0-9]+');

        // DELETE /api/admin/v1/wellbeing-category/:id
        Route::delete('{id}', [WellbeingCategoryController::class, 'destroy'])->where('id', '[0-9]+');

    });

    /**
     * Entity: Department
     * Table: departments
     */
    Route::group(['prefix' => 'department', 'middleware' => ['admin.superadmin']], function () {

        // POST /api/admin/v1/department
        Route::post('', [DepartmentController::class, 'create']);

        // GET /api/admin/v1/department/list
        Route::get('list', [DepartmentController::class, 'index']);

        // GET /api/admin/v1/department/:id
        Route::get('{id}', [DepartmentController::class, 'show'])->where('id', '[0-9]+');

        // POST /api/admin/v1/department/:id
        Route::post('{id}', [DepartmentController::class, 'update'])->where('id', '[0-9]+');

        // DELETE /api/admin/v1/department/:id
        Route::delete('{id}', [DepartmentController::class, 'destroy'])->where('id', '[0-9]+');

        // POST /api/admin/v1/department/:id/wellbeing-scores
        Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'getAverageDepartmentId'])
            ->where('id', '[0-9]+');
    });

    /**
     * Entity: Notifications
     * Table: notifications_to_user
     */
    Route::group(['prefix' => 'notification', 'middleware' => ['admin.superadmin']], function () {

        // GET /api/admin/v1/notification
        Route::get('', [NotificationToUserController::class, 'index']);

        // GET /api/admin/v1/notification/access-denied/:userId
        Route::get('access-denied/{userId}', [NotificationToUserController::class, 'pushAccessDenied'])
            ->where('userId', '[0-9]+');

    });

    /**
     * Entity: Wellbeing scores
     * Table: wellbeing_scores
     */
    Route::group(['prefix' => 'community-data', 'middleware' => ['admin.superadmin']], function () {

        // POST api/admin/v1/community-data/statistic
        Route::post('statistic', [CommunityLevelController::class, 'statistic']);

        // POST api/admin/v1/community-data
        Route::post('', [WellbeingWeekController::class, 'week']);

    });
});

