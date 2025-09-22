<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        $session = session();

        // Authorization check
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/auth/login')->with('error', 'Access denied');
        }

        // Example data for admin
        $data = [
            'title' => 'Admin Dashboard',
            'users' => ['Teacher 1', 'Teacher 2', 'Student A', 'Student B']
        ];

        return view('admin/dashboard', $data);
    }
}
