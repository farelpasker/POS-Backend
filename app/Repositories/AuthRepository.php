<?php

namespace App\Repositories;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthRepository
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function login(array $data): mixed
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (! Auth::attempt($credentials)) {
            throw new \Exception('The provided credentials do not match our records.');
        }

        $user = Auth::user();

        return [
            'user' => new UserResource($user->load('roles')),
            'token' => $user->createToken('API Token')->plainTextToken,
        ];
    }

    public function register(array $data): UserResource
    {
        $user = $this->model->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'photo' => $data['photo'] ?? null,
            'password' => bcrypt($data['password']),
        ]);

        return new UserResource($user->load('roles'));
    }

    public function tokenLogin(array $data): mixed
    {
        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (! Auth::attempt($credentials)) {
            throw new \Exception('Invalid credentials');
        }

        $user = Auth::user();

        return [
            'user' => new UserResource($user->load('roles')),
            'token' => $user->createToken('API Token')->plainTextToken,
        ];
    }
}
