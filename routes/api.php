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
 
Route::middleware('auth:api','throttle:60,1')->group(function () {

	//user
    Route::post('users', 'Api\AdminController@store')->name('add.users');	
	Route::delete('user/{id}', 'Api\AdminController@destroy')->name('delete.user');
    Route::post('user/group', 'Api\AdminController@assign')->name('assign.group.user');
    Route::delete('user/{id}/group/{groupId}', 'Api\AdminController@unAssign')->name('unassign.group.user');
	//group
    Route::post('group', 'Api\AdminController@groupStore')->name('add.group');;
	Route::delete('group/{id}', 'Api\AdminController@groupDestroy')->name('delete.group');	

});

//Fallback Route ccomes last
Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact info@api-demo.com'], 404);
});