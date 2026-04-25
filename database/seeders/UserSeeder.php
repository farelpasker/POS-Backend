<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ['manager','keeper','customer'];

        $permissions = ['create role','edit role','delete role','view role'];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name'=> $roleName]);
        }

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $managerRole = Role::where('name','manager')->first();
        $managerRole->givePermissionTo($permissions);

        foreach($roles as $roleName) {
            $user = User::create([
                'name' => ucfirst($roleName).'User',
                'email' => $roleName.'@gmail.com',
                'phone' => fake()->phoneNumber(),
                'photo' => fake()->imageUrl(200,200,'people',true,'profile'),
                'password' => bcrypt('password'),
            ]);

            $user->assignRole($roleName);
        }
    }
}
