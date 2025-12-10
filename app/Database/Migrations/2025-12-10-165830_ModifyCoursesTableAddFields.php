<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyCoursesTableAddFields extends Migration
{
    public function up()
    {
        // Add fields for course management and scheduling
        $this->forge->addColumn('courses', [
            'course_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'after'      => 'title',
            ],
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'after'      => 'course_code',
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'after'      => 'teacher_id',
            ],
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['1st', '2nd', 'Summer'],
                'after'      => 'school_year',
            ],
            'schedule_days' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'semester',
            ],
            'schedule_time_start' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'schedule_days',
            ],
            'schedule_time_end' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'schedule_time_start',
            ],
        ]);

        // Add foreign key for teacher
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'CASCADE', 'CASCADE');

        // Add unique constraint for course_code + school_year + semester combination
        $this->db->query("ALTER TABLE courses ADD CONSTRAINT unique_course_per_semester UNIQUE (course_code, school_year, semester)");
    }

    public function down()
    {
        // Drop unique constraint first
        try {
            $this->db->query("ALTER TABLE courses DROP INDEX unique_course_per_semester");
        } catch (\Exception $e) {
            // Index doesn't exist, continue
        }

        // Drop foreign key
        $this->forge->dropForeignKey('courses', 'courses_teacher_id_foreign');

        // Drop columns
        $this->forge->dropColumn('courses', 'course_code');
        $this->forge->dropColumn('courses', 'teacher_id');
        $this->forge->dropColumn('courses', 'school_year');
        $this->forge->dropColumn('courses', 'semester');
        $this->forge->dropColumn('courses', 'schedule_days');
        $this->forge->dropColumn('courses', 'schedule_time_start');
        $this->forge->dropColumn('courses', 'schedule_time_end');
    }
}
