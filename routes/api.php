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
Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('register', 'App\Http\Controllers\UserController@register');

Route::get('test', function(){
    return "test";
});

Route::group(['middleware' => 'jwt-auth'], function () {

    Route::post('todo-list', 'App\Http\Controllers\TodoListController@create');
    Route::put('todo-list', 'App\Http\Controllers\TodoListController@update');

    Route::get('logout', 'App\Http\Controllers\UserController@logout');
    Route::get('user-info/{user}', function($user) {
        return response()->json($user, 200);
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
