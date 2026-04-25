<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WarehouseService;
use App\Http\Requests\WarehouseProductRequest;
use App\Http\Requests\WarehouseProductUpdateRequest;

class WarehouseProductController extends Controller
{
    private $warehouse;

    public function __construct(WarehouseService $warehouse)
    {
        $this->warehouse = $warehouse;
    }

    public function attach(WarehouseProductRequest $request, int $warehouseId) {
        $this->warehouse->attachProduct(
            $warehouseId,
            $request->input('product_id'),
            $request->input('stock'),
        );

        return response()->json(['message' => 'product attach succesfully']);
    }

    public function detach(int $warehouseId, int $productId) {
        $this->warehouse->detachProduct(
            $warehouseId,
            $productId,
        );

        return response()->json(['message' => 'product detach succesfully']);
    }

    public function update(WarehouseProductUpdateRequest $request, int $warehouseId, int $productId){
        $warehouseProduct = $this->warehouse->updateProductStock(
            $warehouseId,
            $productId,
            $request->validated()['stock'],
        );

        return response()->json([
            'message'=> 'product stock update succesfuly',
            'data' => $warehouseProduct,
            ]);
    }
}
