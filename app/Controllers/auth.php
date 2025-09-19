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
                    $name     = trim($this->request->getPost('name'));
                    $email    = $this->request->getPost('email');
                    $plainPwd = $this->request->getPost('password');
                    $role     = $this->request->getPost('role'); // user chooses role
                    
                    // ✅ Hash the password
                    $hashedPwd = password_hash($plainPwd, PASSWORD_DEFAULT);

                    $data = [
                        'name'     => $name,
                        'email'    => $email,
                        'password' => $hashedPwd,
                        'role'     => $role
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
                        $userName = $user['name'] ?? $user['email'];
                        
                        $sessionData = [
                            'user_id'    => $user['id'],
                            'user_name'  => $userName,
                            'user_email' => $user['email'],
                            'role'       => $user['role'],
                            'isLoggedIn' => true
                        ];
                        
                        $session->set($sessionData);
                        $session->setFlashdata('success', 'Welcome, ' . $userName . '!');
                        
                        // ✅ Redirect based on role
                        if ($user['role'] === 'admin') {
                            return redirect()->to('/admin/dashboard');
                        } elseif ($user['role'] === 'teacher') {
                            return redirect()->to('/teacher/dashboard');
                        } else {
                            return redirect()->to('/student/dashboard');
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
        
        if (!$session->get('isLoggedIn')) {
            $session->setFlashdata('login_error', 'Please login first.');
            return redirect()->to('login');
        }
        
        $data = [
            'user_name'  => $session->get('user_name'),
            'user_email' => $session->get('user_email'),
            'role'       => $session->get('role')
        ];
        
        return view('dashboard', $data);
    }
}
