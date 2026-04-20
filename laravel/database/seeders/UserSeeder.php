<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ],
            [
                'name' => 'Client',
                'last_name' => 'User',
                'email' => 'client@test.com',
                'password' => bcrypt('password'),
                'role' => 'client',
            ],
            [
                'name' => 'Cook',
                'last_name' => 'User',
                'email' => 'cook@test.com',
                'password' => bcrypt('password'),
                'role' => 'cook',
            ],
        ];

        foreach ($users as $userData) {
            $roleId = Role::where('name', $userData['role'])->firstOrFail()->id;

            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'last_name' => $userData['last_name'],
                    'password' => $userData['password'],
                    'role_id' => $roleId,
                ]
            );
        }
    }
}
