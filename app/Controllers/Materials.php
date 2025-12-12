<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;

class Materials extends Controller
{
    public function __construct()
    {
        helper(['url', 'form']);
    }

    /**
     * Display the file upload form and handle the file upload process.
     * Only admin and assigned teachers can upload to active courses.
     *
     * @param int $course_id
     */
    public function upload($course_id)
    {
        $session = session();
        $userRole = $session->get('role');
        $userId = $session->get('user_id');

        // Check if user is logged in and has permission (admin or teacher)
        if (!$session->get('isLoggedIn') || !in_array($userRole, ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. Only admins and teachers can upload materials.');
            return redirect()->to('/dashboard');
        }

        // Check if course exists and is active
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $course_id)->get()->getRow();
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/dashboard');
        }

        // Check if course is active
        if ($course->status !== 'Active') {
            $session->setFlashdata('error', 'Cannot upload materials to inactive courses.');
            return redirect()->to('/dashboard');
        }

        // For teachers, check if they are assigned to this course
        if ($userRole === 'teacher' && $course->teacher_id != $userId) {
            $session->setFlashdata('error', 'Access denied. You can only upload materials to your assigned courses.');
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            $title = trim($this->request->getPost('material_title'));
            // Get uploaded file
            $file = $this->request->getFile('material_file');

            // Check if file was uploaded
            if (!$file->isValid()) {
                $errorMessage = $file->getErrorString();
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMessage])->setStatusCode(400);
                }
                $session->setFlashdata('error', $errorMessage);
                return redirect()->to('/course/' . $course_id . '/upload');
            }

            // Validate file type
            $allowedTypes = ['pdf', 'ppt', 'pptx'];
            if (!in_array($file->getExtension(), $allowedTypes)) {
                $errorMessage = 'Only PDF and PPT files are allowed.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMessage])->setStatusCode(400);
                }
                $session->setFlashdata('error', $errorMessage);
                return redirect()->to('/course/' . $course_id . '/upload');
            }

            // Check file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                $errorMessage = 'File size too large. Maximum size is 10MB.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMessage])->setStatusCode(400);
                }
                $session->setFlashdata('error', $errorMessage);
                return redirect()->to('/course/' . $course_id . '/upload');
            }

            // Generate unique filename to avoid conflicts
            $newName = $file->getRandomName();

            // Move file to uploads directory
            // Ensure upload directory exists
            $uploadDir = WRITEPATH . 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            if ($file->move($uploadDir, $newName)) {
                // Prepare data for database
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => $uploadDir . $newName,
                ];

                // Add title only if column exists (avoids DB errors when migration not run yet)
                $dbFields = \Config\Database::connect()->getFieldNames('materials');
                if (in_array('title', $dbFields, true)) {
                    $data['title'] = $title ?: $file->getClientName();
                }

                // Save to database
                $materialModel = new MaterialModel();
                if ($materialModel->insertMaterial($data)) {
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => true, 'message' => 'Material uploaded successfully.']);
                    }
                    $session->setFlashdata('success', 'Material uploaded successfully.');
                    return redirect()->to('/course/' . $course_id . '/upload');
                } else {
                    // Delete uploaded file if database save failed
                    if (file_exists($data['file_path'])) {
                        unlink($data['file_path']);
                    }
                    $errorMessage = 'Failed to save material to database.';
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $errorMessage])->setStatusCode(500);
                    }
                    $session->setFlashdata('error', $errorMessage);
                    return redirect()->to('/course/' . $course_id . '/upload');
                }
            } else {
                $errorMessage = 'Failed to upload file.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => $errorMessage])->setStatusCode(500);
                }
                $session->setFlashdata('error', $errorMessage);
                return redirect()->to('/course/' . $course_id . '/upload');
            }
        }

        // Display upload form
        $data = [
            'course' => $course,
            'user_name' => $session->get('user_name'),
            'role' => $userRole,
        ];

        return view('materials/upload', $data);
    }

    /**
     * Handle the deletion of a material record and the associated file.
     * Only admin and teacher can delete.
     *
     * @param int $material_id
     */
    public function delete($material_id)
    {
        $session = session();
        $userRole = $session->get('role');

        // Check if user is logged in and has permission (admin or teacher)
        if (!$session->get('isLoggedIn') || !in_array($userRole, ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. Only admins and teachers can delete materials.');
            return redirect()->to('/dashboard');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->getMaterialById($material_id);

        if (!$material) {
            $session->setFlashdata('error', 'Material not found.');
            return redirect()->to('/dashboard');
        }

        // Delete the file from filesystem
        if (file_exists($material['file_path'])) {
            unlink($material['file_path']);
        }

        // Delete from database
        if ($materialModel->deleteMaterial($material_id)) {
            $session->setFlashdata('success', 'Material deleted successfully.');
        } else {
            $session->setFlashdata('error', 'Failed to delete material.');
        }

        return redirect()->to('/dashboard');
    }

    /**
     * Display materials for enrolled courses (students only).
     */
    public function index()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || $userRole !== 'student') {
            $session->setFlashdata('error', 'Access denied. Only students can view this page.');
            return redirect()->to('/dashboard');
        }

        $materialModel = new MaterialModel();
        $assignmentModel = new \App\Models\AssignmentModel();
        $materials = $materialModel->getMaterialsByUserId($userId);

        // Group materials by course
        $groupedMaterials = [];
        foreach ($materials as $material) {
            $courseId = $material['course_id'];
            if (!isset($groupedMaterials[$courseId])) {
                $groupedMaterials[$courseId] = [
                    'course_name' => $material['course_name'] ?? 'Unknown Course',
                    'materials' => [],
                    'has_submitted' => $assignmentModel->hasSubmittedForCourse($userId, $courseId)
                ];
            }
            $groupedMaterials[$courseId]['materials'][] = $material;
        }

        // Only show courses that have at least one uploaded material
        // (removes empty courses so students only see/downlaod when files exist)
        if (empty($groupedMaterials)) {
            // If no materials at all, leave empty so the view shows the empty state
            $groupedMaterials = [];
        }

        $data = [
            'groupedMaterials' => $groupedMaterials,
            'user_name' => $session->get('user_name'),
            'role' => $userRole,
        ];

        return view('materials/index', $data);
    }

    /**
     * Display materials for a specific course (students only).
     * Checks if student is enrolled in the course.
     *
     * @param int $courseId
     */
    public function courseMaterials($courseId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || $userRole !== 'student') {
            $session->setFlashdata('error', 'Access denied. Only students can view this page.');
            return redirect()->to('/dashboard');
        }

        // Check if student is enrolled in the course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            $session->setFlashdata('error', 'Access denied. You are not enrolled in this course.');
            return redirect()->to('/student/materials');
        }

        // Get course info
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $courseId)->get()->getRow();
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/student/materials');
        }

        // Get materials for this course
        $materialModel = new MaterialModel();
        $materials = $materialModel->getMaterialsByCourse($courseId);

        // Check if student has submitted assignment
        $assignmentModel = new \App\Models\AssignmentModel();
        $hasSubmitted = $assignmentModel->hasSubmittedForCourse($userId, $courseId);

        $data = [
            'course' => $course,
            'materials' => $materials,
            'has_submitted' => $hasSubmitted,
            'user_name' => $session->get('user_name'),
            'role' => $userRole,
        ];

        return view('materials/course', $data);
    }

    /**
     * Submit assignment for a course.
     */
    public function submit($courseId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        // Check if user is logged in and is a student
        if (!$session->get('isLoggedIn') || $userRole !== 'student') {
            $session->setFlashdata('error', 'Access denied. Only students can submit assignments.');
            return redirect()->to('/student/materials');
        }

        // Check if student is enrolled in the course
        $enrollmentModel = new \App\Models\EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($userId, $courseId)) {
            $session->setFlashdata('error', 'Access denied. You are not enrolled in this course.');
            return redirect()->to('/student/materials');
        }

        if ($this->request->getMethod() === 'POST') {
            // Get uploaded file
            $file = $this->request->getFile('assignment_file');

            // Check if file was uploaded
            if (!$file->isValid()) {
                $session->setFlashdata('error', $file->getErrorString());
                return redirect()->to('/student/materials');
            }

            // Validate file type
            $allowedTypes = ['pdf', 'ppt', 'pptx', 'doc', 'docx'];
            if (!in_array($file->getExtension(), $allowedTypes)) {
                $session->setFlashdata('error', 'Invalid file type. Only PDF, PPT, PPTX, DOC, DOCX files are allowed.');
                return redirect()->to('/student/materials');
            }

            // Check file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                $session->setFlashdata('error', 'File size too large. Maximum size is 10MB.');
                return redirect()->to('/student/materials');
            }

            // Generate unique filename
            $newName = $file->getRandomName();

            // Move file to assignments directory (create if not exists)
            $assignmentDir = WRITEPATH . 'uploads/assignments/';
            if (!is_dir($assignmentDir)) {
                mkdir($assignmentDir, 0755, true);
            }

            if ($file->move($assignmentDir, $newName)) {
                // Get course info
                $db = \Config\Database::connect();
                $course = $db->table('courses')->where('id', $courseId)->get()->getRow();

                // Save to database
                $assignmentModel = new \App\Models\AssignmentModel();
                $data = [
                    'user_id' => $userId,
                    'course_id' => $courseId,
                    'file_name' => $file->getClientName(),
                    'file_path' => $newName,
                ];

// Get teacher or admin to notify (for now, notify admin if one exists)
$teacherModel = new \App\Models\UserModel();
$teachers = $teacherModel->where('role', 'teacher')->findAll();
// For simplicity, notify the first teacher or admin
$notifyUser = !empty($teachers) ? $teachers[0] : $teacherModel->where('role', 'admin')->first();
if ($notifyUser && $course) {
    $notificationModel = new \App\Models\NotificationModel();
    $notificationModel->insert([
        'user_id' => $notifyUser['id'],
        'message' => 'A student has submitted an assignment for ' . $course->title,
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

if ($assignmentModel->submitAssignment($data)) {
    $session->setFlashdata('success', 'Assignment submitted successfully.');
                } else {
                    $session->setFlashdata('error', 'Failed to save assignment.' . $assignmentDir . $newName);
                    // Delete uploaded file if database save failed
                    if (file_exists($assignmentDir . $newName)) {
                        unlink($assignmentDir . $newName);
                    }
                }
            } else {
                $session->setFlashdata('error', 'Failed to upload file.');
            }
        }

        return redirect()->to('/student/materials');
    }

    /**
     * Handle the file download for enrolled students.
     *
     * @param int $material_id
     */
    public function download($material_id)
    {
        $session = session();
        $userId = $session->get('user_id');

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('error', 'Please log in to download materials.');
            return redirect()->to('/login');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->getMaterialById($material_id);

        if (!$material) {
            $session->setFlashdata('error', 'Material not found.');
            return redirect()->to('/dashboard');
        }

        // Check if user is enrolled in the course
        $enrollmentModel = new EnrollmentModel();
        if (!$enrollmentModel->isAlreadyEnrolled($userId, $material['course_id'])) {
            $session->setFlashdata('error', 'Access denied. You are not enrolled in this course.');
            return redirect()->to('/dashboard');
        }

        // Check if file exists
        if (!file_exists($material['file_path'])) {
            $session->setFlashdata('error', 'File not found on server.');
            return redirect()->to('/dashboard');
        }

        // Force download
        return $this->response->download($material['file_path'], null, true);
    }

    /**
     * Handle the file download for assignments (teachers only).
     *
     * @param int $assignment_id
     */
    public function downloadAssignment($assignment_id)
    {
        $session = session();

        // Check if user is logged in and is teacher
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            $session->setFlashdata('error', 'Access denied. Only teachers can download assignments.');
            return redirect()->to('/dashboard');
        }

        $assignmentModel = new \App\Models\AssignmentModel();
        $assignment = $assignmentModel->find($assignment_id);

        if (!$assignment) {
            $session->setFlashdata('error', 'Assignment not found.');
            return redirect()->to('/dashboard');
        }

        // Check if file exists
        $filePath = WRITEPATH . 'uploads/assignments/' . $assignment['file_path'];
        if (!file_exists($filePath)) {
            $session->setFlashdata('error', 'File not found on server.');
            return redirect()->to('/dashboard');
        }

        // Force download
        return $this->response->download($filePath, null, true);
    }

    /**
     * Grade a submitted assignment (teachers only).
     */
    public function gradeAssignment($assignment_id)
    {
        $session = session();

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $grade = trim($this->request->getPost('grade') ?? '');
        $feedback = trim($this->request->getPost('feedback') ?? '');

        if ($grade === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Grade is required'])->setStatusCode(422);
        }

        $assignmentModel = new \App\Models\AssignmentModel();
        $assignment = $assignmentModel->find($assignment_id);

        if (!$assignment) {
            return $this->response->setJSON(['success' => false, 'message' => 'Assignment not found'])->setStatusCode(404);
        }

        // Ensure grading columns exist to avoid silent failures
        $fields = \Config\Database::connect()->getFieldNames('assignments');
        if (!in_array('grade', $fields, true) || !in_array('feedback', $fields, true) || !in_array('graded_at', $fields, true)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Grading fields missing. Run migrations.'])->setStatusCode(500);
        }

        $updated = $assignmentModel->update($assignment_id, [
            'grade' => $grade,
            'feedback' => $feedback,
            'graded_at' => date('Y-m-d H:i:s'),
        ]);

        if (!$updated) {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to save grade'])->setStatusCode(500);
        }

        // Notify student
        $notificationModel = new \App\Models\NotificationModel();
        $notificationModel->insert([
            'user_id' => $assignment['user_id'],
            'message' => 'Your assignment has been graded. Grade: ' . $grade,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Teacher materials management page - shows all courses with upload and grading functionality.
     */
    public function manage()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        if (!$session->get('isLoggedIn') || $userRole !== 'teacher') {
            $session->setFlashdata('error', 'Access denied. Only teachers can access this page.');
            return redirect()->to('/dashboard');
        }

        // Get all courses assigned to this teacher
        $db = \Config\Database::connect();
        $courses = $db->table('courses')
                     ->where('teacher_id', $userId)
                     ->get()
                     ->getResultArray();

        // Get assignments for all courses assigned to this teacher
        $assignmentModel = new \App\Models\AssignmentModel();
        $courseIds = array_column($courses, 'id');

        if (!empty($courseIds)) {
            $allAssignments = $assignmentModel->whereIn('course_id', $courseIds)
                                             ->join('users', 'users.id = assignments.user_id')
                                             ->select('assignments.*, users.name as student_name, users.email as student_email')
                                             ->orderBy('assignments.submitted_at', 'DESC')
                                             ->findAll();

            // Add course title to each assignment
            $courseTitles = [];
            foreach ($courses as $course) {
                $courseTitles[$course['id']] = $course['title'];
            }

            foreach ($allAssignments as &$assignment) {
                $assignment['course_title'] = $courseTitles[$assignment['course_id']] ?? 'Unknown Course';
            }
        } else {
            $allAssignments = [];
        }

        $data = [
            'courses' => $courses,
            'assignments' => $allAssignments,
            'user_name' => $session->get('user_name'),
            'role' => $userRole,
        ];

        return view('materials/manage', $data);
    }

    /**
     * Teacher course selection page - shows all courses teacher can manage.
     */
    public function courseSelection()
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        if (!$session->get('isLoggedIn') || $userRole !== 'teacher') {
            $session->setFlashdata('error', 'Access denied. Only teachers can access this page.');
            return redirect()->to('/dashboard');
        }

        // Get all courses for the teacher to manage
        $db = \Config\Database::connect();
        $courses = $db->table('courses')
                     ->where('teacher_id', $userId)
                     ->get()
                     ->getResultArray();

        $data = [
            'courses' => $courses,
            'user_name' => $session->get('user_name'),
            'role' => $userRole,
        ];

        return view('materials/course_selection', $data);
    }

    /**
     * Teacher view: grade assignments for a specific course.
     */
    public function gradeAssignmentsView($courseId)
    {
        $session = session();
        $userId = $session->get('user_id');
        $userRole = $session->get('role');

        if (!$session->get('isLoggedIn') || $userRole !== 'teacher') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Access denied'])->setStatusCode(403);
            }
            $session->setFlashdata('error', 'Access denied. Only teachers can grade assignments.');
            return redirect()->to('/dashboard');
        }

        // Check if the teacher is assigned to this course
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $courseId)->where('teacher_id', $userId)->get()->getRow();

        if (!$course) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['error' => 'Access denied'])->setStatusCode(403);
            }
            $session->setFlashdata('error', 'Access denied. You can only grade assignments for your assigned courses.');
            return redirect()->to('/manage_course');
        }

        // Get assignments for this course
        $assignmentModel = new \App\Models\AssignmentModel();
        $assignments = $assignmentModel->where('course_id', $courseId)
                                       ->join('users', 'users.id = assignments.user_id')
                                       ->select('assignments.*, users.name as student_name, users.email as student_email')
                                       ->orderBy('assignments.submitted_at', 'DESC')
                                       ->findAll();

        $data = [
            'course' => $course,
            'assignments' => $assignments,
            'user_name' => $session->get('user_name'),
            'role' => $userRole,
        ];

        // If AJAX request, return just the assignments table content
        if ($this->request->isAJAX()) {
            return view('materials/grade_assignments_modal', $data);
        }

        // Otherwise return the full page
        return view('materials/grade_assignments', $data);
    }

    /**
     * Student view: see grades for submitted assignments.
     */
    public function grades()
    {
        $session = session();
        $userId = $session->get('user_id');

        if (!$session->get('isLoggedIn') || $session->get('role') !== 'student') {
            $session->setFlashdata('error', 'Access denied. Only students can view grades.');
            return redirect()->to('/dashboard');
        }

        $assignmentModel = new \App\Models\AssignmentModel();
        $assignments = $assignmentModel->getAssignmentsByStudent($userId);

        $data = [
            'assignments' => $assignments,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role'),
        ];

        return view('materials/grades', $data);
    }


}
