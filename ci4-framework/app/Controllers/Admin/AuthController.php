<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminUserModel;

class AuthController extends BaseController
{
    protected $adminModel;

    public function __construct()
    {
        $this->adminModel = new AdminUserModel();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('admin_logged_in')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    public function authenticate()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];

        $messages = [
            'username' => [
                'required' => 'वापरकर्ता नाव आवश्यक आहे'
            ],
            'password' => [
                'required' => 'पासवर्ड आवश्यक आहे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $admin = $this->adminModel->authenticate($username, $password);

        if ($admin) {
            $sessionData = [
                'admin_id' => $admin['id'],
                'admin_username' => $admin['username'],
                'admin_full_name' => $admin['full_name'],
                'admin_logged_in' => true
            ];

            session()->set($sessionData);
            return redirect()->to('/admin/dashboard')->with('success', 'यशस्वीरित्या लॉगिन झाले');
        } else {
            return redirect()->back()->withInput()->with('error', 'चुकीचे वापरकर्ता नाव किंवा पासवर्ड');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('success', 'यशस्वीरित्या लॉगआउट झाले');
    }
}
