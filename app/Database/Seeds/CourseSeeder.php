<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // First, add sample users if they don't exist
        $this->addSampleUsers();

        // Get existing courses or create new ones with schedule data
        $existingCourses = $this->db->table('courses')->get()->getResultArray();

        if (empty($existingCourses)) {
            $courses = [
                [
                    'title' => 'Introduction to Computer Science',
                    'course_code' => 'CS101',
                    'description' => 'Basic programming concepts and problem-solving techniques.',
                    'status' => 'Inactive', // Start as inactive - admin must configure
                ],
                [
                    'title' => 'Data Structures and Algorithms',
                    'course_code' => 'CS201',
                    'description' => 'Advanced data structures and algorithm analysis.',
                    'status' => 'Inactive',
                ],
                [
                    'title' => 'Mathematics 101',
                    'course_code' => 'MATH101',
                    'description' => 'Introduction to algebra, calculus, and mathematical reasoning.',
                    'status' => 'Inactive',
                ],
                [
                    'title' => 'Advanced Data Structures',
                    'course_code' => 'CS301',
                    'description' => 'Advanced algorithms and data structures analysis.',
                    'status' => 'Inactive',
                ],
                [
                    'title' => 'Summer Physics',
                    'course_code' => 'PHY101',
                    'description' => 'Basic physics concepts and summer intensive.',
                    'status' => 'Inactive',
                ]
            ];

            $this->db->table('courses')->insertBatch($courses);
        } else {
            // For existing courses, just ensure they have proper course codes and set to inactive
            // Remove any pre-configured schedule information - admin must set everything manually
            $resetData = [
                1 => ['course_code' => 'CS101', 'status' => 'Inactive', 'teacher_id' => null, 'school_year' => null, 'semester' => null, 'schedule_days' => null, 'schedule_time_start' => null, 'schedule_time_end' => null],
                2 => ['course_code' => 'CS201', 'status' => 'Inactive', 'teacher_id' => null, 'school_year' => null, 'semester' => null, 'schedule_days' => null, 'schedule_time_start' => null, 'schedule_time_end' => null],
                3 => ['course_code' => 'MATH101', 'status' => 'Inactive', 'teacher_id' => null, 'school_year' => null, 'semester' => null, 'schedule_days' => null, 'schedule_time_start' => null, 'schedule_time_end' => null],
                4 => ['course_code' => 'CS301', 'status' => 'Inactive', 'teacher_id' => null, 'school_year' => null, 'semester' => null, 'schedule_days' => null, 'schedule_time_start' => null, 'schedule_time_end' => null],
                5 => ['course_code' => 'PHY101', 'status' => 'Inactive', 'teacher_id' => null, 'school_year' => null, 'semester' => null, 'schedule_days' => null, 'schedule_time_start' => null, 'schedule_time_end' => null],
            ];

            foreach ($resetData as $id => $data) {
                $this->db->table('courses')->where('id', $id)->update($data);
            }
        }

        // Note: No enrollments created since courses start as inactive
        // Admin must configure courses (assign teacher, set schedule, etc.) before they appear for enrollment
    }

    private function addSampleUsers()
    {
        // Check if sample users exist
        $teachers = $this->db->table('users')->where('role', 'teacher')->limit(1)->get()->getRow();
        if (!$teachers) {
            $sampleUsers = [
                [
                    'name' => 'Teacher One',
                    'email' => 'teacher1@example.com',
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'role' => 'teacher',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Teacher Two',
                    'email' => 'teacher2@example.com',
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'role' => 'teacher',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                ],
                [
                    'name' => 'Student One',
                    'email' => 'student1@example.com',
                    'password' => password_hash('password123', PASSWORD_DEFAULT),
                    'role' => 'student',
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s'),
                ]
            ];

            $this->db->table('users')->insertBatch($sampleUsers);
        }
    }
}
