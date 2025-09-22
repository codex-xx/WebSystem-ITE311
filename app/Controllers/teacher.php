<?php

namespace App\Controllers;

class Teacher extends BaseController
{
    public function dashboard()
    {
        $session = session();

        // Authorization check
        if ($session->get('role') !== 'teacher') {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        // Example data for teacher
        $data = [
            'title' => 'Teacher Dashboard',
            'courses' => ['Math 101', 'Science 201', 'History 301']
        ];

        return view('teacher/dashboard', $data);
    }
}
