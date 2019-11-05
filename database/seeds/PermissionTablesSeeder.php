<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = config('default.root.roles');
        $permissions = config('default.root.permissions');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        \DB::transaction(function () use ($roles, $permissions) {
            // create roles
            $roleArr = [];
            foreach ($roles as $r) {
                $role = Role::create([
                    'uuid' => nanoId(),
                    'name' => $r['name'],
                    'title' => $r['title'],
                    'level' => $r['level'],
                ]);
                $roleArr[$r['name']] = $role;
            }
            // create permissions
            foreach ($permissions as $perm) {
                $permission = Permission::create([
                    'uuid' => nanoId(),
                    'name' => $perm['name'],
                    'title' => $perm['title'],
                ]);

                foreach ($perm['roles'] as $roleName) {
                    $permission->assignRole($roleArr[$roleName]);
                }
            }
        }, 5);
    }
}
