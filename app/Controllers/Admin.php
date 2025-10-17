<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Admin extends Controller
{
    public function dashboard()
    {
        // Load the view with the message
        return view('admin_dashboard', ['message' => 'Welcome, Admin!']);
    }
}