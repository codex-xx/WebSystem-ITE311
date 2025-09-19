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
                'name'       => 'Alice Student',
                'email'      => 'alice@student.com',
                'password'   => $defaultPassword,
                'role'       => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($users);
    }
}
