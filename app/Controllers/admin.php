<?php namespace App\Controllers;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    public function dashboard()
    {
        $session = session();

        // ✅ Authorization check
        if ($session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized access');
        }

        // Example data (replace with real queries later)
        $data = [
            'title'       => 'Admin Dashboard',
            'totalUsers'  => 120,
            'totalCourses'=> 15
        ];

        return view('admin/dashboard', $data);
    }
}
