<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // list all permission
        $permissions = [
            'create users',
            'edit users',
            'delete users',
            'view users',
            'create roles',
            'edit roles',
            'delete roles',
            'view roles',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'view permissions',
            'create orders',
            'edit orders',
            'delete orders',
            'view orders',
            'create customer',
            'edit customer',
            'delete customer',
            'view customer',
            'create feedbacks',
            'edit feedbacks',
            'delete feedbacks',
            'view feedbacks',
            'create status',
            'edit status',
            'delete status',
            'view status'
        ];
        // set all permission api guard
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'api');
        }
        // admin roles create
        $adminRole = Role::findOrCreate('admin', 'api');
      
        $adminRole->givePermissionTo(Permission::all());
    }
}
