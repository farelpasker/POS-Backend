<?php

namespace App\Services;

use App\Repositories\MerchantProductRepository;
use App\Repositories\MerchantRepository;
use App\Repositories\TransactionRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService {

    private TransactionRepository $transactionRepo;
    private ProductRepository $productRepo;
    private MerchantProductRepository $merchantProductRepo;
    private $merchantRepo;

    public function __construct(
        TransactionRepository $transactionRepo,
        ProductRepository $productRepo,
        MerchantProductRepository $merchantProductRepo,
        MerchantRepository $merchantRepo
        ) {
        $this->transactionRepo = $transactionRepo;
        $this->productRepo = $productRepo;
        $this->merchantProductRepo = $merchantProductRepo;
        $this->merchantRepo = $merchantRepo;
    }

    public function createTransaction(array $data) {
        return DB::transaction(function () use ($data){
            $merchant = $this->merchantRepo->getById($data['merchant_id'],['id','keeper_id']);
            if (!$merchant) {
                throw ValidationException::withMessages([
                    'merchant_id' => 'Merchant not found'
                ]);
            }

            if(Auth::id() !== $merchant->keeper_id){
                throw ValidationException::withMessages([
                    'merchant_id' => 'You are not authorized to create transactions for this merchant'
                ]);
            }

            $products = [];
            $subTotal = 0;

            foreach($data['products'] as $productData){
                $merchantProduct = $this->merchantProductRepo->getMerchantAndProduct(
                    $data['merchant_id'],
                    $productData['product_id']
                );

                if(!$merchantProduct || $merchantProduct->stock < $productData['quantity']){
                    throw ValidationException::withMessages([
                        'product_id' => 'Insufficient stock for product ID: ' . $productData['product_id']
                    ]);
                }

                $product = $this->productRepo->getById($productData['product_id'],['price']);

                if(!$product) {
                    throw ValidationException::withMessages([
                        'product_id' => ["Product ID {$productData['product_id']} not found"]
                    ]);
                }

                $price = $product->price;
                $productSubTotal = $productData['quantity'] * $price;
                $subTotal += $productSubTotal;

                $products[] = [
                    'product_id' => $productData['product_id'],
                    'quantity' => $productData['quantity'],
                    'price' => $price,
                    'sub_total' => $productSubTotal,
                ];
                
                $newStock = max(0, $merchantProduct->stock - $productData['quantity']);

                $this->merchantProductRepo->updateStock(
                    $data['merchant_id'],
                    $productData['product_id'],
                    $newStock
                );
            }

            $taxTotal = $subTotal * 0.1;
            $grandTotal = $subTotal + $taxTotal;

            $transaction = $this->transactionRepo->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'merchant_id' => $data['merchant_id'],
                'sub_total' => $subTotal,
                'tax_total' => $taxTotal,
                'grand_total' => $grandTotal,
            ]);

            $this->transactionRepo->createTransactionProducts($transaction->id, $products);
            return $transaction->fresh();
        });
    }
}