<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Models\TransactionProduct;

class TransactionRepository {

    private $model;

    public function __construct(Transaction $model) {
        $this->model = $model;
    }

    public function getAll($fields) {
        return $this->model->select($fields)
        ->with('transactionProducts.product','merchant.keeper') //eager loading
        ->latest()
        ->paginate(50);
    }

    public function getById(int $id,array $fields) {
        return $this->model->select($fields)
        ->with('transactionProducts.product','merchant.keeper')
        ->findOrFail($id);
    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function update(array $data, int $id) {
        $model = $this->model->findOrFail($id);
        $model->update($data);
        return $model;
    }

    public function delete(int $id) {
        $model = $this->model->findOrFail($id);
        $model->delete();
    }

    public function createTransactionProducts(int $transactionId, array $products){
        foreach ($products as $product) {
            $subTotal = $product['quantity'] * $product['price'];

            TransactionProduct::create([
                'transaction_id' => $transactionId,
                'product_id' => $product['product_id'],
                'quantity' => $product['quantity'],
                'price' => $product['price'],
                'sub_total' => $subTotal,
            ]);
        }
    }

    public function getTransactionsByMerchant(int $merchantId) {
        return $this->model->where('merchant_id', $merchantId)
        ->select(['id','name','phone','merchant_id','grand_total','created_at'])
        ->with('transactionProducts.product','merchant')
        ->latest()
        ->paginate(10);
    }
}