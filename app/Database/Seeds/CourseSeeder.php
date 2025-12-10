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
                    'teacher_id' => 2, // teacher user ID
                    'school_year' => '2024-2025',
                    'semester' => '1st',
                    'schedule_days' => 'Monday, Wednesday, Friday',
                    'schedule_time_start' => '09:00:00',
                    'schedule_time_end' => '10:30:00',
                ],
                [
                    'title' => 'Data Structures and Algorithms',
                    'course_code' => 'CS201',
                    'description' => 'Advanced data structures and algorithm analysis.',
                    'teacher_id' => 2,
                    'school_year' => '2024-2025',
                    'semester' => '2nd',
                    'schedule_days' => 'Tuesday, Thursday',
                    'schedule_time_start' => '13:00:00',
                    'schedule_time_end' => '14:30:00',
                ],
                [
                    'title' => 'Mathematics 101',
                    'course_code' => 'MATH101',
                    'description' => 'Introduction to algebra, calculus, and mathematical reasoning.',
                    'teacher_id' => 3,
                    'school_year' => '2024-2025',
                    'semester' => '1st',
                    'schedule_days' => 'Monday, Tuesday, Wednesday',
                    'schedule_time_start' => '08:00:00',
                    'schedule_time_end' => '09:00:00',
                ],
                [
                    'title' => 'Advanced Data Structures',
                    'course_code' => 'CS301',
                    'description' => 'Advanced algorithms and data structures analysis.',
                    'teacher_id' => 2,
                    'school_year' => '2023-2024',
                    'semester' => '2nd',
                    'schedule_days' => 'Tuesday, Thursday, Saturday',
                    'schedule_time_start' => '14:00:00',
                    'schedule_time_end' => '16:00:00',
                ],
                [
                    'title' => 'Summer Physics',
                    'course_code' => 'PHY101',
                    'description' => 'Basic physics concepts and summer intensive.',
                    'teacher_id' => 3,
                    'school_year' => '2024-2025',
                    'semester' => 'Summer',
                    'schedule_days' => 'Monday, Wednesday, Friday',
                    'schedule_time_start' => '10:00:00',
                    'schedule_time_end' => '12:00:00',
                ]
            ];

            $this->db->table('courses')->insertBatch($courses);
        } else {
            // Update existing courses with schedule information
            $scheduleData = [
                1 => [
                    'course_code' => 'CS101',
                    'teacher_id' => 2,
                    'school_year' => '2024-2025',
                    'semester' => '1st',
                    'schedule_days' => 'Monday, Wednesday, Friday',
                    'schedule_time_start' => '09:00:00',
                    'schedule_time_end' => '10:30:00',
                ],
                2 => [
                    'course_code' => 'PHYSICS101',
                    'teacher_id' => 3,
                    'school_year' => '2024-2025',
                    'semester' => '1st',
                    'schedule_days' => 'Tuesday, Thursday',
                    'schedule_time_start' => '10:00:00',
                    'schedule_time_end' => '11:30:00',
                ],
                3 => [
                    'course_code' => 'WEB101',
                    'teacher_id' => 2,
                    'school_year' => '2024-2025',
                    'semester' => '2nd',
                    'schedule_days' => 'Wednesday, Friday',
                    'schedule_time_start' => '14:00:00',
                    'schedule_time_end' => '15:30:00',
                ]
            ];

            foreach ($scheduleData as $id => $data) {
                $this->db->table('courses')->where('id', $id)->update($data);
            }
        }

        // Create sample enrollments for a student
        $student = $this->db->table('users')->where('role', 'student')->get()->getRow();
        if ($student) {
            $existingEnrollments = $this->db->table('enrollments')->where('user_id', $student->id)->get()->getResultArray();

            if (empty($existingEnrollments)) {
                $enrollments = [
                    [
                        'user_id' => $student->id,
                        'course_id' => 1, // CS101 - 2024-2025 1st
                        'status' => 'approved',
                        'enrolled_at' => date('Y-m-d H:i:s'),
                    ],
                    [
                        'user_id' => $student->id,
                        'course_id' => 2, // CS201 - 2024-2025 2nd
                        'status' => 'approved',
                        'enrolled_at' => date('Y-m-d H:i:s', strtotime('-2 months')),
                    ],
                    [
                        'user_id' => $student->id,
                        'course_id' => 4, // CS301 - 2023-2024 2nd
                        'status' => 'approved',
                        'enrolled_at' => date('Y-m-d H:i:s', strtotime('-3 months')),
                    ],
                    [
                        'user_id' => $student->id,
                        'course_id' => 5, // PHY101 - 2024-2025 Summer
                        'status' => 'approved',
                        'enrolled_at' => date('Y-m-d H:i:s', strtotime('-4 months')),
                    ]
                ];

                $this->db->table('enrollments')->insertBatch($enrollments);
            }
        }
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
