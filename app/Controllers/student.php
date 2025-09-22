<?php

namespace App\Controllers;

class Student extends BaseController
{
    public function dashboard()
    {
        $session = session();

        // Authorization check
        if ($session->get('role') !== 'student') {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        // Example data for student
        $data = [
            'title' => 'Student Dashboard',
            'enrolledCourses' => ['Math 101', 'Science 201']
        ];

        return view('student/dashboard', $data);
    }
}
