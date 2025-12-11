<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Course extends Controller
{
    public function __construct()
    {
        // Optional: Load helpers or libraries here
        helper('url');
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('courses');
        $courses = $builder->get()->getResultArray();

        // Check enrollment status for logged-in students
        $enrollmentStatuses = [];
        $user_id = session()->get('user_id');
        $user_role = session()->get('role');
        if ($user_id && $user_role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            foreach ($courses as $course) {
                $enrollmentStatuses[$course['id']] = $enrollmentModel->isAlreadyEnrolled($user_id, $course['id']);
            }
        }

        return view('courses/index', [
            'courses' => $courses,
            'enrollmentStatuses' => $enrollmentStatuses,
            'user_role' => $user_role
        ]);
    }

    /**
     * Handle AJAX enrollment request (pending approval).
     * Expects POST with 'course_id'.
     * Returns JSON response.
     */
    public function enroll()
    {
        // Check if user is logged in (adjust based on your auth system)
        $user_id = session()->get('user_id');  // Or: auth()->user()->id if using Myth/Auth
        if (!$user_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please log in to enroll.'
            ])->setStatusCode(401);
        }

        // Check if user is a student
        $user_role = session()->get('role');
        if ($user_role !== 'student') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Only students can request enrollment.'
            ])->setStatusCode(403);
        }

        // Get course_id from POST
        $course_id = $this->request->getPost('course_id');
        if (!$course_id || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        // Instantiate model
        $enrollmentModel = new EnrollmentModel();

        // Request enrollment (pending approval)
        $enrollment_id = $enrollmentModel->requestEnrollment($user_id, $course_id);

        if ($enrollment_id) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment request submitted. Waiting for teacher approval.',
                'enrollment_id' => $enrollment_id
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to submit enrollment request. Check for conflicts or existing enrollment.'
            ])->setStatusCode(400);
        }
    }

    public function search()
    {
        $request = service('request');
        $term = $request->getVar('term');

        $db = \Config\Database::connect();
        $builder = $db->table('courses');

        if (!empty($term)) {
            $builder->groupStart()
                    ->like('title', $term)
                    ->orLike('description', $term)
                    ->groupEnd();
        }

        $query = $builder->get();
        $results = $query->getResultArray();

        // Check enrollment status for logged-in students
        $enrollmentStatuses = [];
        $user_id = session()->get('user_id');
        $user_role = session()->get('role');
        if ($user_id && $user_role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            foreach ($results as $course) {
                $enrollmentStatuses[$course['id']] = $enrollmentModel->isAlreadyEnrolled($user_id, $course['id']);
            }
        }

        // If AJAX, return JSON with enrollment statuses
        if ($request->isAJAX()) {
            return $this->response->setJSON([
                'courses' => $results,
                'enrollmentStatuses' => $enrollmentStatuses,
                'user_role' => $user_role
            ]);
        }

        // Otherwise load index view (adjust if needed)
        return view('courses/index', [
            'courses' => $results,
            'enrollmentStatuses' => $enrollmentStatuses,
            'user_role' => $user_role
        ]);
    }

    /**
     * Display course management page for admin and teachers.
     * Shows all courses where you can upload materials or update schedules.
     */
    public function manage()
    {
        $session = session();

        // Check if user is logged in and has permission (admin or teacher)
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. Only admins and teachers can manage course materials.');
            return redirect()->to('/dashboard');
        }

        // Get courses assigned to the teacher or all for admin
        $db = \Config\Database::connect();
        if ($session->get('role') === 'teacher') {
            $courses = $db->table('courses')->where('teacher_id', $session->get('user_id'))->get()->getResultArray();
        } else {
            $courses = $db->table('courses')->get()->getResultArray();
        }

        $data = [
            'courses' => $courses,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        return view('courses/manage', $data);
    }

    /**
     * Display materials for a specific course.
     * Shows all uploaded materials for the given course ID.
     */
    public function viewMaterials($courseId)
    {
        $session = session();

        // Check if user is logged in and has permission (admin or teacher)
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. Only admins and teachers can view course materials.');
            return redirect()->to('/dashboard');
        }

        // Get all courses for navigation + specific course
        $db = \Config\Database::connect();
        $courses = $db->table('courses')->get()->getResultArray();
        $course = $db->table('courses')->where('id', $courseId)->get()->getRow();

        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/dashboard');
        }

        // Get materials for this course
        $materialModel = new \App\Models\MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($courseId);

        // Get assignments for this course
        $assignmentModel = new \App\Models\AssignmentModel();
        $assignments = $assignmentModel->where('course_id', $courseId)
                                       ->join('users', 'users.id = assignments.user_id')
                                       ->select('assignments.*, users.name as student_name, users.email as student_email')
                                       ->orderBy('assignments.submitted_at', 'DESC')
                                       ->findAll();

        $data = [
            'courses' => $courses, // For navigation
            'current_course' => $course, // For displaying current course
            'materials' => $materials, // Materials for this course
            'assignments' => $assignments, // Assignments for this course
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        return view('materials/upload', $data);
    }

    /**
     * Update course schedule via AJAX.
     * Expects POST with course_id, school_year, semester, schedule_days, schedule_time_start, schedule_time_end.
     */
    public function updateSchedule()
    {
        $session = session();

        // Check if user is logged in and has permission
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        // Get form data
        $courseId = $this->request->getPost('course_id');
        $schoolYear = $this->request->getPost('school_year');
        $semester = $this->request->getPost('semester');
        $scheduleDays = $this->request->getPost('schedule_days');
        $scheduleTimeStart = $this->request->getPost('schedule_time_start');
        $scheduleTimeEnd = $this->request->getPost('schedule_time_end');

        if (!$courseId || !$schoolYear || !$semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required fields.'
            ])->setStatusCode(400);
        }

        // Load CourseModel
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($courseId);

        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        // Check if teacher has permission (only their courses or admin)
        $userRole = $session->get('role');
        $userId = $session->get('user_id');
        if ($userRole === 'teacher' && $course['teacher_id'] != $userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You can only edit your own courses.'
            ])->setStatusCode(403);
        }

        // Prepare data for update
        $updateData = [
            'school_year' => $schoolYear,
            'semester' => $semester,
            'schedule_days' => $scheduleDays,
            'schedule_time_start' => $scheduleTimeStart,
            'schedule_time_end' => $scheduleTimeEnd,
        ];

        // For schedule validation, we need to include teacher_id in the validation data
        // but we don't want to update the teacher_id field
        $validationData = $updateData;
        $validationData['teacher_id'] = $course['teacher_id'];

        // Update the course using the custom method that handles validation separately
        if ($courseModel->validateAndUpdate($courseId, $updateData, $validationData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Course schedule updated successfully.'
            ]);
        } else {
            // Ensure errors are properly formatted for JSON
            $errors = $courseModel->errors();
            $errorMessage = is_array($errors) ? implode(', ', $errors) : 'Validation failed';

            return $this->response->setJSON([
                'success' => false,
                'message' => $errorMessage
            ])->setStatusCode(400);
        }
    }
}
