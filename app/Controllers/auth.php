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
                        $session->setFlashdata('success', 'Welcome, ' . $sessionData['user_name'] . '!');
                        
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

        // ✅ Step 3: Pass role + data to view
        $data = [
            'user_name'  => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role'       => $role,
            'roleData'   => $roleData
        ];

        // Add course data for students
        if ($role === 'student') {
            $data['enrolledCourses'] = $enrolledCourses ?? [];
            $data['availableCourses'] = $availableCourses ?? [];
        }
        
        return view('auth/dashboard', $data);
    }
}
