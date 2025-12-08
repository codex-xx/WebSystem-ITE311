<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTitleToMaterials extends Migration
{
    public function up()
    {
        $fields = [
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'course_id',
            ],
        ];

        $this->forge->addColumn('materials', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('materials', 'title');
    }
}

