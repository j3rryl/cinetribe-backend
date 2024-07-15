<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email' => 'required|email|unique:users,email',
            'password'=>'required|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }

        $input = $request->all();
    $input['password'] = bcrypt($input['password']); 
    $user = User::create($input); 

    $accessToken = $user->createToken('cinetribe')->accessToken;

    return response()->json([
        'message' => 'User registered successfully',
        'user' => [
            'name' => $user->name,
            'email' => $user->email,
        ],
        'access_token' => $accessToken,
    ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors(),
            ], 422); 
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user(); 
            $accessToken = $user->createToken('cinetribe')->accessToken; 
            return response()->json([
                'message' => 'User login successful',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'access_token' => $accessToken,
            ]);
        } else {
            return response()->json([
                'message' => 'Incorrect email and/or password',
                'error' => 'unauthorized',
            ], 401); 
        }
    }
}
