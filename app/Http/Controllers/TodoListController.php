<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TodoListController extends Controller
{
    //
    public $todoStatus = ["completed", "ongoing", "not started"];
    public $priority = ["normal", "important", "very important"];

    private function generateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    }

    public function index(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'per_page' => 'required|min:1|max:10',
        ]);
        
            return TodoList::paginate($request->per_page);
    }
    public function getTask(Request $request, TodoList $todoList)
    {
        # code...
        return response()->json($this->generateResponse("success",["message" => "Task retrieved successfully", "payload" => $todoList ]), 200);
    }
    public function create(Request $request)
    {
        # code...
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|min:1|max:10',
            'title' => 'required|max:125',
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

    public function Update(Request $request, TodoList $todoList)
    {
        # code...
        $validator = Validator::make($request->all(), [
            // 'id' => 'required|min:1|max:10',
            'title' => 'required|max:125',
            'description' => 'required|max:40',
            'label' => 'min:3|max:40',
            'priority' => 'min:4|max:40',
            
        ]);

        if ($validator->fails()) {
            $response = ['status' => false, 'data' => ["message" => 'invalid input', "payload" => $validator->errors()]];

            return response()->json($response, 403);
        }

        // $todoList = TodoList::whereId($request->id)->firstOrFail();

            $todoList->title = $request->title;
            $todoList->description = $request->description;
            $todoList->slug = \Str::slug($request->title);
            $todoList->priority = $request->priority ? $request->priority : $todoList->priority;
            $todoList->status = $request->status ? $request->status : $todoList->status;
            $todoList->label = $request->label ? $request->label : $todoList->label;
            $todoList->estimated_start_date = $request->estimated_start_date? $request->estimated_start_date : $todoList->estimated_start_date;
            $todoList->estimated_end_date =  $request->estimated_end_date? $request->estimated_end_date : $todoList->estimated_end_date;
            $todoList->reminder = $request->reminder ? $request->reminder :  $todoList->reminder;
            $todoList->comment = $request->comment ? $request->comment :  $todoList->comment;

        if ( $todoList->save()) {
            // $recentTodos = TodoList::orderBy('created_at', 'desc')->take(10)->get();
            return response()->json($this->generateResponse("success",["message" => "Task updated","payload" =>  $todoList ]), 200);
        } else {
            return response()->json($this->generateResponse("failed",["message" => "could not update task"]), 402);
        }
    }

    public function destroy(Request $request, TodoList $todoList)
    {
        if ($todoList->delete()) {
            $todoList = App\Models\TodoList::orderBy('created_at', 'desc')->take(10)->get();
            return response()->json(generateResponse("success", ["message" => "Deleted task", "payload" => $todoList]), 200);
        } else {
            return response()->json(generateResponse("failed", ["message" => "could not delete task"]), 402);
        }
    }

    public function startTask(Request $request, TodoList $todoList)
    {
        $todoList->status = $this->todoStatus[1];
        $todoList->started_at = Carbon::now();

        if ($todoList->save()) {
            return response()->json($this->generateResponse("success", ["message" => "Task Started", "payload" => $todoList]), 200);
        } else {
            return response()->json($this->generateResponse("failed",["message" =>  "could not start task"]), 402);
        }
    }

    public function completedTask(Request $request, TodoList $todoList)
    {
        $todoList->status = $this->todoStatus[0];
        $todoList->completed_at = Carbon::now();

        if ($todoList->save()) {
            return response()->json($this->generateResponse("success", ["message" => "Task Started", "payload" => $todoList]), 200);
        } else {
            return response()->json($this->generateResponse("failed",["message" =>  "could not start task"]), 402);
        }
    }

    public function getTaskByLabel(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|min:3|max:40',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'data' => ["message" => 'invalid input', "payload" => $validator->errors()]];

            return response()->json($response, 403);
        }
        $taskByLabel = $user->TodoList->filter(function ($todoList) {
            global $request;
            return $todoList->label === $request->label;
           });
            // TodoList::where('label', $request->label)->get();
        
        return response()->json($this->generateResponse("success", ["message" => "Task by label", "payload" => $taskByLabel]), 200);
    }

    public function getTaskByStatus(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|min:3|max:40',
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'data' => ["message" => 'invalid input', "payload" => $validator->errors()]];

            return response()->json($response, 403);
        }

        $taskByStatus = $user->TodoList->filter(function ($todoList) {
            global $request;
            return $todoList->status === $request->status;
           });
        //    where('status', $request->status)->get();
        
        return response()->json($this->generateResponse("success", ["message" => "Task by status", "payload" => $taskByStatus]), 200);
    }
}
