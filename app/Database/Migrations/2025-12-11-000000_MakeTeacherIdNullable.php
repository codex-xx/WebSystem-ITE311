<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeTeacherIdNullable extends Migration
{
    public function up()
    {
        // Drop existing foreign key first
        $this->forge->dropForeignKey('courses', 'courses_teacher_id_foreign');

        // Modify teacher_id column to allow null
        $this->forge->modifyColumn('courses', [
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // Re-add foreign key with ON DELETE SET NULL
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign key
        $this->forge->dropForeignKey('courses', 'courses_teacher_id_foreign');

        // Modify teacher_id column back to NOT NULL
        $this->forge->modifyColumn('courses', [
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        // Re-add foreign key as CASCADE
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }
}
