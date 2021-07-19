<?php

use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\CompanyController;
use App\Http\Controllers\API\V1\ShooglesController;
use App\Http\Controllers\API\V1\UserController;
use App\Http\Controllers\API\V1\WellbeingCategoru;
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
        ->name('api.v1.logout');

});

// api/v1 routes
Route::group(['prefix' => 'v1'], function () {

    Route::post('login', [AuthController::class, 'login'])
        ->name('api.v1.login');

    Route::post('signup', [AuthController::class, 'signup'])
        ->name('api.v1.signup');

});

/**
 * Entity: Company
 * Table: companies
 */
Route::group(['prefix' => 'v1/company', 'middleware' => ['auth:api']], function () {

    // Get a list of companies (no pagination yet)
    // POST /api/v1/company/list
    // {order: 'DESC/ASC'}
    Route::get('list', [CompanyController::class, 'index']);

    // Get company data by ID
    // GET /api/v1/company/:id
    Route::get('{id}', [CompanyController::class, 'show']);

    // Create a new company
    // POST /api/v1/company
    Route::post('', [CompanyController::class, 'create']);

    // Edit new company
    // POST /api/v1/company/:id
    Route::post('{id}', [CompanyController::class, 'update']);

    // Delete company
    // DELETE /api/v1/company/:id
    Route::delete('{id}', [CompanyController::class, 'destroy']);

});

/**
 * Entity: User
 * Table: users
 */
Route::group(['prefix' => 'v1/user', 'middleware' => ['auth:api']], function () {

    // Get a list of companies (no pagination yet)
    // POST /api/v1/user/list
    // {order: 'DESC/ASC'}
    Route::get('list', [UserController::class, 'index']);

});

/**
 * Entity: WellbeingCategory
 * Table: wellbeing_categories
 */
Route::group(['prefix' => 'v1/wellbeing-category', 'middleware' => ['auth:api']], function () {

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
Route::group(['prefix' => 'v1/shoogle', 'middleware' => ['auth:api']], function () {

    // list request:
    // POST api/v1/shoogle/list
    //{query: 'abc'}
    Route::post('list', [ShooglesController::class, 'index']);

    // shoogle fetch request:
    // GET /api/v1/shoogle/:id
    Route::get('{id}', [ShooglesController::class, 'show']);


    // Delete request:
    // DELETE /api/v1/shoogle/:id
    Route::delete('{id}', [ShooglesController::class, 'destroy']);

    // Create new chat
    // POST /api/v1/shoogle
    Route::post('', [ShooglesController::class, 'create']);


    // Edit chat
    // POST /api/v1/shoogle/:id

});
