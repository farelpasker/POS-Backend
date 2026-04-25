<?php

namespace App\Repositories;

use App\Models\Category;

class CategoryRepository
{
    private $model;
    public function __construct(Category $model) {
        $this->model = $model;
    }
    public function getAll(array $fields){
        return $this->model->select($fields)->latest()->paginate(10);
    }

    public function getById($id, array $fields){
        return $this->model->select($fields)->findOrFail($id);
    }

    public function create(array $data){
        return $this->model->create($data);
    }

    public function update($id, array $data){
        $category = $this->model->findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete(int $id){
        $category = $this->model->findOrFail($id);
        $category->delete();
    }
}