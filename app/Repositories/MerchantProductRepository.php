<?php

namespace App\Repositories;

use App\Models\MerchantProduct;
use Illuminate\Validation\ValidationException;

class MerchantProductRepository 
{
    private $model;
    public function __construct(MerchantProduct $model) {
        $this->model = $model;
    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function getMerchantAndProduct(int $merchantId, int $productId) {
        return $this->model
        ->where("merchant_id", $merchantId)
        ->where("product_id", $productId)
        ->first();
    }

    public function updateStock(int $merchantId, int $productId, int $stock) {
        $merchantProduct = $this->getMerchantAndProduct($merchantId, $productId);
        if(!$merchantProduct) {
            throw ValidationException::withMessages([
                "product_id"=> "Product not found in merchant"
            ]);
        }
        
        $merchantProduct->update([
            'stock' => $stock,
        ]);

        return $merchantProduct;
    }
}