<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeTeacherIdNullable extends Migration
{
    public function up()
    {
        // Drop existing foreign key first (if exists)
        $this->safeDropForeign('courses', 'teacher_id');

        // Modify teacher_id column to allow null
        $this->forge->modifyColumn('courses', [
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        // Re-add foreign key with ON DELETE SET NULL and ON UPDATE CASCADE
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Drop foreign key (if exists)
        $this->safeDropForeign('courses', 'teacher_id');

        // Modify teacher_id column back to NOT NULL
        $this->forge->modifyColumn('courses', [
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);

        // Re-add foreign key as CASCADE on delete/update
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'CASCADE', 'CASCADE');
    }

    /**
     * Safely drop a foreign key referencing a column on a table if it exists.
     */
    private function safeDropForeign(string $table, string $column)
    {
        try {
            $fk = $this->getForeignKeyName($table, $column);

            if ($fk) {
                $this->forge->dropForeignKey($table, $fk);
            }
        } catch (\Exception $e) {
            // ignore if constraint does not exist or other DB-specific issues
        }
    }

    /**
     * Returns the foreign key constraint name for a given table and column, or null.
     */
    private function getForeignKeyName(string $table, string $column)
    {
        // Works for MySQL (information_schema); adapt if using other DB engines
        $dbName = $this->db->database ?? null;

        if (empty($dbName)) {
            return null;
        }

        $sql = "SELECT CONSTRAINT_NAME AS fk FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL LIMIT 1";
        $res = $this->db->query($sql, [$dbName, $table, $column])->getRowArray();

        return $res['fk'] ?? null;
    }
}
