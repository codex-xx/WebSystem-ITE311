<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $courses = [
            [
                'title' => 'Mathematics 101',
                'description' => 'Introduction to algebra, calculus, and mathematical reasoning.'
            ],
            [
                'title' => 'Physics Fundamentals',
                'description' => 'Basics of motion, forces, energy, and classical physics principles.'
            ],
            [
                'title' => 'Web Development Essentials',
                'description' => 'Learn HTML, CSS, JavaScript, and PHP for building modern websites.'
            ]
        ];

        // Insert batch into 'courses' table (matches your migration fields)
        $this->db->table('courses')->insertBatch($courses);
    }
}
