<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use App\Repositories\RoleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleController extends Controller
{
    private $roleRepo;
    private $permissionService;

    public function __construct(RoleRepository $roleRepo) {
        $this->roleRepo = $roleRepo;
    }
    public function index() {
        $role = $this->roleRepo->getAll(['id', 'name']);
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil',
            'data' => RoleResource::collection($role),
        ], 200);
    }

    public function show($id) {
        $role = $this->roleRepo->getById($id, ['id', 'name']);
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diambil',
            'data' => new RoleResource($role),
        ], 200);
    }

    public function store(RoleRequest $request) {
        $role = $this->roleRepo->create($request->validated());
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil ditambahkan',
            'data' => new RoleResource($role),
        ], 201);
    }

    public function update(RoleRequest $request, $id) {
        $role = $this->roleRepo->update($request->validated(), $id);
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil diupdate',
            'data' => new RoleResource($role),
        ], 200);
    }

    public function delete($id) {
        $this->roleRepo->delete($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Data berhasil dihapus',
            'data' => null,
        ], 200);
    }   
}
