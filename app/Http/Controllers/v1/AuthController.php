<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        // validate incoming request
        $validatedData = $request->validate([
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'name'       => 'required|string',
            'email'      => 'required|string|email|unique:users',
            'password'   => 'required|string|confirmed|min:6|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required'
        ]);

        $validatedData['password'] = Hash::make($validatedData['password']);

        if (User::query()->create($validatedData)) {
            return response()->json(null, 201);
        }

        return response()->json(null, 401);

    }


    public function login(Request $request): JsonResponse
    {
        // validate incoming request
        $validatedData = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::query()->where('email', $validatedData['email'])->first();

        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }
}
