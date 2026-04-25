<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleRequest;
use App\Repositories\UserRoleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class UserRoleController extends Controller
{
    private $repo;

    public function __construct(UserRoleRepository $repo){
        $this->repo = $repo;
    }

    public function assignRole(UserRoleRequest $request){
        $user = $this->repo->assignRole(
            $request->validated()['user_id'],
            $request->validated()['role_id']
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Role assigned successfully',
            'data' => $user,
        ]);
    }

    public function removeRole(UserRoleRequest $request) {
        $user = $this->repo->removeRoleUser(
            $request->validated()['user_id'],
            $request->validated()['role_id']
        );
        return response()->json([
            'status' => 'success',
            'message' => 'Role removed successfully',
            'data' => $user,
        ]);
    }

    public function listUserRoles(int $userId) {
        try {
            $role = $this->repo->getRoleUserById($userId);
            return response()->json([
                'status' => 'success',
                'message' => 'Role list fetched successfully',
                'data' => [
                    'user_id' => $userId,
                    'roles' => $role,
                ]
            ]);
        } catch (ModelNotFoundException $e){
            return response()->json([
                'status' => 'error',
                'message' => 'User not found',
                'data' => null,
            ], 404);
        }
    }
}
