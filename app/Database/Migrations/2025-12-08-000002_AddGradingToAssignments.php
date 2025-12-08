<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGradingToAssignments extends Migration
{
    public function up()
    {
        $fields = [
            'grade' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'file_path',
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'grade',
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'feedback',
            ],
        ];

        $this->forge->addColumn('assignments', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('assignments', ['grade', 'feedback', 'graded_at']);
    }
}

