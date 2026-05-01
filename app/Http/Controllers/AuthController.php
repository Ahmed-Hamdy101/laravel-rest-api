<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user  = Auth::user()->load('role');
            $token = $user->createToken('admin')->accessToken;

            return response()->json([
                'token' => $token,
                'user'  => [
                    'id'        => $user->id,
                    'full_name' => trim("{$user->f_name} {$user->l_name}"),
                    'email'     => $user->email,
                    'role'      => $user->role?->name,
                ],
            ]);
        }

        return response()->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data             = $request->only(['f_name', 'l_name', 'email']);
        $data['password'] = Hash::make($request->input('password'));

        $user  = User::create($data);
        $token = $user->createToken('admin')->accessToken;

        return response()->json([
            'message' => 'User created successfully',
            'token'   => $token,
            'user'    => [
                'id'        => $user->id,
                'full_name' => trim("{$user->f_name} {$user->l_name}"),
                'email'     => $user->email,
                'role'      => $user->role?->name,
            ],
        ], Response::HTTP_CREATED);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
