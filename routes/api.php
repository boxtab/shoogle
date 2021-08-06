<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\DepartmentController;
use App\Http\Controllers\API\V1\InviteController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\ShooglesController;
use App\Http\Controllers\API\V1\UserController;
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
    Route::post('csv', [InviteController::class, 'upload']);
});

/**
 * Entity: User
 * Table: users
 */
Route::group(['prefix' => 'v1/user', 'middleware' => ['auth:api', 'user_already_logged_in', 'cors']], function () {

    Route::get('list', [UserController::class, 'index'])->middleware(['admin.superadmin']);
    // Get user data by ID
    // GET /api/v1/user/:id
    Route::get('{id}', [UserController::class, 'show'])->where('id', '[0-9]+');
    // Edit user
    // POST /api/v1/user/:id
    Route::post('{id}', [UserController::class, 'update'])->where('id', '[0-9]+');
    // Create user
    // POST /api/v1/user/
    Route::post('', [UserController::class, 'create']);
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

    // list request:
    // POST api/v1/shoogles/list
    // {query: 'abc'}
    Route::post('list', [ShooglesController::class, 'index'])->middleware(['admin.superadmin']);

    // shoogles fetch request:
    // GET /api/v1/shoogles/:id
    Route::get('{id?}', [ShooglesController::class, 'show'])->where('id', '[0-9]+');


    // Delete request:
    // DELETE /api/v1/shoogles/:id
    Route::delete('{id}', [ShooglesController::class, 'destroy'])->where('id', '[0-9]+');

    // Create new chat
    // POST /api/v1/shoogles
    Route::post('', [ShooglesController::class, 'create']);


    // Edit chat
    // POST /api/v1/shoogles/:id
    Route::post('{id}', [ShooglesController::class, 'update'])->where('id', '[0-9]+');
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
