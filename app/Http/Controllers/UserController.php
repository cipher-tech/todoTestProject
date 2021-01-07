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
        /* getting JWT token */
        $token = null;
        try {
            /* generate token */
            if (!$token = JWTAuth::attempt(['email' => $email, 'password' => $password])) {
                return response()->json([
                    'response' => 'error',
                    'message' => 'Password or email is invalid',
                    'token' => $token
                ]);
            }
        } catch (JWTAuthException $e) {
            /* if token generation fails */
            return response()->json([
                'response' => 'error',
                'message' => 'Token creation failed',
            ]);
        }
        return $token;
    }

    public function login(Request $request)
    {
        /* validating client input */
        $validator = Validator::make($request->all(), [
            'email' => 'required|max:125|email',
            'password' => 'required|max:125'
        ]);
        if ($validator->fails()) {
            /* checking if validation fails*/
            $response = ['status' => false, 'data' => 'invalid input'];
            return response()->json($response, 201);
        }

        /* fetch user */
        $user = \App\Models\User::where('email', $request->email)->get()->last();
        if ($user && \Hash::check($request->password, $user->password)) // The passwords match...
        {
            /* generate user JWT token */
            $token = self::getToken($request->email, $request->password);
            $user->auth_token = $token;
            $user->save();

            $response = ['status' => true, 'data' => ["Logged in user" => $user]];
        } else
            $response = ['status' => false, 'data' => "Record doesn't exists"];


        return response()->json($response, 201);
    }

    public function register(Request $request)
    {
        // validating client input
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3|max:40|alpha',
            'email' => 'required|max:125|email',
            'password' => 'required|min:6|max:40',
            'phone_number' => 'required|min:4|max:40',

        ]);

        if ($validator->fails()) {
            /* checking if validation fails*/
            $response = ['status' => false, 'data' => ['invalid input', $validator->errors()]];

            return response()->json($response, 403);
        }

        /* defining new user */
        $payload = [
            'name' => $request->name,
            'password' => \Hash::make($request->password),
            'email' => $request->email,
            'slug' => \Str::slug($request->name),
            'phone_number' => $request->phone_number,
            'auth_token' => '',
        ];

        /* creating new user */
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
        // fetch single user
        # code...
        return response()->json($this->generateResponse("success", ["message" => "Successfully fetched user", "payload" => $user]), 200);
    }
    public function getUserStats(Request $request, User $user)
    {
        // generate user statistics
        # code...

        // get total tasks 
        $totalTasks = $user->TodoList()->count();
        
        // / get total label
        $totalLabels = $user->TodoList->filter(function ($todoList) {
            return $todoList->label !== '0';
        })
        ->count();

        // / statistics for time and label
        $timeStats = $user->TodoList->filter(function ($todoList) {
            return $todoList->status === 'completed';
        })->each(function ($todoList) {
            $started_at = new Carbon($todoList->started_at, "West Central Africa");
            $completed_at = new Carbon($todoList->completed_at, "West Central Africa");

            $totalDays = $started_at->diffInDays($completed_at);
            $totalHours = $started_at->diffInHours($completed_at);
            $totalMinute = $started_at->diffInMinutes($completed_at);
            $totalSeconds = $started_at->diffInSeconds($completed_at);

            /* building the time stats object */
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

        /* returning response */
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
