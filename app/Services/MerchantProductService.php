<?php

namespace App\Services;

use App\Models\Merchant;
use App\Repositories\MerchantProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Repositories\WarehouseProductRepository;
use App\Repositories\MerchantRepository;

class MerchantProductService
{
    private $merchantProductRepository;
    private $warehouseProductRepository;
    private $merchantRepository;

    public function __construct(MerchantProductRepository $merchantProductRepository, WarehouseProductRepository $warehouseProductRepository, MerchantRepository $merchantRepository)
    {
        $this->merchantProductRepository = $merchantProductRepository;
        $this->warehouseProductRepository = $warehouseProductRepository;
        $this->merchantRepository = $merchantRepository;
    }

    public function assignProductToMerchant(array $data) {
        return DB::transaction(function () use ($data) {
            $warehouseProduct = $this->warehouseProductRepository->getByWarehouseAndProduct(
                $data['warehouse_id'],
                $data['product_id'],
            );

            if(!$warehouseProduct || $warehouseProduct->stock < $data['stock']) {
                throw ValidationException::withMessages([
                    'stock'=> ['Insufficient stock in warehouse'],
                ]);
            }

            $existingProduct = $this->merchantProductRepository->getMerchantAndProduct(
                $data['merchant_id'],
                $data['product_id'],
            );

            if($existingProduct) {
                throw ValidationException::withMessages([
                    'product_id'=> ['Product already exists in this merchant'],
                ]);
            }

            $this->warehouseProductRepository->updateStock(
                $data['warehouse_id'],
                $data['product_id'],
                $warehouseProduct->stock - $data['stock'],
            );

            return $this->merchantProductRepository->create(
                [
                    'merchant_id'=> $data['merchant_id'],
                    'product_id'=> $data['product_id'],
                    'warehouse_id'=> $data['warehouse_id'],
                    'stock'=> $data['stock'],
                ]
            );
        });
    }

    public function updateStock(int $merchantId, int $productId, int $newStock, int $warehouseId) {
        return DB::transaction(function () use ($merchantId, $productId, $newStock, $warehouseId) {
            $merchant = $this->merchantRepository->getById($merchantId, ['id']);

        if(!$warehouseId) {
            throw ValidationException::withMessages([
                'warehouse_id'=> ['Warehouse ID is required'],
            ]);
        }

        if(!$merchant) {
            throw ValidationException::withMessages([
                'product'=> ['Merchant not found'],
            ]);
        }

        $product = $this->merchantProductRepository->getMerchantAndProduct($merchantId, $productId);

        if( !$product ) {
            throw ValidationException::withMessages([
                'product' => ['Product not assigned to this merchant'],
            ]);
        }

        $curruntStock = $product->stock;
        if($newStock > $curruntStock) {
            $diff = $newStock - $curruntStock;

            $warehouseProduct = $this->warehouseProductRepository->getByWarehouseAndProduct($warehouseId, $productId);

            if(!$warehouseProduct || $warehouseProduct->stock < $diff) {
                throw ValidationException::withMessages([
                    'stock'=> ['Insufficient stock in warehouse'],
                ]);
            }

            $this->warehouseProductRepository->updateStock($warehouseId, $productId, $warehouseProduct->stock - $diff);
        }
        
        if ($newStock < $curruntStock) {
            $diff = $curruntStock - $newStock;

            $warehouseProduct = $this->warehouseProductRepository->getByWarehouseAndProduct($warehouseId, $productId);

            if(!$warehouseProduct) {
                throw ValidationException::withMessages([
                    'stock'=> ['Product is not available in warehouse'],
                ]);
            }

            $this->warehouseProductRepository->updateStock($warehouseId, $productId, $warehouseProduct->stock + $diff);
        }

        return $this->merchantProductRepository->updateStock($merchantId, $productId, $newStock);
        });
    }

    public function removeProductFromMerchant(int $merchantId, int $productId) {
        $merchant = $this->merchantRepository->getById($merchantId, $fields ?? ['*']);

        if(!$merchant) {
            throw ValidationException::withMessages([
                'product'=> ['Merchant not found'],
            ]);
        }

        $existingProduct = $this->merchantProductRepository->getMerchantAndProduct($merchantId, $productId);

        if( !$existingProduct ) {
            throw ValidationException::withMessages([
                'product' => ['Product not assigned to this merchant'],
            ]);
        }

        $merchant->products()->detach($productId);
    }

}
