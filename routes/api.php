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

Route::post('assign-categories', 'API\ProductAPIController@saveProductCategories');
Route::post('filter-products', 'API\ProductAPIController@filterProducts');