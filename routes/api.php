<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\DepartmentController;
use App\Http\Controllers\API\V1\InviteController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\ShooglesController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\WelbeingScoresController;
use App\Http\Controllers\API\V1\WellbeingCategoryController;
use App\Http\Controllers\TestController;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'v1', 'middleware' => ['auth:api']], function () {

    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');

});

// api/v1 routes
Route::group(['prefix' => 'v1'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('signup', [AuthController::class, 'signup'])->name('signup');
});

/**
 * Entity: Company
 * Table: companies
 */
Route::group(['prefix' => 'v1/company', 'middleware' => ['auth:api', 'superadmin', 'user_already_logged_in', 'cors']], function () {

    // Get a list of companies (no pagination yet)
    // POST /api/v1/company/list
    // {order: 'DESC/ASC'}
    Route::post('list', [CompanyController::class, 'index']);

    // Get company data by ID
    // GET /api/v1/company/:id
    Route::get('{id}', [CompanyController::class, 'show'])->where('id', '[0-9]+');

    // Create a new company
    // POST /api/v1/company
    Route::post('', [CompanyController::class, 'create']);

    // Edit new company
    // POST /api/v1/company/:id
    Route::post('{id}', [CompanyController::class, 'update'])->where('id', '[0-9]+');

    // Delete company
    // DELETE /api/v1/company/:id
    Route::delete('{id}', [CompanyController::class, 'destroy'])->where('id', '[0-9]+');;

    // Entry company
    // GET /api/v1/company/:id/get-access-token
    Route::get('{id}/get-access-token', [CompanyController::class, 'entry']);
});

Route::group(['prefix' => 'v1/company', 'middleware' => ['auth:api', 'admin', 'user_already_logged_in', 'cors']], function () {
    // Current company
    // GET /api/v1/company/own
    Route::get('own', [CompanyController::class, 'own']);
});


/**
 * Entity: Invite
 * Table: invites
 */
Route::group(['prefix' => 'v1/invite', 'middleware' => ['auth:api', 'admin.superadmin', 'user_already_logged_in', 'cors']], function () {
    Route::get('list', [InviteController::class, 'index']);
    Route::post('', [InviteController::class, 'store']);
    Route::post('csv', [InviteController::class, 'upload']);
});

/**
 * Entity: User
 * Table: users
 */
Route::group(['prefix' => 'v1/user', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {
    // User list.
    Route::get('list', [UserController::class, 'index'])->middleware(['admin.superadmin']);

    // Show a user.
    Route::get('{id}', [UserController::class, 'show'])->where('id', '[0-9]+');

    // Edit a user.
    Route::post('{id}', [UserController::class, 'update'])->where('id', '[0-9]+');

    // Create a user.
    Route::post('', [UserController::class, 'create']);

    // Getting wellbeing scores points for a user.
    Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'averageUser'])->where('id', '[0-9]+');
});

/**
 * Entity: WellbeingCategory
 * Table: wellbeing_categories
 */
Route::group(['prefix' => 'v1/wellbeing-category', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {
    Route::get('list', [WellbeingCategoryController::class, 'index']);
    Route::get('{id}', [WellbeingCategoryController::class, 'show']);
    Route::post('', [WellbeingCategoryController::class, 'create']);
    Route::post('{id}', [WellbeingCategoryController::class, 'update']);
    Route::delete('{id}', [WellbeingCategoryController::class, 'destroy']);
});

/**
 * Entity: Shoogle
 * Table: shoogles
 */
Route::group(['prefix' => 'v1/shoogles', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {
    // Shoogles list.
    Route::post('list', [ShooglesController::class, 'index'])->middleware(['admin.superadmin']);

    // Create a shoogle.
    Route::post('', [ShooglesController::class, 'create']);

    // Show a shoogles.
    Route::get('{id?}', [ShooglesController::class, 'show'])->where('id', '[0-9]+');

    // Editing a shoogles.
    Route::post('{id}', [ShooglesController::class, 'update'])->where('id', '[0-9]+');

    // Delete shoogles.
    Route::delete('{id}', [ShooglesController::class, 'destroy'])->where('id', '[0-9]+');

    // Getting wellbeing scores points for a shoogles.
    Route::post('{id}/wellbeing-scores', [WelbeingScoresController::class, 'averageShoogle'])->where('id', '[0-9]+');
});

/**
 * Entity: Profile
 * Table: users
 */
Route::group(['prefix' => 'v1/profile', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

    // Saving a user profile
    Route::put('', [ProfileController::class, 'store']);

    // Retrieving data from a user profile
    Route::get('', [ProfileController::class, 'show']);

});

/**
 * Entity: Department
 * Table: departments
 */
Route::group(['prefix' => 'v1/department', 'middlewar' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

    // Create a department
    Route::post('', [DepartmentController::class, 'create']);

    // List of departments
    Route::get('list', [DepartmentController::class, 'index']);

    // Detailed information on the department
    Route::get('{id}', [DepartmentController::class, 'show'])->where('id', '[0-9]+');

    // Edit department
    Route::post('{id}', [DepartmentController::class, 'update'])->where('id', '[0-9]+');

    // Delete department
    Route::delete('{id}', [DepartmentController::class, 'destroy'])->where('id', '[0-9]+');

});
