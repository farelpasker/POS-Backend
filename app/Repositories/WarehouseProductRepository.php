<?php

namespace App\Repositories;

use App\Models\WarehouseProduct;
use Illuminate\Validation\ValidationException;


class WarehouseProductRepository 
{
    private $model;

    public function __construct(WarehouseProduct $model){
        $this->model = $model;
    }
    public function getByWarehouseAndProduct(int $warehouseId, int $productId) {
        return $this->model->where('warehouse_id',$warehouseId)
        ->where('product_id',$productId)
        ->first();
    }

    public function updateStock(int $warehouseId, int $productId, int $stock) {
        $warehouseStock = $this->getByWarehouseAndProduct($warehouseId,$productId);

        if(!$warehouseStock) {
            throw ValidationException::withMessages([
                'product_id' => ['product not found for this warehouses'],
            ]);
        }

        $warehouseStock->update(['stock' => $stock]);
        return $warehouseStock;
    }
}