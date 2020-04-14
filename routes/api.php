<?php

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



Route::group(['middleware'=>['auth:api'], 'namespace'=>'Api'], function () {
    Route::resource('roles', 'RoleController');//give default routes like roles.index, roles.store, roles.update, roles.delete etc
    Route::resource('users', 'UserController');//give default routes like roles.index, roles.store, roles.update, roles.delete etc
    Route::get('verify', 'UserController@verify');
    Route::post('email/verify', 'UserController@verifyEmail');

    Route::post('roles/delete', 'RoleController@deleteAll');
    Route::post('users/delete', 'UserController@deleteAll');
    Route::post('user/role', 'UserController@changeRole');
    Route::post('user/photo', 'UserController@changePhoto');

});
Route::post('login', 'Api\UserController@login')->name('login');


