<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Enrollment extends Controller
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    /**
     * Display enrollment status for the logged-in student.
     */
    public function studentIndex()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'student') {
            return redirect()->to('/auth/login')->with('error', 'Access denied.');
        }

        $user_id = $session->get('user_id');
        $enrollmentModel = new EnrollmentModel();

        $enrollments = $enrollmentModel->getUserEnrollments($user_id);

        return view('enrollments/student_index', [
            'enrollments' => $enrollments,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ]);
    }

    /**
     * Display pending enrollment requests for the logged-in teacher.
     */
    public function teacherIndex()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['teacher', 'admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Access denied.');
        }

        $teacher_id = $session->get('user_id');
        $enrollmentModel = new EnrollmentModel();

        $pendingRequests = $enrollmentModel->getPendingRequests($teacher_id);
        $allEnrollments = $enrollmentModel->getEnrollmentsForTeacher($teacher_id);

        return view('enrollments/teacher_index', [
            'pendingRequests' => $pendingRequests,
            'allEnrollments' => $allEnrollments,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ]);
    }

    /**
     * Approve an enrollment request.
     * Expects POST with 'enrollment_id'.
     */
    public function approve()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        $enrollment_id = $this->request->getPost('enrollment_id');
        $processed_by = $session->get('user_id');

        $enrollmentModel = new EnrollmentModel();
        $result = $enrollmentModel->approveEnrollment($enrollment_id, $processed_by);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment approved successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to approve enrollment.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Deny an enrollment request.
     * Expects POST with 'enrollment_id'.
     */
    public function deny()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        $enrollment_id = $this->request->getPost('enrollment_id');
        $processed_by = $session->get('user_id');

        $enrollmentModel = new EnrollmentModel();
        $result = $enrollmentModel->denyEnrollment($enrollment_id, $processed_by);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Enrollment denied successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to deny enrollment.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Force-enroll a student (admin/teacher action).
     * Expects POST with 'user_id' and 'course_id'.
     */
    public function forceEnroll()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['teacher', 'admin'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied.'
            ])->setStatusCode(403);
        }

        $user_id = $this->request->getPost('user_id');
        $course_id = $this->request->getPost('course_id');
        $processed_by = $session->get('user_id');

        if (!$user_id || !$course_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request parameters.'
            ])->setStatusCode(400);
        }

        $enrollmentModel = new EnrollmentModel();
        $result = $enrollmentModel->forceEnroll($user_id, $course_id, $processed_by);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Student force-enrolled successfully.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to force-enroll student. Check for conflicts.'
            ])->setStatusCode(500);
        }
    }

    /**
     * Display form to force-enroll a student.
     */
    public function forceEnrollForm()
    {
        $session = session();

        if (!$session->get('isLoggedIn') || !in_array($session->get('role'), ['teacher', 'admin'])) {
            return redirect()->to('/auth/login')->with('error', 'Access denied.');
        }

        // Get all users (for dropdown) - you might want to filter by students only
        $db = \Config\Database::connect();
        $users = $db->table('users')->select('id, name, email')->get()->getResultArray();
        $courses = $db->table('courses')->select('id, title, course_code')->get()->getResultArray();

        return view('enrollments/force_enroll_form', [
            'users' => $users,
            'courses' => $courses,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ]);
    }
}
