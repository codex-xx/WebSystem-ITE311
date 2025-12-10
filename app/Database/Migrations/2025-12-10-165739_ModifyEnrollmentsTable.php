<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyEnrollmentsTable extends Migration
{
    public function up()
    {
        // Add status field for approval workflow
        $this->forge->addColumn('enrollments', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'denied', 'force_enrolled'],
                'default'    => 'pending',
                'after'      => 'enrolled_at',
            ],
            'requested_by' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status',
            ],
            'processed_by' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'requested_by',
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'processed_by',
            ],
        ]);

        // Add foreign keys
        $this->forge->addForeignKey('requested_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('processed_by', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        // Drop foreign keys first
        $this->forge->dropForeignKey('enrollments', 'enrollments_requested_by_foreign');
        $this->forge->dropForeignKey('enrollments', 'enrollments_processed_by_foreign');

        // Drop columns
        $this->forge->dropColumn('enrollments', 'status');
        $this->forge->dropColumn('enrollments', 'requested_by');
        $this->forge->dropColumn('enrollments', 'processed_by');
        $this->forge->dropColumn('enrollments', 'processed_at');
    }
}
