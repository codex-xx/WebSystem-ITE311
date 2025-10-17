<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Teacher extends Controller
{
    public function dashboard()
    {
        // Load the view with the message
        return view('teacher_dashboard', ['message' => 'Welcome, Teacher!']);
    }
}