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
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            // Cek apakah email ada di database
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return ResponseFormatter::error([
                    'email' => 'Email tidak terdaftar'
                ], 404);
            }

            // Cek apakah password cocok
            if (!Hash::check($request->password, $user->password)) {
                return ResponseFormatter::error([
                    'password' => 'Password salah'
                ], 401);
            }

            // Jika email dan password benar, buat token
            $access_token = $user->createToken('authToken')->plainTextToken;

            return ResponseFormatter::success([
                'access_token' => $access_token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'Login berhasil');
        } catch (Exception $exception) {
            return ResponseFormatter::error([
                'message' => 'Terjadi kesalahan',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function register(Request $request)
    {

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

            // Buat UserPreference otomatis 
            $user->userPreference()->create();

            $tokenResult = $user->createToken('authToken')->plainTextToken;
            return ResponseFormatter::success([
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
                'user' => $user
            ], 'User Registered');
        } catch (ValidationException $exception) {
            return ResponseFormatter::error($exception->errors(), 422);
        } catch (\Exception $exception) {
            return ResponseFormatter::error($exception->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        $token = $request->user()->currentAccessToken()->delete();
        return ResponseFormatter::success($token, 'Token Revoked');
    }

    public function getCurrentUser(Request $request)
    {
        return ResponseFormatter::success($request->user());
    }
}
