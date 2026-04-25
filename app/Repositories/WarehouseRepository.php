<?php

namespace App\Repositories;

use App\Models\Warehouse;

class WarehouseRepository 
{
    private $model;
    public function __construct(Warehouse $model) {
        $this->model = $model;
    }
    public function getAll(array $fields){
        return $this->model->select($fields)->with(['products.category'])->latest()->paginate(10);
    }

    public function getById(int $id, array $fields){
        return $this->model->select($fields)->with(['products.category'])->findOrFail($id);
    }

    public function create(array $data) {
        return $this->model->create($data);
    }

    public function update(int $id, array $data) {
        $warehouse = $this->model->findOrFail($id);
        $warehouse->update($data);
        return $warehouse;
    }

    public function delete(int $id) {
        $warehouse = $this->model->findOrFail($id);
        $warehouse->delete();
    }
}