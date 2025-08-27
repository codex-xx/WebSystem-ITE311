<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Auth extends BaseController
{
    public function register()
{
    helper(['form']);
    $data = [];

    if ($this->request->getMethod() === 'post') {
        $rules = [
            'username'         => 'required|min_length[3]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            $data['validation'] = $this->validator;
            return view('auth/register', $data);
        }

        $userModel = new \App\Models\UserModel();

        // ✅ Save user to DB
        $userModel->save([
            'username'   => $this->request->getPost('username'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => 'student',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('login'))
                         ->with('success', 'Registration successful. Please log in.');
    }

    return view('auth/register', $data);
}


    public function login()
    {
        helper(['form']);

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required',
            ];

            if (! $this->validate($rules)) {
                return view('auth/login', ['validation' => $this->validator]);
            }

            $UserModel = new UserModel();
            $user = $UserModel->where('email', $this->request->getPost('email'))->first();

            if (! $user || ! password_verify($this->request->getPost('password'), $user['password'])) {
                return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
            }

            // Set session
            session()->set([
                'userID'     => $user['id'],
                'username'   => $user['username'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'isLoggedIn' => true,
            ]);

            return redirect()->to(base_url('dashboard'))
                             ->with('success', 'Welcome back, ' . $user['username'] . '!');
        }

        return view('auth/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))
                         ->with('success', 'You have been logged out.');
    }

    public function dashboard()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))
                             ->with('error', 'Please log in to continue.');
        }

        return view('auth/dashboard');
    }
}
