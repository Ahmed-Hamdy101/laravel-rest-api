<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // permissions for roles
        $permissions = Permission::all();
        $admin = Role::whereName('Admin')->first();
        DB::table('role_permission')->insert([
            'role_id' => $admin->id,
            'permission_id' => $permissions->id,
        ]);
        // editor can edit roles, users, products, and orders but cannot delete them
        $editor= Role::whereName('Editor')->first();
        foreach ($permissions as $permission) {
            if (!in_array($permission->name, ['edit_roles'])) {
                DB::table('role_permission')->insert([
                    'role_id' => $editor->id,
                    'permission_id' => $permission->whereIn('id', [5, 6, 7, 8])->pluck('id')->toArray(),
                ]);
            }
        }
        // viewer can only view roles, users, products, and orders
        $viewer= Role::whereName('Viewer')->first();
        // define the permissions that the viewer role should have
        $viewerRoles = ['view_roles', 'view_users', 'view_products', 'view_orders'];
        // loop through the permissions and assign the appropriate permissions to the viewer role
        foreach ($permissions as $permission) {
            if (!in_array($permission->name, $viewerRoles)) {
                DB::table('role_permission')->insert([
                    'role_id' => $viewer->id,
                    'permission_id' => $permission->whereIn('id', [5, 6, 7, 8])->pluck('id')->toArray(),
                ]);
            }
        }
    }
}
