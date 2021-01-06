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
    Route::put('todo-list', 'App\Http\Controllers\TodoListController@update');
    Route::delete('todo-list/{todoList}',  function($todoList) {
        
        if ($todoList->delete()) {
            $todoList = App\Models\TodoList::orderBy('created_at', 'desc')->take(10)->get();
            return response()->json(generateResponse("success", ["Deleted task", $todoList]), 200);
        } else {
            return response()->json(generateResponse("failed", "could not delete task"), 402);
        }
    });
    Route::get('todo-list/start/{todoList}', 'App\Http\Controllers\TodoListController@startTask');
    Route::get('todo-list/completed/{todoList}', 'App\Http\Controllers\TodoListController@completedTask');

    
    Route::get('user-info/{user}', 'App\Http\Controllers\UserController@getUser');
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
