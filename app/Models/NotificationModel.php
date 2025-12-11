<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];

    /**
     * Get the count of unread notifications for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Get the latest unread notifications for a user (limit 5).
     *
     * @param int $userId
     * @return array
     */
    public function getNotificationsForUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->findAll();
    }

    /**
     * Mark a specific notification as read.
     *
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }
}
