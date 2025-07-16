<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage schools']);
        Permission::create(['name' => 'manage teachers']);
        Permission::create(['name' => 'manage students']);
        Permission::create(['name' => 'manage staff']);

        // create roles and assign created permissions

        $role = Role::create(['name' => 'admin_dinas']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'admin_sekolah']);
        $role->givePermissionTo(['manage teachers', 'manage students', 'manage staff']);

        $role = Role::create(['name' => 'guru']);
        $role->givePermissionTo([]);
    }
}
