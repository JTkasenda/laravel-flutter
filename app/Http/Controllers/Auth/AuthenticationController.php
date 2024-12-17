<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "name" => ["required"],
            "username" => ["required", "string", "min:4", "unique:users"],
            "email" => ["required", "string", "unique:users"],
            "password" => ["required", "string", "min:6", "confirmed"]
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $userdata = [
            "name" => $request->name,
            "username" => $request->username,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ];

        $user = User::create($userdata);

        $token = $user->createToken("forumapp")->plainTextToken;

        return response([
            "user" => $user,
            "token" => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "username" => ["required", "exists:users,username"],
            "password" => ["required"]
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $user = User::whereUsername($request->username)->first();
        // dd($user);
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(["error" => "Invalid credentials"], 422);
        }
        else{
            return response()->json($user);
        }
    }
}
