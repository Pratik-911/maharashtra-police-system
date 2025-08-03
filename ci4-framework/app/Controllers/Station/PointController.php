<?php

namespace App\Controllers\Station;

use App\Controllers\BaseController;
use App\Models\PointModel;

class PointController extends BaseController
{
    protected $pointModel;

    public function __construct()
    {
        $this->pointModel = new PointModel();
    }

    public function index()
    {
        $stationCode = session()->get('station_code');
        
        $data = [
            'points' => $this->pointModel->where('police_station_id', $stationCode)
                                       ->orderBy('name', 'ASC')
                                       ->findAll(),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/points/index', $data);
    }

    public function create()
    {
        $data = [
            'station_name' => session()->get('station_name'),
            'station_code' => session()->get('station_code')
        ];

        return view('station/points/create', $data);
    }

    public function store()
    {
        $stationCode = session()->get('station_code');
        
        $rules = [
            'name' => 'required|min_length[3]',
            'location' => 'required|min_length[5]',
            'latitude' => 'required|decimal',
            'longitude' => 'required|decimal',
            'description' => 'permit_empty'
        ];

        $messages = [
            'name' => [
                'required' => 'पॉइंटचे नाव आवश्यक आहे',
                'min_length' => 'नाव किमान 3 अक्षरांचे असावे'
            ],
            'location' => [
                'required' => 'स्थान आवश्यक आहे',
                'min_length' => 'स्थान किमान 5 अक्षरांचे असावे'
            ],
            'latitude' => [
                'required' => 'अक्षांश आवश्यक आहे',
                'decimal' => 'योग्य अक्षांश प्रविष्ट करा'
            ],
            'longitude' => [
                'required' => 'रेखांश आवश्यक आहे',
                'decimal' => 'योग्य रेखांश प्रविष्ट करा'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'zone_id' => 'ZONE1', // Default zone
            'police_station_id' => $stationCode,
            'name' => $this->request->getPost('name'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'radius' => 100 // Default radius in meters
        ];

        if ($this->pointModel->save($data)) {
            return redirect()->to('/station/points')->with('success', 'पॉइंट यशस्वीरित्या जोडला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'पॉइंट जोडताना त्रुटी झाली');
        }
    }

    public function edit($id)
    {
        $stationCode = session()->get('station_code');
        
        $point = $this->pointModel->where('point_id', $id)
                                 ->where('police_station_id', $stationCode)
                                 ->first();

        if (!$point) {
            return redirect()->to('/station/points')->with('error', 'पॉइंट सापडला नाही');
        }

        $data = [
            'point' => $point,
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/points/edit', $data);
    }

    public function update($id)
    {
        $stationCode = session()->get('station_code');
        
        $point = $this->pointModel->where('point_id', $id)
                                 ->where('police_station_id', $stationCode)
                                 ->first();

        if (!$point) {
            return redirect()->to('/station/points')->with('error', 'पॉइंट सापडला नाही');
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'location' => 'required|min_length[5]',
            'latitude' => 'required|decimal',
            'longitude' => 'required|decimal',
            'description' => 'permit_empty'
        ];

        $messages = [
            'name' => [
                'required' => 'पॉइंटचे नाव आवश्यक आहे',
                'min_length' => 'नाव किमान 3 अक्षरांचे असावे'
            ],
            'location' => [
                'required' => 'स्थान आवश्यक आहे',
                'min_length' => 'स्थान किमान 5 अक्षरांचे असावे'
            ],
            'latitude' => [
                'required' => 'अक्षांश आवश्यक आहे',
                'decimal' => 'योग्य अक्षांश प्रविष्ट करा'
            ],
            'longitude' => [
                'required' => 'रेखांश आवश्यक आहे',
                'decimal' => 'योग्य रेखांश प्रविष्ट करा'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'radius' => $this->request->getPost('radius') ?: 100
        ];

        if ($this->pointModel->where('point_id', $id)->set($data)->update()) {
            return redirect()->to('/station/points')->with('success', 'पॉइंट यशस्वीरित्या अपडेट केला गेला');
        } else {
            return redirect()->back()->withInput()->with('error', 'पॉइंट अपडेट करताना त्रुटी झाली');
        }
    }

    public function delete($id)
    {
        $stationCode = session()->get('station_code');
        
        $point = $this->pointModel->where('point_id', $id)
                                 ->where('police_station_id', $stationCode)
                                 ->first();

        if (!$point) {
            return redirect()->to('/station/points')->with('error', 'पॉइंट सापडला नाही');
        }

        // Check if point is being used in any duties
        $dutyModel = new \App\Models\DutyModel();
        $activeDuties = $dutyModel->where('point_id', $id)->findAll();
        
        if (!empty($activeDuties)) {
            return redirect()->to('/station/points')->with('error', 'हा पॉइंट ड्यूटीमध्ये वापरला जात आहे, त्यामुळे डिलीट करता येत नाही');
        }

        if ($this->pointModel->where('point_id', $id)->delete()) {
            return redirect()->to('/station/points')->with('success', 'पॉइंट यशस्वीरित्या डिलीट केला गेला');
        } else {
            return redirect()->to('/station/points')->with('error', 'पॉइंट डिलीट करताना त्रुटी झाली');
        }
    }
}
