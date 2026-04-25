<?php

namespace App\Repositories;

use App\Models\Merchant;

class MerchantRepository
{
    private $model;

    public function __construct(Merchant $model) {
        $this->model = $model;
    }

    public function getAll(array $fields){
        return $this->model->select($fields)
        ->with("keeper","products.category")
        ->latest()
        ->paginate(10);
    }

    public function getById($id, array $fields){
        return $this->model->select($fields)
        ->with("keeper","products.category")
        ->findOrFail($id);
    }

    public function create(array $data){
        return $this->model->create($data);
    }

    public function update(int $id,array $data){
        $merchant = $this->model->findOrFail($id);
        $merchant->update($data);
        return $merchant;
    }

    public function delete($id){
        $merchant = $this->model->findOrFail($id);
        $merchant->delete();
    }

    public function getKeeperId(int $keeperId, array $fields){ 
        return $this->model->select($fields)
        ->where('keeper_id', $keeperId)
        ->with(["products.category"])
        ->firstOrFail();
    }
}