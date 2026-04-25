<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $service;

    public function __construct(AuthService $service) {
        $this->service = $service;
    }

    public function register(RegisterRequest $request) {
        $data = $this->service->register($request->validated());
        return response()->json([
            'message' => 'register succesfuly',
            'data' => $data,
        ], 201);
    }

    public function login(LoginRequest $request) {
        try {
            $data = $this->service->login($request->validated());
            return response()->json([
                'message' => 'login succesfuly',
                'data' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'login failed',
            ], 500);
        }
    }

    public function tokenLogin(LoginRequest $request) {
        $data = $this->service->tokenLogin($request->validated());
        return response()->json([
            'message' => 'login succesfuly',
            'data' => $data,
        ]);
    }

    public function logout(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json([
            'message' => 'logout succesfuly',
        ]);
    }

    public function user(request $request) {
        return response()->json([
            'message' => 'user data',
            'data' => $request->user(),
        ]);
    }
}
