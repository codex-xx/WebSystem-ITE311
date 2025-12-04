<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $session = session();
        $model   = new UserModel();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'             => 'required|min_length[3]|max_length[100]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'matches[password]',
                'role'             => 'required|in_list[admin,teacher,student]'
            ];
            
            if ($this->validate($rules)) {
                try {
                    $data = [
                        'name'     => trim($this->request->getPost('name')),
                        'email'    => $this->request->getPost('email'),
                        // ✅ Hash password before saving
                        'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
                        'role'     => $this->request->getPost('role')
                    ];
                    
                    if ($model->insert($data)) {
                        $session->setFlashdata('register_success', 'Registration successful. Please login.');
                        return redirect()->to(base_url('login'));
                    } else {
                        $session->setFlashdata('register_error', 'Registration failed. Please try again.');
                    }
                } catch (\Exception $e) {
                    $session->setFlashdata('register_error', 'Registration failed: ' . $e->getMessage());
                }
            } else {
                $session->setFlashdata('register_error', implode(', ', $this->validator->getErrors()));
            }
        }
        
        return view('auth/register', [
            'validation' => $this->validator
        ]);
    }

    public function login()
    {
        helper(['form']);
        $session = session();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required'
            ];
            
            if ($this->validate($rules)) {
                $email    = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                
                try {
                    $model = new UserModel();
                    $user  = $model->where('email', $email)->first();
                    
                    if ($user && password_verify($password, $user['password'])) {
                        $sessionData = [
                            'user_id'    => $user['id'],
                            'user_name'  => $user['name'] ?? $user['email'],
                            'user_email' => $user['email'],
                            'role'       => $user['role'],
                            'isLoggedIn' => true
                        ];
                        
                        $session->set($sessionData);

                        // Create a test notification for demo purposes
                        $notificationModel = new \App\Models\NotificationModel();
                        $testMessage = 'Welcome to the ITE311 Dashboard! You have successfully logged in as ' . ucfirst($user['role']) . '.';
                        $notificationModel->insert([
                            'user_id' => $user['id'],
                            'message' => $testMessage,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);

                        $session->setFlashdata('success', 'Welcome, ' . $sessionData['user_name'] . '! You have a new notification.');
                        
                        // ✅ Redirect based on role
                      if ($user['role'] === 'admin') {
                        return redirect()->to('/dashboard');
                    } elseif ($user['role'] === 'teacher') {
                        return redirect()->to('/dashboard');
                    } elseif ($user['role'] === 'student') {
                        return redirect()->to('/dashboard');
                    }
                    } else {
                        $session->setFlashdata('login_error', 'Invalid email or password.');
                    }
                } catch (\Exception $e) {
                    $session->setFlashdata('login_error', 'Login failed: ' . $e->getMessage());
                }
            } else {
                $session->setFlashdata('login_error', implode(', ', $this->validator->getErrors()));
            }
        }
        
        return view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('login');
    }

    /**
     * Display user management page for admin.
     * Shows user management within the courses/manage view since no new files allowed.
     */
    public function users()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            $session->setFlashdata('error', 'Access denied. Only admins can manage users.');
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        $data = [
            'users' => $users,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        // Use auth/manage view for user management
        return view('auth/manage', $data);
    }

    /**
     * Handle AJAX request to get user data for editing.
     */
    public function getUser($userId)
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found'])->setStatusCode(404);
        }

        return $this->response->setJSON([
            'success' => true,
            'user' => $user
        ]);
    }

    /**
     * Handle user update via AJAX.
     */
    public function updateUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $userId = $this->request->getPost('user_id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $role = $this->request->getPost('role');

        // Validate input
        if (!$userId || !$name || !$email || !$role) {
            return $this->response->setJSON(['success' => false, 'message' => 'All fields are required'])->setStatusCode(400);
        }

        // Validate role
        if (!in_array($role, ['admin', 'teacher', 'student'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid role'])->setStatusCode(400);
        }

        $userModel = new UserModel();

        // Check if user exists
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found'])->setStatusCode(404);
        }

        // Check if email is already taken by another user
        $existingUser = $userModel->where('email', $email)->where('id !=', $userId)->first();
        if ($existingUser) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email is already in use'])->setStatusCode(400);
        }

        // Check if data has actually changed
        $dataChanged = false;
        $data = [];

        if (trim($name) !== trim($user['name'])) {
            $data['name'] = trim($name);
            $dataChanged = true;
        }

        if ($email !== $user['email']) {
            $data['email'] = $email;
            $dataChanged = true;
        }

        if ($role !== $user['role']) {
            $data['role'] = $role;
            $dataChanged = true;
        }

        // If no data changed, return message
        if (!$dataChanged) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nothing changed. Update cancelled.'
            ]);
        }

        if ($userModel->update($userId, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user'])->setStatusCode(500);
        }
    }

    /**
     * Handle user deletion via AJAX.
     */
    public function deleteUser($userId)
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        $userModel = new UserModel();

        // Check if user exists
        $user = $userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found'])->setStatusCode(404);
        }

        // Prevent admin from deleting themselves
        if ($userId == $session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot delete your own account'])->setStatusCode(400);
        }

        if ($userModel->delete($userId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete user'])->setStatusCode(500);
        }
    }

    public function dashboard()
    {
        $session = session();

        // ✅ Step 1: Authorization check
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login first.');
            return redirect()->to('login');
        }

        $model = new UserModel();
        $role  = $session->get('role');
        $userId = $session->get('user_id');

        $roleData = [];

        // ✅ Step 2: Fetch role-specific data
        if ($role === 'admin') {
            $roleData['total_users'] = $model->countAll();
            $roleData['recent_users'] = $model->orderBy('id', 'DESC')->findAll(5);
        } elseif ($role === 'teacher') {
            $roleData['students'] = $model->where('role', 'student')->findAll();
        } elseif ($role === 'student') {
            $roleData['profile'] = $model->find($userId);

            // Fetch enrolled and available courses
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $db = \Config\Database::connect();

            $enrolledCourses = $enrollmentModel->getUserEnrollments($userId);
            $allCourses = $db->table('courses')->get()->getResultArray();

            $enrolledIds = array_column($enrolledCourses, 'course_id');
            $availableCourses = array_filter($allCourses, function($course) use ($enrolledIds) {
                return !in_array($course['id'], $enrolledIds);
            });
        }

        // Fetch unread notification count for all logged-in users
        $notificationModel = new \App\Models\NotificationModel();
        $unreadCount = $notificationModel->getUnreadCount($userId);

        // ✅ Step 3: Pass role + data to view
        $data = [
            'user_name'  => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role'       => $role,
            'roleData'   => $roleData,
            'unreadCount' => $unreadCount
        ];

        // Add course data for students
        if ($role === 'student') {
            $data['enrolledCourses'] = $enrolledCourses ?? [];
            $data['availableCourses'] = $availableCourses ?? [];
        }

        return view('auth/dashboard', $data);
    }
}
