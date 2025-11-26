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

        return view('courses/index', ['courses' => $courses]);
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
        $term = $request->getGet('term'); // or $request->getPost('term')

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

        // If AJAX, return JSON
        if ($request->isAJAX()) {
            return $this->response->setJSON($results);
        }

        // Otherwise load index view (adjust if needed)
        return view('courses/index', ['courses' => $results]);
    }

// Add other methods here if the controller already has them (e.g., index(), show())
}
