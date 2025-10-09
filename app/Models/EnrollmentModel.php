<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at'];

    /**
     * Enroll a user in a course.
     *
     * @param array $data Array with 'user_id' and 'course_id' keys.
     * @return int|bool Insert ID on success, false on failure.
     */
    public function enrollUser ($data)
    {
        // Prevent duplicates by checking first
        if ($this->isAlreadyEnrolled($data['user_id'], $data['course_id'])) {
            return false; // Or throw an exception: throw new \Exception('User  already enrolled');
        }

        // Set enrolled_at to now if not provided
        if (!isset($data['enrolled_at'])) {
            $data['enrolled_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    /**
     * Fetch all courses a user is enrolled in (with course details via join).
     *
     * @param int $user_id The user's ID.
     * @return array Array of enrollment records with joined course info.
     */
    public function getUserEnrollments($user_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('enrollments.*, courses.title as course_name, courses.description'); // Adjust fields as per your courses table
        $builder->join('courses', 'courses.id = enrollments.course_id');
        $builder->where('enrollments.user_id', $user_id);
        $builder->orderBy('enrollments.enrolled_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Check if a user is already enrolled in a specific course.
     *
     * @param int $user_id The user's ID.
     * @param int $course_id The course's ID.
     * @return bool True if already enrolled, false otherwise.
     */
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }
}
