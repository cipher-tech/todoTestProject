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
function generateResponse($status, $data)
{
    return  ["status" => $status, "data" => $data];
}

Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('register', 'App\Http\Controllers\UserController@register');

Route::get('test', function(){
    return "test";
});

Route::group(['middleware' => 'jwt-auth'], function () {
    Route::get('logout', 'App\Http\Controllers\UserController@logout');

    Route::get('todo-list', 'App\Http\Controllers\TodoListController@index');
    Route::post('todo-list', 'App\Http\Controllers\TodoListController@create');
    Route::put('todo-list/{todoList}', 'App\Http\Controllers\TodoListController@update');
    Route::delete('todo-list/{todoList}',"App\Http\Controllers\UserController@destroy" );
    Route::get('todo-list/start/{todoList}', 'App\Http\Controllers\TodoListController@startTask');
    Route::get('todo-list/completed/{todoList}', 'App\Http\Controllers\TodoListController@completedTask');
    Route::get('user-info/{user}', 'App\Http\Controllers\UserController@getUser');
    Route::get('user-stats/{user}', 'App\Http\Controllers\UserController@getUserStats');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
