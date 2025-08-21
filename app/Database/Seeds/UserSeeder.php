<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $defaultPassword = password_hash('123456', PASSWORD_DEFAULT);

        $users = [
            [
                'username'   => 'Alice Student',
                'email'      => 'alice@student.com',
                'password'   => $defaultPassword,
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'Bob Teacher',
                'email'      => 'bob@teacher.com',
                'password'   => $defaultPassword,
                'role'       => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'Charlie Admin',
                'email'      => 'charlie@admin.com',
                'password'   => $defaultPassword,
                'role'       => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'David Student',
                'email'      => 'david@student.com',
                'password'   => $defaultPassword,
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username'   => 'Eve Teacher',
                'email'      => 'eve@teacher.com',
                'password'   => $defaultPassword,
                'role'       => 'teacher',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
