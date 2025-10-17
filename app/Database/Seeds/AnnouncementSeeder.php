<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\AnnouncementModel;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        $model = new AnnouncementModel();
        $data = [
            [
                'title' => 'First Announcement',
                'content' => 'This is the first announcement.',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Second Announcement',
                'content' => 'This is the second announcement.',
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];
        
        foreach ($data as $item) {
            $model->insert($item);
        }
    }
}