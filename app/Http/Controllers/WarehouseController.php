<?php

namespace App\Http\Controllers;

use App\Http\Requests\WarehouseRequest;
use App\Http\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    private $warehouse;

    public function __construct(WarehouseService $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    public function index() {
        $fields = ['id','name','photo'];
        $warehouse = $this->warehouse->getAll($fields ?: ['*']);
        return response()->json(WarehouseResource::collection($warehouse));
    }

    public function show(int $id) {
        try{
            $fields = ['id','name','photo','phone','address'];
            $warehouse = $this->warehouse->getById($id,$fields);
            return response()->json(new WarehouseResource($warehouse));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'warehouse not found',
            ], 404);
        }
    }
    
    public function store(WarehouseRequest $request) {
        $warehouse = $this->warehouse->create($request->validated());
        return response()->json(new WarehouseResource($warehouse));
    }

    public function update(WarehouseRequest $request, int $id) {
        try {
            $warehouse = $this->warehouse->update($id, $request->validated());
            return response()->json(new WarehouseResource($warehouse));
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'warehouse not found',
            ],404);
        }
    }

    public function destroy(int $id) {
        try {
            $this->warehouse->delete($id);
            return response()->json([
                'message' => 'warehouse '
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'warehouse not found'
            ],404);
        }
    }
}
