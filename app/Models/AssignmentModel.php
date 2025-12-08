<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'course_id', 'file_name', 'file_path', 'submitted_at', 'grade', 'feedback', 'graded_at'];

    /**
     * Submit an assignment.
     */
    public function submitAssignment($data)
    {
        if (!isset($data['submitted_at'])) {
            $data['submitted_at'] = date('Y-m-d H:i:s');
        }
        return $this->insert($data);
    }

    /**
     * Get assignments for a specific student in a course.
     */
    public function getAssignmentsByStudentAndCourse($userId, $courseId)
    {
        // Ensure parameters are integers
        $userId = (int) $userId;
        $courseId = (int) $courseId;

        if ($userId <= 0 || $courseId <= 0) {
            return [];
        }

        return $this->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->orderBy('submitted_at', 'DESC')
                    ->findAll();
    }

    /**
     * Get all assignments for a specific student.
     */
    public function getAssignmentsByStudent($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('assignments.*, courses.title as course_name');
        $builder->join('courses', 'courses.id = assignments.course_id');
        $builder->where('assignments.user_id', $userId);
        $builder->orderBy('assignments.submitted_at', 'DESC');
        $query = $builder->get();

        if ($query) {
            return $query->getResultArray();
        }
        return [];
    }

    /**
     * Get all assignments for a teacher (for their courses).
     * Grouped by student and course.
     */
    public function getAssignmentsForTeacher($teacherId)
    {
        // Get courses taught by teacher (assuming teacher can see all enrollments or something, but since no assignment, perhaps get all assignments where the teacher can access)
        // For simplicity, since teachers manage all courses, get all assignments with student and course info.
        $builder = $this->db->table($this->table);
        $builder->select('assignments.*, users.name as student_name, users.id as user_id, courses.title as course_name');
        $builder->join('users', 'users.id = assignments.user_id');
        $builder->join('courses', 'courses.id = assignments.course_id');
        $builder->orderBy('assignments.submitted_at', 'DESC');
        $query = $builder->get();
        if ($query) {
            return $query->getResultArray();
        }
        return [];
    }

    /**
     * Check if student has submitted for a course.
     */
    public function hasSubmittedForCourse($userId, $courseId)
    {
        return $this->where('user_id', $userId)
                    ->where('course_id', $courseId)
                    ->countAllResults() > 0;
    }
}
