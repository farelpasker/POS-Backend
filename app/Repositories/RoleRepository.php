<?php

namespace App\Repositories;

use Spatie\Permission\Models\Role;


class RoleRepository {
    private $model;

    public function __construct(Role $role){
        $this->model = $role;
    }

    public function getAll($fields)
    {
        return $this->model->select($fields)->latest()->paginate(50);
    }

    public function getById(int $id, $fields) {
        return $this->model->select($fields)->findOrFail($id);
    }

    public function create(array $data) {
        return $this->model->create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);
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
}