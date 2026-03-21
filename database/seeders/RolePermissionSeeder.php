<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        $viewPermission = Permission::firstOrCreate(['name' => 'view']);
        $createPermission = Permission::firstOrCreate(['name' => 'create']);
        $editPermission = Permission::firstOrCreate(['name' => 'edit']);
        $deletePermission = Permission::firstOrCreate(['name' => 'delete']);

        // Reset cache again after creating permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create roles and assign permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([$viewPermission, $createPermission, $editPermission, $deletePermission]);

        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions([$viewPermission, $createPermission, $editPermission]);

        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions([$viewPermission]);

        // Final cache clear
        app()['cache']->forget('spatie.permission.cache');
    }
}
