<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $defaultPassword = password_hash('123456', PASSWORD_BCRYPT);

        $users = [
            [
                'name'       => 'Student',
                'email'      => 'student@example.com',
                'password'   => $defaultPassword,
                'role'       => 'student',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Teacher',
                'email'      => 'teacher@example.com',
                'password'   => $defaultPassword,
                'role'       => 'teacher',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Admin',
                'email'      => 'admin@example.com',
                'password'   => $defaultPassword,
                'role'       => 'admin',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Carl',
                'email'      => 'carl@example.com',
                'password'   => $defaultPassword,
                'role'       => 'student',
                'status'     => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
