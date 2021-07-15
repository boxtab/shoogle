<?php

use App\Http\Controllers\API\V1\Auth\AuthController;
use App\Http\Controllers\API\V1\CompanyController;
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
