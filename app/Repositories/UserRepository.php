<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository {
    private $model;

    public function __construct(User $user){
        $this->model = $user;
    }

    public function getAll($fields) 
    {
        return $this->model->select($fields)->latest()->paginate(50);
    }

    public function getById($id, array $fields) {
        return $this->model->select($fields)->findOrFail($id);
    }

    public function findById($id) {
        return $this->model->findOrFail($id);
    }

    public function store(array $data) {
        return $this->model->create($data);
    }

    public function update(array $data, $id) {
        $model = $this->findById($id);
        $model->update($data);
        return $model;
    }

    public function delete($id) {
        $model = $this->findById($id);
        $model->delete();
    }
}