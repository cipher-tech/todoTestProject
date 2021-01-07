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
 /* todo list Authentication routes */
Route::post('login', 'App\Http\Controllers\UserController@login');
Route::post('register', 'App\Http\Controllers\UserController@register');

Route::group(['middleware' => 'jwt-auth'], function () {
    /* protected routes. Uses Route model binding defined in RouteServiceProvider  */
    /* todo list routes */
    Route::get('todo-list/{user}', 'App\Http\Controllers\TodoListController@index');
    Route::get('get-task/{todoList}', 'App\Http\Controllers\TodoListController@getTask');
    Route::post('create-todo-list/{user}', 'App\Http\Controllers\TodoListController@create');
    Route::put('todo-list/{todoList}', 'App\Http\Controllers\TodoListController@update');
    Route::delete('todo-list/{todoList}',"App\Http\Controllers\TodoListController@destroy" );
    Route::get('todo-list/start/{todoList}', 'App\Http\Controllers\TodoListController@startTask');
    Route::get('todo-list/completed/{todoList}', 'App\Http\Controllers\TodoListController@completedTask');
    Route::post('todo-list-getByLabel/{user}', 'App\Http\Controllers\TodoListController@getTaskByLabel');
    Route::post('todo-list-getByStatus/{user}', 'App\Http\Controllers\TodoListController@getTaskByStatus');
    Route::post('todo-list-search/{user}', 'App\Http\Controllers\TodoListController@searchTodoList');
    
    /* user routes */
    Route::get('user-info/{user}', 'App\Http\Controllers\UserController@getUser');
    Route::get('user-stats/{user}', 'App\Http\Controllers\UserController@getUserStats');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
