<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function register(RegisterRequest $request)
    {
        try {
            $data = $this->service->register($request->validated());

            return response()->json([
                'message' => 'Register successful',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->service->login($request->validated());

            return response()->json([
                'message' => 'Login successful',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function tokenLogin(LoginRequest $request)
    {
        try {
            $data = $this->service->tokenLogin($request->validated());

            return response()->json([
                'message' => 'Login successful',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful',
        ], 200);
    }

    public function user(request $request)
    {
        return response()->json([
            'message' => 'user data',
            'data' => $request->user(),
        ]);
    }
}
