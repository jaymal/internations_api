<?php

use Illuminate\Http\Request;

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
Route::post('/login', 'Api\AuthenticationController@login');
Route::post('/register', 'Api\AuthenticationController@register');
Route::get('/list', 'Api\UserController@index');
 
Route::middleware('auth:api')->group(function () {

	//user
    Route::post('user', 'Api\AdminController@store');	
	Route::delete('user/{id}', 'Api\AdminController@destroy');
    Route::put('user/group', 'Api\AdminController@assign');
    Route::put('user/{id}/group', 'Api\AdminController@unAssign');
	//group
    Route::post('group', 'Api\AdminController@groupStore');
	Route::delete('group/{id}', 'Api\AdminController@groupDestroy');	

});
