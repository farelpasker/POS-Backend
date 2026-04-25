<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository {
    private $model;

    public function __construct(Product $model)
    { 
        $this->model = $model; 
    }

    public function getAll($fields) {
        return $this->model->select($fields)->with('category')->latest()->get();
    }

    public function getById($id, $fields) {
        return $this->model->select($fields)->with('category')->findOrFail($id);
    }

    public function store($data) {
        return $this->model->create($data);
    }

    public function update($id, $data) {
        $product = $this->model->findOrFail($id);
        $product->update($data);
        return $product;
    }

    public function delete($id) {
        $product = $this->model->findOrFail($id);
        $product->delete();
    }
}