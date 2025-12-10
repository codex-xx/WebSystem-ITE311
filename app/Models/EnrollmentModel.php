<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\NotificationModel;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'course_id', 'enrolled_at', 'status', 'requested_by', 'processed_by', 'processed_at'];

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

        $enrollment_id = $this->insert($data);

        if ($enrollment_id) {
            // Fetch course title
            $course = $this->db->table('courses')->select('title')->where('id', $data['course_id'])->get()->getRow();
            if ($course) {
                // Create notification
                $notificationModel = new NotificationModel();
                $notificationData = [
                    'user_id' => $data['user_id'],
                    'message' => "You have been enrolled in {$course->title}",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $notificationModel->insert($notificationData);
            }
        }

        return $enrollment_id;
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
        $builder->select('enrollments.*, courses.title as course_name, courses.description, courses.course_code, courses.schedule_days, courses.schedule_time_start, courses.schedule_time_end, courses.school_year, courses.semester'); // Adjust fields as per your courses table
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
        // Check for approved, force_enrolled, or pending enrollments
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->whereIn('status', ['approved', 'force_enrolled', 'pending'])
                    ->countAllResults() > 0;
    }

    /**
     * Request enrollment in a course (pending approval).
     *
     * @param int $user_id The student's ID.
     * @param int $course_id The course's ID.
     * @return int|bool Enrollment ID on success, false on failure.
     */
    public function requestEnrollment($user_id, $course_id)
    {
        // Check if course exists
        $course = $this->db->table('courses')->where('id', $course_id)->get()->getRow();
        if (!$course) {
            return false;
        }

        // Check for existing enrollment (any status, not just approved)
        if ($this->isAlreadyEnrolled($user_id, $course_id)) {
            return false;
        }

        // Check for schedule conflicts
        if ($this->hasScheduleConflict($user_id, $course_id)) {
            return false;
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'status' => 'pending',
            'requested_by' => $user_id,
            'enrolled_at' => date('Y-m-d H:i:s'),
        ];

        $enrollment_id = $this->insert($data);

        if ($enrollment_id) {
            if ($course->teacher_id) {
                // Notify teacher
                $notificationModel = new NotificationModel();
                $student = $this->db->table('users')->select('name')->where('id', $user_id)->get()->getRow();
                $notificationData = [
                    'user_id' => $course->teacher_id,
                    'message' => "{$student->name} has requested enrollment in {$course->title}",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $notificationModel->insert($notificationData);
            }
        } else {
            log_message('error', 'Enrollment insert failed: ' . print_r($this->errors(), true));
        }

        return $enrollment_id;
    }

    /**
     * Approve an enrollment request.
     *
     * @param int $enrollment_id The enrollment ID.
     * @param int $processed_by The teacher/admin ID processing the request.
     * @return bool True on success, false on failure.
     */
    public function approveEnrollment($enrollment_id, $processed_by)
    {
        $data = [
            'status' => 'approved',
            'processed_by' => $processed_by,
            'processed_at' => date('Y-m-d H:i:s'),
            'enrolled_at' => date('Y-m-d H:i:s'), // Update enrolled_at when approved
        ];

        $result = $this->update($enrollment_id, $data);

        if ($result) {
            // Get enrollment details for notification
            $enrollment = $this->find($enrollment_id);
            if ($enrollment) {
                $course = $this->db->table('courses')->select('title')->where('id', $enrollment['course_id'])->get()->getRow();
                $notificationModel = new NotificationModel();
                $notificationData = [
                    'user_id' => $enrollment['user_id'],
                    'message' => "Your enrollment request for {$course->title} has been approved",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $notificationModel->insert($notificationData);
            }
        }

        return $result;
    }

    /**
     * Deny an enrollment request.
     *
     * @param int $enrollment_id The enrollment ID.
     * @param int $processed_by The teacher/admin ID processing the request.
     * @return bool True on success, false on failure.
     */
    public function denyEnrollment($enrollment_id, $processed_by)
    {
        $data = [
            'status' => 'denied',
            'processed_by' => $processed_by,
            'processed_at' => date('Y-m-d H:i:s'),
        ];

        $result = $this->update($enrollment_id, $data);

        if ($result) {
            // Get enrollment details for notification
            $enrollment = $this->find($enrollment_id);
            if ($enrollment) {
                $course = $this->db->table('courses')->select('title')->where('id', $enrollment['course_id'])->get()->getRow();
                $notificationModel = new NotificationModel();
                $notificationData = [
                    'user_id' => $enrollment['user_id'],
                    'message' => "Your enrollment request for {$course->title} has been denied",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $notificationModel->insert($notificationData);
            }
        }

        return $result;
    }

    /**
     * Force-enroll a student in a course (admin/teacher action).
     *
     * @param int $user_id The student's ID.
     * @param int $course_id The course's ID.
     * @param int $processed_by The admin/teacher ID performing the action.
     * @return int|bool Enrollment ID on success, false on failure.
     */
    public function forceEnroll($user_id, $course_id, $processed_by)
    {
        // Check for schedule conflicts
        if ($this->hasScheduleConflict($user_id, $course_id)) {
            return false;
        }

        // If there's a pending request, update it; otherwise create new enrollment
        $existing = $this->where('user_id', $user_id)
                        ->where('course_id', $course_id)
                        ->whereIn('status', ['pending', 'denied'])
                        ->first();

        if ($existing) {
            $data = [
                'status' => 'force_enrolled',
                'processed_by' => $processed_by,
                'processed_at' => date('Y-m-d H:i:s'),
                'enrolled_at' => date('Y-m-d H:i:s'),
            ];
            $this->update($existing['id'], $data);

            // Notify student
            $course = $this->db->table('courses')->select('title')->where('id', $course_id)->get()->getRow();
            $notificationModel = new NotificationModel();
            $notificationData = [
                'user_id' => $user_id,
                'message' => "You have been enrolled in {$course->title} by an administrator",
                'created_at' => date('Y-m-d H:i:s')
            ];
            $notificationModel->insert($notificationData);

            return $existing['id'];
        } else {
            $data = [
                'user_id' => $user_id,
                'course_id' => $course_id,
                'status' => 'force_enrolled',
                'processed_by' => $processed_by,
                'enrolled_at' => date('Y-m-d H:i:s'),
                'processed_at' => date('Y-m-d H:i:s'),
            ];
            return $this->insert($data);
        }
    }

    /**
     * Check if enrolling in a course would create schedule conflicts.
     *
     * @param int $user_id The student's ID.
     * @param int $course_id The course's ID.
     * @return bool True if there's a conflict, false otherwise.
     */
    public function hasScheduleConflict($user_id, $course_id)
    {
        // Get the new course's schedule
        $newCourse = $this->db->table('courses')
                            ->select('school_year, semester, schedule_days, schedule_time_start, schedule_time_end')
                            ->where('id', $course_id)
                            ->get()->getRow();

        if (!$newCourse) {
            return false;
        }

        // Get all approved/force_enrolled courses for this user in the same school year and semester
        $existingCourses = $this->db->table('enrollments')
                                   ->select('courses.id, courses.schedule_days, courses.schedule_time_start, courses.schedule_time_end')
                                   ->join('courses', 'courses.id = enrollments.course_id')
                                   ->where('enrollments.user_id', $user_id)
                                   ->whereIn('enrollments.status', ['approved', 'force_enrolled'])
                                   ->where('courses.school_year', $newCourse->school_year)
                                   ->where('courses.semester', $newCourse->semester)
                                   ->get()->getResult();

        // Check for time conflicts on same days
        foreach ($existingCourses as $existing) {
            if ($this->timesOverlap($existing, $newCourse)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if two schedule times overlap.
     *
     * @param object $existing Existing course schedule.
     * @param object $new New course schedule.
     * @return bool True if they overlap.
     */
    private function timesOverlap($existing, $new)
    {
        // Check if they share any days
        $existingDays = explode(',', $existing->schedule_days ?? '');
        $newDays = explode(',', $new->schedule_days ?? '');

        $commonDays = array_intersect(array_map('trim', $existingDays), array_map('trim', $newDays));
        if (empty($commonDays)) {
            return false;
        }

        // Check if times overlap
        $existingStart = strtotime($existing->schedule_time_start);
        $existingEnd = strtotime($existing->schedule_time_end);
        $newStart = strtotime($new->schedule_time_start);
        $newEnd = strtotime($new->schedule_time_end);

        return ($newStart < $existingEnd && $newEnd > $existingStart);
    }

    /**
     * Get enrollment requests for a teacher to review.
     *
     * @param int $teacher_id The teacher's ID.
     * @return array Array of pending enrollment requests.
     */
    public function getPendingRequests($teacher_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('enrollments.*, courses.title as course_title, courses.course_code as course_code, users.name as student_name, users.email as student_email');
        $builder->join('courses', 'courses.id = enrollments.course_id');
        $builder->join('users', 'users.id = enrollments.user_id');
        $builder->where('(courses.teacher_id IS NULL OR courses.teacher_id = ' . $this->db->escape($teacher_id) . ')');
        $builder->where('enrollments.status', 'pending');
        $builder->orderBy('enrollments.enrolled_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get all enrollments for a teacher (across all their courses).
     *
     * @param int $teacher_id The teacher's ID.
     * @return array Array of enrollments.
     */
    public function getEnrollmentsForTeacher($teacher_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('enrollments.*, courses.title as course_title, courses.course_code as course_code, users.name as student_name, users.email as student_email');
        $builder->join('courses', 'courses.id = enrollments.course_id');
        $builder->join('users', 'users.id = enrollments.user_id');
        $builder->where('(courses.teacher_id IS NULL OR courses.teacher_id = ' . $this->db->escape($teacher_id) . ')');
        $builder->orderBy('enrollments.enrolled_at', 'DESC');

        return $builder->get()->getResultArray();
    }
}
