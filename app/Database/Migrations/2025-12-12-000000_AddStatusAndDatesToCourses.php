<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusAndDatesToCourses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'default'    => 'Inactive',
                'after'      => 'schedule_time_end',
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'status',
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'status');
        $this->forge->dropColumn('courses', 'start_date');
        $this->forge->dropColumn('courses', 'end_date');
    }
}
