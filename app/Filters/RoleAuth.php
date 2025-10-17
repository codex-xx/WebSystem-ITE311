<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use CodeIgniter\HTTP\RedirectResponse;

class RoleAuth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null): ?RedirectResponse
    {
        $session = Services::session();

        // 🔐 Block access if not logged in
        if (!$session->has('user_role')) {
            log_message('error', 'User not logged in in RoleAuth filter');
            return redirect()->to('/login');
        }

        $userRole = $session->get('user_role');

        try {
            $uri = trim($request->getUri()->getPath(), '/');
            $firstSegment = explode('/', $uri)[0];  // Get first part of URI (e.g., 'admin')
        } catch (\Exception $e) {
            log_message('error', 'Error getting URI in RoleAuth filter: ' . $e->getMessage());
            return redirect()->to('/login');
        }

        // 🔐 Match role to route segment
        if ($firstSegment === 'admin' && $userRole !== 'admin') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        if ($firstSegment === 'teacher' && $userRole !== 'teacher') {
            return redirect()->to('/announcements')->with('error', 'Access Denied: Insufficient Permissions');
        }

        // ✅ Allow access
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-response logic needed
    }
}
