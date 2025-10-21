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
     * Only admin and teacher can upload.
     *
     * @param int $course_id
     */
    public function upload($course_id)
    {
        $session = session();
        $userRole = $session->get('role');

        // Check if user is logged in and has permission (admin or teacher)
        if (!$session->get('isLoggedIn') || !in_array($userRole, ['admin', 'teacher'])) {
            $session->setFlashdata('error', 'Access denied. Only admins and teachers can upload materials.');
            return redirect()->to('/dashboard');
        }

        // Check if course exists
        $db = \Config\Database::connect();
        $course = $db->table('courses')->where('id', $course_id)->get()->getRow();
        if (!$course) {
            $session->setFlashdata('error', 'Course not found.');
            return redirect()->to('/dashboard');
        }

        if ($this->request->getMethod() === 'POST') {
            // Get uploaded file
            $file = $this->request->getFile('material_file');

            // Check if file was uploaded
            if (!$file->isValid()) {
                $session->setFlashdata('error', $file->getErrorString());
                return redirect()->to('/admin/course/' . $course_id . '/upload');
            }

            // Validate file type
            $allowedTypes = ['pdf', 'ppt', 'pptx', 'doc', 'docx'];
            if (!in_array($file->getExtension(), $allowedTypes)) {
                $session->setFlashdata('error', 'Invalid file type. Only PDF, PPT, PPTX, DOC, DOCX files are allowed.');
                return redirect()->to('/admin/course/' . $course_id . '/upload');
            }

            // Check file size (10MB max)
            if ($file->getSize() > 10 * 1024 * 1024) {
                $session->setFlashdata('error', 'File size too large. Maximum size is 10MB.');
                return redirect()->to('/admin/course/' . $course_id . '/upload');
            }

            // Generate unique filename to avoid conflicts
            $newName = $file->getRandomName();

            // Move file to uploads directory
            if ($file->move(WRITEPATH . 'uploads/', $newName)) {
                // Prepare data for database
                $data = [
                    'course_id' => $course_id,
                    'file_name' => $file->getClientName(),
                    'file_path' => WRITEPATH . 'uploads/' . $newName,
                ];

                // Save to database
                $materialModel = new MaterialModel();
                if ($materialModel->insertMaterial($data)) {
                    $session->setFlashdata('success', 'Material uploaded successfully.');
                } else {
                    $session->setFlashdata('error', 'Failed to save material to database.');
                    // Delete uploaded file if database save failed
                    if (file_exists($data['file_path'])) {
                        unlink($data['file_path']);
                    }
                }
            } else {
                $session->setFlashdata('error', 'Failed to upload file.');
            }

            return redirect()->to('/admin/course/' . $course_id . '/upload');
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
}
