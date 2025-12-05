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
     * Handle AJAX enrollment request.
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

        // Get course_id from POST
        $course_id = $this->request->getPost('course_id');
        if (!$course_id || !is_numeric($course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid course ID.'
            ])->setStatusCode(400);
        }

        // Check if course exists
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $course_id)->get()->getRow();
        if (!$course) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Course not found.'
            ])->setStatusCode(404);
        }

        // Instantiate model
        $enrollmentModel = new EnrollmentModel();

        // Check if already enrolled
        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        // Enroll the user
        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            // enrollment_date will be set automatically in the model
        ];
        $enrollment_id = $enrollmentModel->enrollUser ($data);

        if ($enrollment_id) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Successfully enrolled in the course.',
                'enrollment_id' => $enrollment_id
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to enroll. Please try again.'
            ])->setStatusCode(500);
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
     * Shows all courses where you can upload materials.
     */
    public function manage()
    {
        $session = session();

        // Check if user is logged in and has permission (admin or teacher)
        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. Only admins and teachers can manage course materials.');
            return redirect()->to('/dashboard');
        }

        // Get all courses
        $db = \Config\Database::connect();
        $courses = $db->table('courses')->get()->getResultArray();

        $data = [
            'courses' => $courses,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        return view('materials/upload', $data);
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
}
