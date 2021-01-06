<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoList;
use Illuminate\Support\Facades\Validator;

class TodoListController extends Controller
{
    //
    public $todoStatus = ["completed", "ongoing", "not started"];
    public $priority = ["normal", "important", "very important"];

    private function generateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }

    public function create(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|min:1|max:10',
            'title' => 'required|max:125|email',
            'description' => 'required|max:40',
            'label' => 'min:3|max:40',
            'priority' => 'min:4|max:40',
            
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'data' => ['invalid input', $validator->errors()]];

            return response()->json($response, 403);
        }

        $todoList = new TodoList([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => \Str::slug($request->title),
            'priority' => $request->priority || $this->priority[0] ,
            'status' => $this->todoStatus[2],
            'label' => $request->label || null,
            'estimated_start_date' => $request->estimated_start_date? $request->estimated_start_date :  null,
            'estimated_end_date' =>  $request->estimated_end_date? $request->estimated_end_date :  null,
            'reminder' => $request->reminder || false,
        ]);

        $todoList->user()->associate($request->user_id);
       
        if ( $todoList->save()) {
            $recentTodos = TodoList::orderBy('created_at', 'desc')->take(10)->get();
            return response()->json($this->generateResponse("success",["Task created", $recentTodos ]), 200);
         } else {
            return response()->json($this->generateResponse("failed","could not create task"), 402);
         }
    }

    public function Update()
    {
        # code...
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|min:1|max:10',
            'title' => 'required|max:125|email',
            'description' => 'required|max:40',
            'label' => 'min:3|max:40',
            'priority' => 'min:4|max:40',
            
        ]);

        if ($validator->fails()) {
            $response = ['status' => false, 'data' => ['invalid input', $validator->errors()]];

            return response()->json($response, 403);
        }

        $todoList = new TodoList([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => \Str::slug($request->title),
            'priority' => $request->priority || $this->priority[0] ,
            'status' => $this->todoStatus[2],
            'label' => $request->label || null,
            'estimated_start_date' => $request->estimated_start_date? $request->estimated_start_date :  null,
            'estimated_end_date' =>  $request->estimated_end_date? $request->estimated_end_date :  null,
            'reminder' => $request->reminder || false,
        ]);

        if ( $todoList->save()) {
            $recentTodos = TodoList::orderBy('created_at', 'desc')->take(10)->get();
            return response()->json($this->generateResponse("success",["Task created", $recentTodos ]), 200);
        } else {
            return response()->json($this->generateResponse("failed","could not create task"), 402);
        }
    }
}
