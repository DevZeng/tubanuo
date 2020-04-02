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
Route::post('user/updateUser','UserController@updateUser');
//学生信息
Route::get('user/getStudent','UserController@getStudent');//获取父母下的子女信息
Route::get('user/likeStudent','UserController@likeStudent');//模糊搜索学生信息
Route::get('user/getOneStudent','UserController@getOneStudent');//获取指定学生的信息

Route::get('user/exStudent','UserController@exStudent');//班主任审核家长绑定学生信息
Route::post('user/addStudent','UserController@addStudent');//提交绑定学生信息给班主任审核
Route::post('user/addStudent','UserController@addStudent');
Route::get('user/config','UserController@getConfig');
