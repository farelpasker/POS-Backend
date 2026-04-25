<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthRepository {
    
    private $model;

    public function __construct(User $model){
        $this->model = $model;
    }

    public function login(array $data) {
        $credentials = [
            "email" => $data['email'],
            "password" => $data['password'],
        ];

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.'
            ], 401);
        }

        request()->session()->regenerate();

        $user = Auth::user();
        return response()->json([
            "message" => "login succesfull",
            'data' => new UserResource($user->load('roles')),
        ], 200);
    }

    public function register(array $data) {
        return $this->model->create([
            'name'=> $data['name'],
            'email'=> $data['email'],
            'phone'=> $data['phone'],
            'photo'=> $data['photo'],
            'password'=> bcrypt($data['password']),
        ]);
    }

    public function tokenLogin(array $data) {
        if(!Auth::attempt(['email' => $data['email'], 'password' => $data['password']])) {
            return response()->json([
                'message' => 'Invalid credentials',
                'data' => null
            ],401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource($user->load('roles')),
                'token' => $token,
            ]
        ], 200);
    }
}