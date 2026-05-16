<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (Role::coreRoles() as $role) {
            Role::query()->firstOrCreate(['name' => $role]);
        }

        PermissionSeeder::syncBaseRolePermissions();
    }
}
