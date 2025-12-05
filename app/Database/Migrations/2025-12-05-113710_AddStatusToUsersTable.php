<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
                'default' => 'inactive',
                'after' => 'role',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('assignments');
        $this->forge->dropColumn('users', 'status');
    }
}
