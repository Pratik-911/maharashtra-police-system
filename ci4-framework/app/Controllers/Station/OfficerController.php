<?php

namespace App\Controllers\Station;

use App\Controllers\BaseController;
use App\Models\OfficerModel;

class OfficerController extends BaseController
{
    protected $officerModel;

    public function __construct()
    {
        $this->officerModel = new OfficerModel();
    }

    public function index()
    {
        $stationCode = session()->get('station_code');
        
        $data = [
            'officers' => $this->officerModel->where('police_station', $stationCode)
                                           ->orderBy('name', 'ASC')
                                           ->findAll(),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/officers/index', $data);
    }

    public function create()
    {
        $data = [
            'station_name' => session()->get('station_name'),
            'station_code' => session()->get('station_code')
        ];

        return view('station/officers/create', $data);
    }

    public function store()
    {
        $stationCode = session()->get('station_code');
        
        $rules = [
            'name' => 'required|min_length[3]',
            'badge_no' => 'required|is_unique[officers.badge_no]',
            'rank' => 'required',
            'mobile' => 'required|min_length[10]|max_length[15]',
            'password' => 'required|min_length[6]'
        ];

        $messages = [
            'name' => [
                'required' => 'अधिकाऱ्याचे नाव आवश्यक आहे',
                'min_length' => 'नाव किमान 3 अक्षरांचे असावे'
            ],
            'badge_no' => [
                'required' => 'बॅज नंबर आवश्यक आहे',
                'is_unique' => 'हा बॅज नंबर आधीच वापरला आहे'
            ],
            'rank' => [
                'required' => 'पद आवश्यक आहे'
            ],
            'mobile' => [
                'required' => 'मोबाइल नंबर आवश्यक आहे',
                'min_length' => 'मोबाइल नंबर किमान 10 अंकांचा असावे',
                'max_length' => 'मोबाइल नंबर जास्तीत जास्त 15 अंकांचा असावे'
            ],
            'password' => [
                'required' => 'पासवर्ड आवश्यक आहे',
                'min_length' => 'पासवर्ड किमान 6 अक्षरांचा असावे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'badge_no' => $this->request->getPost('badge_no'),
            'rank' => $this->request->getPost('rank'),
            'police_station' => $stationCode, // Automatically set to current station
            'mobile' => $this->request->getPost('mobile'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT)
        ];

        if ($this->officerModel->save($data)) {
            return redirect()->to('/station/officers')->with('success', 'अधिकारी यशस्वीरित्या जोडला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'अधिकारी जोडताना त्रुटी झाली');
        }
    }

    public function edit($id)
    {
        $stationCode = session()->get('station_code');
        
        $officer = $this->officerModel->where('id', $id)
                                    ->where('police_station', $stationCode)
                                    ->first();

        if (!$officer) {
            return redirect()->to('/station/officers')->with('error', 'अधिकारी सापडला नाही');
        }

        $data = [
            'officer' => $officer,
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/officers/edit', $data);
    }

    public function update($id)
    {
        $stationCode = session()->get('station_code');
        
        $officer = $this->officerModel->where('id', $id)
                                    ->where('police_station', $stationCode)
                                    ->first();

        if (!$officer) {
            return redirect()->to('/station/officers')->with('error', 'अधिकारी सापडला नाही');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'badge_no' => "required|is_unique[officers.badge_no,id,{$id}]",
            'rank' => 'required',
            'mobile' => 'required|min_length[10]|max_length[15]'
        ];

        $messages = [
            'name' => [
                'required' => 'अधिकाऱ्याचे नाव आवश्यक आहे',
                'min_length' => 'नाव किमान 3 अक्षरांचे असावे'
            ],
            'badge_no' => [
                'required' => 'बॅज नंबर आवश्यक आहे',
                'is_unique' => 'हा बॅज नंबर आधीच वापरला आहे'
            ],
            'rank' => [
                'required' => 'पद आवश्यक आहे'
            ],
            'mobile' => [
                'required' => 'मोबाइल नंबर आवश्यक आहे',
                'min_length' => 'मोबाइल नंबर किमान 10 अंकांचा असावे',
                'max_length' => 'मोबाइल नंबर जास्तीत जास्त 15 अंकांचा असावे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'badge_no' => $this->request->getPost('badge_no'),
            'rank' => $this->request->getPost('rank'),
            'mobile' => $this->request->getPost('mobile')
        ];

        // Update password only if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if ($this->officerModel->update($id, $data)) {
            return redirect()->to('/station/officers')->with('success', 'अधिकारी यशस्वीरित्या अपडेट केला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'अधिकारी अपडेट करताना त्रुटी झाली');
        }
    }

    public function delete($id)
    {
        $stationCode = session()->get('station_code');
        
        $officer = $this->officerModel->where('id', $id)
                                    ->where('police_station', $stationCode)
                                    ->first();

        if (!$officer) {
            return redirect()->to('/station/officers')->with('error', 'अधिकारी सापडला नाही');
        }

        if ($this->officerModel->delete($id)) {
            return redirect()->to('/station/officers')->with('success', 'अधिकारी यशस्वीरित्या डिलीट केला गेला');
        } else {
            return redirect()->to('/station/officers')->with('error', 'अधिकारी डिलीट करताना त्रुटी झाली');
        }
    }
}
