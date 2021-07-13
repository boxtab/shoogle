<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// api/v1 routes
Route::group(['prefix' => 'v1'], function () {

    Route::match(['GET', 'POST'], 'test', [TestController::class, 'index'])
        ->name('api.v1.test');

    Route::post('create', [TestController::class, 'create'])
        ->name('api.v1.create');

});
