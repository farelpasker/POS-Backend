<?php

namespace App\Repositories;

use App\Models\User;
use Spatie\Permission\Models\Role;

class UserRoleRepository {
    private $userModel;
    private $roleModel;

    public function __construct(User $user, Role $role){
        $this->userModel = $user;
        $this->roleModel = $role;
    }

    public function assignRole(int $userId, int $roleId){
        $user = $this->userModel->find($userId);
        $role = $this->roleModel->find($roleId);
        $user->assignRole($role);
        return $user;
    }

    public function removeRoleUser(int $userId, int $roleId){
        $user = $this->userModel->find($userId);
        $role = $this->roleModel->find($roleId);
        $user->removeRole($role);
        return $user;
    }

    public function updateRoleUser(int $userId, int $roleId){
        $user = $this->userModel->find($userId);
        $role = $this->roleModel->find($roleId);
        $user->syncRoles($role);
        return $user;
    }

    public function getRoleUserById(int $userId){
        $user = $this->userModel->findOrFail($userId);
        return $user->roles;
    }

    
}