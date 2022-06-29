<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use JWTAuth;
use App\Models\User;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\ComponentHttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function register(Request $request) {
        $data = $request->only('name','email','password');
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50'
        ]);

        if($validator->fails()) {
            return response()->json(['error'=> $validator->messages()],200);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt ($request->password)
        ]);

        return response()->json([
            'success' => true,
            'messages' => 'User created successfully',
            'data' => $user
        ], Response::HTTP_OK);
    }

    public function authenticate(Request $request) {
        $credentials = $request->only('email','password');
        $validator = Validator::make($credentials,[
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);

        if($validator->fails()) {
            return response()->json(['error'=> $validator->messages()],200);
        }

        try {
            if(! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'messages' => 'Login credentials are invalid'
                    ],400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                    'success' => false,
                    'messages' => 'Could not create token'
                    ],500);
        }
        return response()->json([
                    'success' => true,
                    'token' => $token
        ],200);
    }

    public function logout(Request $request) {
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()],200);
        }
        try {
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'messages' => 'User has been logged out'
            ]);
        } catch (JWTException $exception) {
            return response()->json([
                'succes' => false,
                'messages' => 'Sorry,User cannot be logged out'
            ],Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get_user(Request $request) {
        $this->validate($request,['token' => 'required']);
        $user = JWTAuth::authenticate($request->token);
    }
}
