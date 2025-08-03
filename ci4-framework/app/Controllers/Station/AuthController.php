<?php

namespace App\Controllers\Station;

use App\Controllers\BaseController;
use App\Models\PoliceStationModel;

class AuthController extends BaseController
{
    protected $stationModel;

    public function __construct()
    {
        $this->stationModel = new PoliceStationModel();
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('station_logged_in')) {
            return redirect()->to('/station/dashboard');
        }

        return view('station/auth/login');
    }

    public function authenticate()
    {
        $rules = [
            'station_id' => 'required',
            'password' => 'required'
        ];

        $messages = [
            'station_id' => [
                'required' => 'पोलीस स्टेशन आयडी आवश्यक आहे'
            ],
            'password' => [
                'required' => 'पासवर्ड आवश्यक आहे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $stationId = $this->request->getPost('station_id');
        $password = $this->request->getPost('password');

        $station = $this->stationModel->authenticate($stationId, $password);

        if ($station) {
            $sessionData = [
                'station_id' => $station['id'],
                'station_code' => $station['station_id'],
                'station_name' => $station['station_name'],
                'station_logged_in' => true
            ];

            session()->set($sessionData);
            return redirect()->to('/station/dashboard')->with('success', 'यशस्वीरित्या लॉगिन झाले');
        } else {
            return redirect()->back()->withInput()->with('error', 'चुकीचे स्टेशन आयडी किंवा पासवर्ड');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/station/login')->with('success', 'यशस्वीरित्या लॉगआउट झाले');
    }
}
