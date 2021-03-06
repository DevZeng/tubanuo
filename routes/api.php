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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('notify','UserController@setNotify');
Route::post('school/notify','UserController@setSchoolNotify');
Route::get('access/token','WxController@getAccessToken');
Route::get('login','WxController@login');
Route::get('test','WxController@test');
Route::get('testdb','WxController@testDB');
Route::post('visitor','WxController@addVisitor');
Route::get('user/getUser',"UserController@getUser");
Route::post('user/addUser',"UserController@addUser");
Route::post('user/addTeacher','UserController@addTeacher');
Route::post('user/updateUser','UserController@updateUser');
Route::get('user/class','UserController@getClass');
//学生信息
Route::get('user/getStudent','UserController@getStudent');//获取父母下的子女信息
Route::get('user/likeStudent','UserController@likeStudent');//模糊搜索学生信息
Route::get('user/getOneStudent','UserController@getOneStudent');//获取指定学生的信息

Route::get('user/exStudent','UserController@exStudent');//获取班主任审核家长绑定学生信息
//Route::post('user/addStudent','UserController@addStudent');
Route::get('user/exStatus','UserController@exStatus');//提交审核状态
Route::post('user/saveStudent','UserController@saveStudent');//保存学生信息
Route::get('user/config','UserController@getConfig');
Route::post('insert_user','WxController@insert_user');
//Route::get('user/notice','UserController@Notice');
Route::get('grades','WxController@getGrade');
Route::get('classes','WxController@getClassByGrade');
Route::get('temp','UserController@getImage');
Route::get('user/student','StudentController@getUserStudent');//获取用户学生
Route::get('exam/name','StudentController@getExamName');//获取考试名字
Route::get('exam/detail','StudentController@getExamDetail');//获取考试详情
