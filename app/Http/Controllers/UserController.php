<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    private function generateResponse($status, $data)
    {
        return  ["status" => $status, "data" => $data];
    } 
    private function getToken($email, $password)
    {
        $token = null;
        //$credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token' => $token
                ]);
            }
        } catch (JWTAuthException $e) {
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }
        return $token;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:125|email',
            'password' => 'required|max:125'
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'data' => 'invalid input'];

            return response()->json($response, 201);
        }
        $user = \App\Models\User::where('email', $request->email)->get()->last();
        if ($user && \Hash::check($request->password, $user->password)) // The passwords match...
        {
            $token = self::getToken($request->email, $request->password);
            $user->auth_token = $token;
            $user->save();
            $response = ['status' => true, 'data' => ["user" => $user]];
        } else
            $response = ['status' => false, 'data' => "Record doesn't exists"];


        return response()->json($response, 201);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:40|alpha',
            'email' => 'required|max:125|email',
            'password' => 'required|min:6|max:40',
            'phone_number' => 'required|min:4|max:40',
            
        ]);
        if ($validator->fails()) {
            $response = ['status' => false, 'data' => ['invalid input', $validator->errors()]];

            return response()->json($response, 403);
        }
        $userSlug = \uniqid();
        $payload = [
            'name' => $request->name,
            'password' => \Hash::make($request->password),
            'email' => $request->email,
            'slug' => $userSlug,
            'phone_number' => $request->phone_number,
            'auth_token' => '',
        ];

        $user = new User($payload);
        if ($user->save()) {

            $token = self::getToken($request->email, $request->password); // generate user token

            if (!is_string($token))  return response()->json(['status' => false, 'data' => 'Token generation failed'], 201);

            $user = \App\Models\User::where('email', $request->email)->get()->first();

            $user->auth_token = $token; // update user token

            $user->save();

            

            $response = ['status' => true, 'data' => ['name' => $user->name, 'id' => $user->id, 'email' => $request->email, 'auth_token' => $token]];
        } else
            $response = ['status' => false, 'data' => 'Could not register user'];


        return response()->json($response, 200);
    }

    public function getUser(Request $request, User $user)
    {
        # code...
        return response()->json($this->generateResponse("success", ["message" => "Successfully fetched user", "payload" => $user]), 200);
        
    }
    public function getUserStats(Request $request, User $user)
    {
        # code...
        $totalTasks = $user->TodoList()->count();
        $totalLabels = $user->TodoList->filter(function ($todoList) {
            return $todoList->label !== '0';
           })
           ->count();

        //    $timeStats = [];
           $timeStats = $user->TodoList->filter(function ($todoList) {
            return $todoList->status === 'completed';
           })->each(function ($todoList) {
            // global $timeStats;
            // $timeStats = [];
            $started_at = new Carbon($todoList->started_at, "West Central Africa");
            $completed_at = new Carbon($todoList->completed_at, "West Central Africa");
            
            $totalDays = $started_at->diffInDays($completed_at);
            $totalHours = $started_at->diffInHours($completed_at);
            $totalMinute = $started_at->diffInMinutes($completed_at);
            $totalSeconds = $started_at->diffInSeconds($completed_at);
            
            $stats = [
                "id" => $todoList->id,
                "title" => $todoList->title,
                "started_at" => $todoList->started_at,
                "completed_at" => $todoList->completed_at,
                "totalDays" => $totalDays,
                "totalHours" => $totalHours,
                "totalMinute" => $totalMinute,
                "totalSeconds" => $totalSeconds,
            ];
            $todoList->timeStats = $stats;
       
           })->pluck("timeStats");

        return response()->json($this->generateResponse("success", [
            "message" => "Successfully fetched user", 
            "payload" => [
                "totalTasks" => $totalTasks,
                "totalLabels" => $totalLabels,
                "timeStats" => $timeStats
            ]
            ]), 200);
        
    }
}
