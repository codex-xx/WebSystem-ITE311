<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Announcement extends Controller
{
    public function index()
    {
        $model = new \App\Models\AnnouncementModel();
        $announcements = $model->orderBy('created_at', 'DESC')->findAll();
        return view('announcements', ['announcements' => $announcements]);
    }
}