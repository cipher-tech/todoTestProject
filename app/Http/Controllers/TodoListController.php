<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TodoList;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class TodoListController extends Controller
{
    /* todo list public properties */
    public $todoStatus = ["completed", "ongoing", "not started"];
    public $priority = ["normal", "important", "very important"];

    private function generateResponse($status, $data)
    {
        /* utility function to generate consistent custom response */
        return  ["status" => $status, "data" => $data];
    }

    public function index(Request $request, User $user)
    {
        # code...

        /* validating client input */
        $validator = Validator::make($request->all(), [
            'per_page' => 'required|min:1|max:10',
        ]);

        /* fetching and returning data*/
        $taskByLabel = $user->TodoList()->paginate($request->per_page);
        return response()->json($taskByLabel, 200);
    }
    public function getTask(Request $request, TodoList $todoList)
    {
        # code...
        /*returning data*/
        return response()->json($this->generateResponse("success", ["message" => "Task retrieved successfully", "payload" => $todoList]), 200);
    }
    public function create(Request $request, User $user)
    {
        # code...
        /* validating client input*/
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:125',
            'description' => 'required|max:40',
            'label' => 'min:3|max:40',
            'priority' => 'min:4|max:40',
        ]);
        if ($validator->fails()) {
            /* checking if validation fails*/
            $response = ['status' => false, 'data' => ['invalid input', $validator->errors()]];

            return response()->json($response, 403);
        }

        /* creating new todo list*/
        $todoList = new TodoList([
            'title' => $request->title,
            'description' => $request->description,
            'slug' => \Str::slug($request->title),
            'priority' => $request->priority || $this->priority[0],
            'status' => $this->todoStatus[2],
            'label' => $request->label ? $request->label :  null,
            'estimated_start_date' => $request->estimated_start_date ? $request->estimated_start_date :  null,
            'estimated_end_date' =>  $request->estimated_end_date ? $request->estimated_end_date :  null,
            'reminder' => $request->reminder || false,
        ]);

        /* Associating User and TodoList Models*/
        $todoList->user()->associate($user->id);

        /*returning data*/
        if ($todoList->save()) {
            return response()->json($this->generateResponse("success", ["Task created", $todoList]), 200);
        } else {
            return response()->json($this->generateResponse("failed", "could not create task"), 402);
        }
    }

    public function Update(Request $request, TodoList $todoList)
    {
        # code...
        /* validating client input */
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:125',
            'description' => 'required|max:40',
            'label' => 'min:3|max:40',
            'priority' => 'min:4|max:40',
            "estimated_start_date" => 'min:3|max:30',
            "estimated_end_date" => 'min:3|max:30',
            "comment" => 'min:3|max:30'

        ]);


        if ($validator->fails()) {
            /* checking if validation fails*/
            $response = ['status' => false, 'data' => ["message" => 'invalid input', "payload" => $validator->errors()]];
            return response()->json($response, 403);
        }

        /* updating todo list*/
        $todoList->title = $request->title;
        $todoList->description = $request->description;
        $todoList->slug = \Str::slug($request->title);
        $todoList->priority = $request->priority ? $request->priority : $todoList->priority;
        $todoList->status = $request->status ? $request->status : $todoList->status;
        $todoList->label = $request->label ? $request->label : $todoList->label;
        $todoList->estimated_start_date = $request->estimated_start_date ? $request->estimated_start_date : $todoList->estimated_start_date;
        $todoList->estimated_end_date =  $request->estimated_end_date ? $request->estimated_end_date : $todoList->estimated_end_date;
        $todoList->reminder = $request->reminder ? $request->reminder :  $todoList->reminder;
        $todoList->comment = $request->comment ? $request->comment :  $todoList->comment;

         /* returning data*/
        if ($todoList->save()) {
            return response()->json($this->generateResponse("success", ["message" => "Task updated", "payload" =>  $todoList]), 200);
        } else {
            return response()->json($this->generateResponse("failed", ["message" => "could not update task"]), 402);
        }
    }

    public function destroy(Request $request, TodoList $todoList)
    {
        if ($todoList->delete()) {
            return response()->json(generateResponse("success", ["message" => "Deleted task"]), 200);
        } else {
            return response()->json(generateResponse("failed", ["message" => "could not delete task"]), 402);
        }
    }

    public function startTask(Request $request, TodoList $todoList)
    {
        /* updating todo list*/
        $todoList->status = $this->todoStatus[1];
        $todoList->started_at = Carbon::now();

         /* returning routes data*/
        if ($todoList->save()) {
            return response()->json($this->generateResponse("success", ["message" => "Task Started", "payload" => $todoList]), 200);
        } else {
            return response()->json($this->generateResponse("failed", ["message" =>  "could not start task"]), 402);
        }
    }

    public function completedTask(Request $request, TodoList $todoList)
    {
        /* updating todo list*/
        $todoList->status = $this->todoStatus[0];
        $todoList->completed_at = Carbon::now();

        /* returning routes data*/
        if ($todoList->save()) {
            return response()->json($this->generateResponse("success", ["message" => "Task Started", "payload" => $todoList]), 200);
        } else {
            return response()->json($this->generateResponse("failed", ["message" =>  "could not start task"]), 402);
        }
    }

    public function getTaskByLabel(Request $request, User $user)
    {
        /* validating client input */
        $validator = Validator::make($request->all(), [
            'label' => 'required|max:40',
        ]);

        if ($validator->fails()) {
            /* checking if validation fails*/
            $response = ['status' => false, 'data' => ["message" => 'invalid input', "payload" => $validator->errors()]];
            return response()->json($response, 403);
        }
        /* fetching data */
        $taskByLabel = $user->TodoList->filter(function ($todoList) {
            global $request;
            return $todoList->label === $request->label;
        });

         /* returning route data*/
        return response()->json($this->generateResponse("success", ["message" => "Task by label", "payload" => $taskByLabel]), 200);
    }

    public function getTaskByStatus(Request $request, User $user)
    {
        /* validating client input */
        $validator = Validator::make($request->all(), [
            'status' => 'required|max:40',
        ]);
        if ($validator->fails()) {
            /* checking if validation fails*/
            $response = ['status' => false, 'data' => ["message" => 'invalid input', "payload" => $validator->errors()]];
            return response()->json($response, 403);
        }

        /* fetching data */
        $taskByStatus = $user->TodoList->filter(function ($todoList) {
            global $request;
            return $todoList->status === $request->status;
        });

        return response()->json($this->generateResponse("success", ["message" => "Task by status", "payload" => $taskByStatus]), 200);
    }
    public function searchTodoList(Request $request, User $user)
    {
        # code...
        /* getting request input */
        $match = $request->get('match');
        /* fetching matched data */
        $suggestion = $user->TodoList()->where('title', 'LIKE', '%' . $match . '%')->orWhere('label', 'LIKE', '%' . $match . '%')->get();
        
        /* returning response */
        if (count($suggestion) > 0)
            return response()->json($this->generateResponse("success", ["message" => "suggestions", "payload" => $suggestion]), 200);
        else return response()->json($this->generateResponse("failed", ["message" => "suggestions", "payload" => []]), 200);
    }
}
