<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\DepartmentController;
use App\Http\Controllers\API\V1\InviteController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\ShooglerController;
use App\Http\Controllers\API\V1\ShooglesController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\WelbeingScoresController;
use App\Http\Controllers\API\V1\WellbeingCategoryController;
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

Route::group(['prefix' => 'shared/v1'], function () {

    // POST /api/shared/v1/logout
    Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:api');

    // POST /api/shared/v1/login
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // POST /api/shared/v1/password/forgot
    Route::post('password/forgot', [AuthController::class, 'passwordForgot'])->name('password.reset');

    // POST /api/shared/v1/password/reset
    Route::post('password/reset', [AuthController::class, 'passwordReset']);
});


Route::group(['prefix' => 'front/v1'], function () {

    // POST /api/front/v1/signup
    Route::post('signup', [AuthController::class, 'signup'])->name('signup');

    /**
     * Entity: Profile
     * Table: users
     */
    Route::group(['prefix' => 'profile', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

        // PUT /api/front/v1/profile
        Route::put('', [ProfileController::class, 'store']);

        // GET /api/front/v1/profile
        Route::get('', [ProfileController::class, 'show']);

    });

    /**
     * Entity: Shoogle
     * Table: shoogles
     */
    Route::group(['prefix' => 'shoogle', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

        // POST /api/front/v1/shoogles/list
        Route::post('list', [ShooglesController::class, 'index'])->middleware(['admin.superadmin']);

        // GET /api/front/v1/shoogle/list/:page/:pageSize
        Route::get('list/{page}/{pageSize}', [ShooglesController::class, 'userList'])
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // POST /api/front/v1/shoogles
        Route::post('', [ShooglesController::class, 'create']);

        // GET /api/front/v1/shoogles/:id
        Route::get('{id?}', [ShooglesController::class, 'show'])->where('id', '[0-9]+');

        // GET /api/front/v1/shoogle/:id/views
        Route::get('{id}/views', [ShooglesController::class, 'views'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/solo/1
        Route::post('{id}/solo/1', [ShooglesController::class, 'soloYes'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/solo/0
        Route::post('{id}/solo/0', [ShooglesController::class, 'soloNo'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/:id/leave
        Route::post('{id}/leave', [ShooglesController::class, 'leave'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogle/search/:page/:pageSize
        Route::post('search/{page}/{pageSize}', [ShooglesController::class, 'search'])
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');

        // POST /api/front/v1/shoogles/:id
        Route::post('{id}', [ShooglesController::class, 'update'])->where('id', '[0-9]+');

        // DELETE /api/front/v1/shoogles/:id
        Route::delete('{id}', [ShooglesController::class, 'destroy'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogles/:id/wellbeing-scores
        Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'averageShoogle'])->where('id', '[0-9]+');

        // POST /api/front/v1/shoogles/wellbeing-scores
        Route::post('wellbeing-scores', [WelbeingScoresController::class, 'averageCompany']);

        // POST /api/front/v1/shoogles/:id/shooglers/:page/:pageSize
        Route::post('{id}/shooglers/{page}/{pageSize}', [ShooglerController::class, 'index'])
            ->where('id', '[0-9]+')
            ->where('page', '[0-9]+')
            ->where('pageSize', '[0-9]+');
    });

});


Route::group(['prefix' => 'admin/v1'], function () {

    /**
     * Entity: Company
     * Table: companies
     */
    Route::group(['prefix' => 'company', 'middleware' => ['auth:api', 'superadmin', 'user_already_logged_in', 'cors']], function () {

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
    Route::group(['prefix' => 'company', 'middleware' => ['auth:api', 'admin', 'user_already_logged_in', 'cors']], function () {

        // GET /api/admin/v1/company/own
        Route::get('own', [CompanyController::class, 'own']);

    });

    /**
     * Entity: Invite
     * Table: invites
     */
    Route::group(['prefix' => 'invite', 'middleware' => ['auth:api', 'admin.superadmin', 'user_already_logged_in', 'cors']], function () {

        // GET /api/admin/v1/invite/list
        Route::get('list', [InviteController::class, 'index']);

        // POST /api/invite/admin/v1
        Route::post('', [InviteController::class, 'store']);

        // POST /api/admin/v1/invite/csv
        Route::post('csv', [InviteController::class, 'upload']);

        // GET /api/admin/v1/invite/:id
        Route::get('{id}', [InviteController::class, 'show'])->where('id', '[0-9]+');

        // POST /api/admin/v1/invite/:id
        Route::post('{id}', [InviteController::class, 'update'])->where('id', '[0-9]+');

        // DELETE /api/admin/v1/invite/:id
        Route::delete('{id}', [InviteController::class, 'destroy'])->where('id', '[0-9]+');
    });

    /**
     * Entity: User
     * Table: users
     */
    Route::group(['prefix' => 'user', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

        // GET /api/admin/v1/user/list
        Route::get('list', [UserController::class, 'index'])->middleware(['admin.superadmin']);

        // GET /api/admin/v1/user/:id
        Route::get('{id}', [UserController::class, 'show'])->where('id', '[0-9]+');

        // POST /api/admin/v1/user/:id
        Route::post('{id}', [UserController::class, 'update'])->where('id', '[0-9]+');

        // POST /api/user/admin/v1
        Route::post('', [UserController::class, 'create']);

        // POST /api/admin/v1/user/:id/wellbeing-scores
        Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'averageUser'])->where('id', '[0-9]+');

        // DELETE /api/admin/v1/user/:id
        Route::delete('{id}', [UserController::class, 'destroy'])->where('id', '[0-9]+');

    });

    /**
     * Entity: WellbeingCategory
     * Table: wellbeing_categories
     */
    Route::group(['prefix' => 'wellbeing-category', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

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
    Route::group(['prefix' => 'department', 'middlewar' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

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

    });
});
