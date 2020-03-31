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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('access/token','WxController@getAccessToken');
Route::get('login','WxController@login');
Route::post('visitor','WxController@addVisitor');
Route::get('user/getUser',"UserController@getUser");
Route::post('user/addUser',"UserController@addUser");
Route::post('user/addTeacher','UserController@addTeacher');
//Route::post('user/addStudent','UserController@addStudent');
Route::post('user/updateUser','UserController@updateUser');

