<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function login(Request $request){
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            
            $credentials = $request->only('email', 'password');
            if(!Auth::attempt($credentials)){
                return ResponseFormatter::error([
                    'message' => 'Unauthorized'
                ], 'Authentication Failed', 500);
            }
            // Generate Token
            $user = User::where('email', $request->email)->first();
            if(!Hash::check($request->password, $user->password)){
                throw new Exception('Invalid Credentials');
            }

            // If success, login
            $access_token = $user->createToken('authToken')->plainTextToken; 
            return ResponseFormatter::success([
                'access_token' => $access_token,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } catch (Exception $exception) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $exception->getMessage()
            ], 'Authentication Failed', 500);

        }

    }
    public function register(Request $request){

        try {
            $request->validate([
                'name' => 'required|string',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|confirmed'
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ]);
        } catch (Exception $exception) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $exception->getMessage()
            ], 'Authentication Failed', 500);
        }
    
    }

    public function logout(Request $request){
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function getCurrentUser(Request $request){
        return ResponseFormatter::success($request->user());
    }
}
