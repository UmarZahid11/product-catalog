<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ProductAPIController;

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

Route::resource('products', 'API\ProductAPIController');
Route::resource('categories', 'API\CategoryAPIController');


// Route::get('products', [ProductAPIController::class, 'index']);
// Route::get('products/{id}', [ProductAPIController::class, 'show']);
// Route::post('products', [ProductAPIController::class, 'create']);
// Route::put('products/{id}', [ProductAPIController::class, 'update']);
// Route::delete('products/{id}', [ProductAPIController::class, 'delete']);