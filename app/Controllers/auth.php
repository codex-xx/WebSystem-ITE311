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
                        'password' => $this->request->getPost('password'),
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
                $password = trim($this->request->getPost('password'));

                try {
                    $model = new UserModel();
                    $user  = $model->where('email', $email)->first();

                    if ($user && password_verify($password, $user['password'])) {
                        // Check if user account is active
                        if ($user['status'] !== 'active') {
                            $session->setFlashdata('login_error', 'This user account has been deleted or deactivated.');
                            return redirect()->to(base_url('login'));
                        }
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

    public function profile()
    {
        helper(['form']);
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }

        $userId = $session->get('user_id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($this->request->getMethod() === 'POST') {
            // Only validate password change fields since profile info is read-only
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[6]',
                'confirm_password' => 'matches[new_password]'
            ];

            if ($this->validate($rules)) {
                // Get the form data
                $currentPasswordInput = trim($this->request->getPost('current_password'));
                $newPassword = trim($this->request->getPost('new_password'));

                // Verify current password - be very explicit about this
                $currentPasswordVerification = password_verify($currentPasswordInput, $user['password']);

                if (!$currentPasswordVerification) {
                    log_message('debug', 'Password change failed: current password verification failed for user ' . $userId);
                    $session->setFlashdata('error', 'Current password is incorrect.');
                    return redirect()->to('/profile');
                }

                try {
                    $updateData = [
                        'password' => $newPassword,
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $updateResult = $userModel->update($userId, $updateData);

                    if ($updateResult) {
                        $session->setFlashdata('success', 'Password changed successfully! You should now be able to log in with your new password.');
                        return redirect()->to('/profile');
                    } else {
                        $session->setFlashdata('error', 'Database update failed. Please try again.');
                        return redirect()->to('/profile');
                    }
                } catch (\Exception $e) {
                    $session->setFlashdata('error', 'Error updating password.');
                    return redirect()->to('/profile');
                }
            } else {
                log_message('debug', 'Password change validation FAILED: ' . implode(', ', $this->validator->getErrors()));
                $session->setFlashdata('error', 'Validation failed: ' . implode(', ', $this->validator->getErrors()));
                return redirect()->to('/profile');
            }
        }

        $data = [
            'user' => $user,
            'user_name' => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role' => $session->get('role'),
            'validation' => $this->validator
        ];

        return view('auth/profile', $data);
    }

    public function changePassword()
    {
        helper(['form']);
        $session = session();

        // Check if user is logged in
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'You must be logged in to change your password.'])->setStatusCode(401);
        }

        $userId = $session->get('user_id');
        $userModel = new UserModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'current_password' => 'required',
                'new_password' => 'required|min_length[6]',
                'confirm_password' => 'matches[new_password]'
            ];

            if ($this->validate($rules)) {
                // Get current user data
                $user = $userModel->find($userId);

                if (!$user) {
                    return $this->response->setJSON(['success' => false, 'message' => 'User not found.'])->setStatusCode(404);
                }

                // Verify current password
                if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Current password is incorrect.'])->setStatusCode(400);
                }

                // Update password
                $passwordData = [
                    'password' => $this->request->getPost('new_password')
                ];

                if ($userModel->update($userId, $passwordData)) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Password changed successfully!']);
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to change password. Please try again.'])->setStatusCode(500);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => implode(', ', $this->validator->getErrors())])->setStatusCode(400);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method.'])->setStatusCode(405);
        }
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

        // Protect Admin role: Admins cannot have their role changed
        if ($user['role'] === 'admin' && $role !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot change role of admin users'])->setStatusCode(400);
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
     * Handle user deactivation via AJAX.
     */
    public function deactivateUser($userId)
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

        // Prevent admin from deactivating themselves
        if ($userId == $session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot deactivate your own account'])->setStatusCode(400);
        }

        // Prevent deactivation of admin users
        if ($user['role'] === 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Cannot deactivate admin users'])->setStatusCode(400);
        }

        // Check if user is already deactivated
        if (($user['status'] ?? 'active') === 'inactive') {
            return $this->response->setJSON(['success' => false, 'message' => 'User is already deactivated'])->setStatusCode(400);
        }

        if ($userModel->update($userId, ['status' => 'inactive'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User deactivated successfully'
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to deactivate user'])->setStatusCode(500);
        }
    }

    /**
     * Handle user activation via AJAX.
     */
    public function activateUser($userId)
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

        // Check if user is already active
        if (($user['status'] ?? 'active') === 'active') {
            return $this->response->setJSON(['success' => false, 'message' => 'User is already active'])->setStatusCode(400);
        }

        if ($userModel->update($userId, ['status' => 'active'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'User activated successfully'
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to activate user'])->setStatusCode(500);
        }
    }

    /**
     * Handle user creation via AJAX (Admin only).
     */
    public function addUser()
    {
        $session = session();

        // Check if user is logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])->setStatusCode(403);
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'     => 'required|min_length[3]|max_length[100]',
                'email'    => 'required|valid_email',
                'password' => 'required',
                'role'     => 'required|in_list[admin,teacher,student]'
            ];

            if ($this->validate($rules)) {
                try {
                    $userModel = new UserModel();

                    // Check if email already exists
                    $existingUser = $userModel->where('email', $this->request->getPost('email'))->first();
                    if ($existingUser) {
                        return $this->response->setJSON(['success' => false, 'message' => 'Email is already in use'])->setStatusCode(400);
                    }

                    $data = [
                        'name'     => trim($this->request->getPost('name')),
                        'email'    => $this->request->getPost('email'),
                        'password' => $this->request->getPost('password'),
                        'role'     => $this->request->getPost('role'),
                        'status'   => 'active' // New users are active by default
                    ];

                    if ($userModel->insert($data)) {
                        // Set success message for display after page reload
                        $session->setFlashdata('success', 'User "' . $data['name'] . '" has been created successfully with role "' . $data['role'] . '".');

                        return $this->response->setJSON([
                            'success' => true,
                            'message' => 'User created successfully',
                            'user' => [
                                'id' => $userModel->insertID(),
                                'name' => $data['name'],
                                'email' => $data['email'],
                                'role' => $data['role'],
                                'status' => $data['status'],
                                'created_at' => date('Y-m-d H:i:s')
                            ]
                        ]);
                    } else {
                        return $this->response->setJSON(['success' => false, 'message' => 'Failed to create user'])->setStatusCode(500);
                    }
                } catch (\Exception $e) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()])->setStatusCode(500);
                }
            } else {
                return $this->response->setJSON(['success' => false, 'message' => implode(', ', $this->validator->getErrors())])->setStatusCode(400);
            }
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method'])->setStatusCode(405);
        }
    }

    /**
     * Display students list page for teachers.
     */
    public function teacherStudents()
    {
        $session = session();

        // Check if user is logged in and is teacher
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'teacher') {
            $session->setFlashdata('error', 'Access denied. Only teachers can view students.');
            return redirect()->to('/dashboard');
        }

        $userModel = new UserModel();
        $students = $userModel->where('role', 'student')->findAll();

        $data = [
            'students' => $students,
            'user_name' => $session->get('user_name'),
            'role' => $session->get('role')
        ];

        return view('auth/students', $data);
    }

    // Test endpoint to check password hash for debugging
    public function testPassword() {
        $session = session();

        // Only allow if logged in and is admin
        if (!$session->get('isLoggedIn') || $session->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $users = $userModel->findAll();

        $data = [];
        foreach($users as $user) {
            $data[] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'password_hash' => $user['password'],
                'hash_starts_with' => substr($user['password'], 0, 10) . '...',
                'created_at' => $user['created_at'],
                'updated_at' => $user['updated_at']
            ];
        }

        return view('auth/test_password', ['users' => $data]);
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
            $roleData['total_students'] = $model->where('role', 'student')->where('status', 'active')->countAllResults();
            $roleData['total_teachers'] = $model->where('role', 'teacher')->where('status', 'active')->countAllResults();
            $roleData['total_admins'] = $model->where('role', 'admin')->where('status', 'active')->countAllResults();
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
