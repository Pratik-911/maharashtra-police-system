<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\OfficerModel;

class AuthController extends BaseController
{
    protected $officerModel;

    public function __construct()
    {
        $this->officerModel = new OfficerModel();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('officer_logged_in')) {
            return redirect()->to('/officer/dashboard');
        }

        return view('officer/auth/login');
    }

    public function authenticate()
    {
        $rules = [
            'badge_no' => 'required',
            'password' => 'required'
        ];

        $messages = [
            'badge_no' => [
                'required' => 'बॅज नंबर आवश्यक आहे'
            ],
            'password' => [
                'required' => 'पासवर्ड आवश्यक आहे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $badge_no = $this->request->getPost('badge_no');
        $password = $this->request->getPost('password');

        $officer = $this->officerModel->getOfficerByBadge($badge_no);

        if ($officer && $this->officerModel->verifyPassword($password, $officer['password'])) {
            $sessionData = [
                'officer_id' => $officer['id'],
                'officer_badge_no' => $officer['badge_no'],
                'officer_name' => $officer['name'],
                'officer_rank' => $officer['rank'],
                'officer_station' => $officer['police_station'],
                'officer_logged_in' => true
            ];

            session()->set($sessionData);
            return redirect()->to('/officer/dashboard')->with('success', 'यशस्वीरित्या लॉगिन झाले');
        } else {
            return redirect()->back()->withInput()->with('error', 'चुकीचा बॅज नंबर किंवा पासवर्ड');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/officer/login')->with('success', 'यशस्वीरित्या लॉगआउट झाले');
    }
}
