<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    private $repo;
    private $service;

    public function __construct(UserRepository $user, UserService $service) {
        $this->repo = $user;
        $this->service = $service;
    }

    public function index() {
        $fields = ['id', 'name', 'email','phone', 'photo'];
        $users = $this->repo->getAll($fields ?: ['*']);

        return response()->json([
            'message'=> 'Users retrieved successfully',
            'data'=> UserResource::collection($users),
        ], 200);
    }

    public function show(int $id) {
        $fields = ['id', 'name', 'email', 'photo', 'phone'];
        $user = $this->repo->getById($id, $fields ?: ['*']);
        return response()->json([
            'message'=> 'User retrieved successfully',
            'data'=> new UserResource($user),
        ], 200);
    }

    public function store(UserRequest $request) {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $this->service->create($validated);
            DB::commit();
            return response()->json([
                'message' => 'User created successfully',
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UserRequest $request, int $id) {
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $this->service->update($id, $validated);
            DB::commit();
            return response()->json([
                'message' => 'User updated successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message'=> 'User not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'=> 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id) {
        DB::beginTransaction();
        try {
            $this->service->delete($id);
            DB::commit();
            return response()->json([
                'message' => 'User deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
