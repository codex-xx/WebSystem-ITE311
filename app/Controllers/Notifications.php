<?php

namespace App\Controllers;

use App\Models\NotificationModel;
use CodeIgniter\Controller;

class Notifications extends Controller
{
    public function __construct()
    {
        helper('url');
    }

    /**
     * Get current user's unread notification count and list.
     * Returns JSON response.
     */
    public function get()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(401);
        }

        $notificationModel = new NotificationModel();
        $unreadCount = $notificationModel->getUnreadCount($userId);
        $notifications = $notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark a notification as read.
     * Expects POST with 'id'.
     * Returns JSON response.
     */
    public function mark_as_read($id)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        if (!$id || !is_numeric($id)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid notification ID'])->setStatusCode(400);
        }

        $notificationModel = new NotificationModel();

        // Verify the notification belongs to the user
        $notification = $notificationModel->find($id);
        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Notification not found'])->setStatusCode(404);
        }

        if ($notificationModel->markAsRead($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Notification marked as read']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to mark as read'])->setStatusCode(500);
        }
    }
}
