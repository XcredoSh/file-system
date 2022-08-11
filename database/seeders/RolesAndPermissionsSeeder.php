<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        
        $addUser = 'add user';
        $editUser = 'edit user';
        $showUser = 'show user';
        $deleteUser = 'delete user';

        $addRole = 'add role';
        $editRole = 'edit role';
        $deleteRole = 'delete role';

        $addFile = 'add file';
        $editFile = 'edit file';
        $showFile = 'show file';
        $deleteFile = 'delete file';

        // Create permissions for User Files

        // Manage user permissions
        Permission::create(['name' => $addUser]);
        Permission::create(['name' => $editUser]);
        Permission::create(['name' => $showUser]);
        Permission::create(['name' => $deleteUser]);

        // Manage role permissions
        Permission::create(['name' => $addRole]);
        Permission::create(['name' => $editRole]);
        Permission::create(['name' => $deleteRole]);

        // Manage file permissions
        Permission::create(['name' => $addFile]);
        Permission::create(['name' => $editFile]);
        Permission::create(['name' => $showFile]);
        Permission::create(['name' => $deleteFile]);

        // Define roles available
        $superAdmin = 'super-admin';
        $simpleUser = 'simple-user';
        $moderator = 'moderator';

        // create roles and assign created permissions

        // superAdmin
        $roleSuperAdmin = Role::create(['name' => $superAdmin]);
        $roleSuperAdmin->givePermissionTo(Permission::all());

        // simpleUser
        $roleSimpleUser = Role::create(['name' => $simpleUser]);
        $roleSimpleUser->givePermissionTo([$addFile, $showFile]);

        // moderator
        $roleModerator = Role::create(['name' => $moderator]);
        $roleModerator->givePermissionTo([$deleteFile, $showFile]);
    }
}
