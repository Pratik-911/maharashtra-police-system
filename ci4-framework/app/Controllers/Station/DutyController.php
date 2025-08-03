<?php

namespace App\Controllers\Station;

use App\Controllers\BaseController;
use App\Models\DutyModel;
use App\Models\DutyOfficerModel;
use App\Models\OfficerModel;
use App\Models\PointModel;

class DutyController extends BaseController
{
    protected $dutyModel;
    protected $dutyOfficerModel;
    protected $officerModel;
    protected $pointModel;

    public function __construct()
    {
        $this->dutyModel = new DutyModel();
        $this->dutyOfficerModel = new DutyOfficerModel();
        $this->officerModel = new OfficerModel();
        $this->pointModel = new PointModel();
    }

    public function index()
    {
        $stationCode = session()->get('station_code');
        
        $data = [
            'duties' => $this->getDutiesForStation($stationCode),
            'active_duties' => $this->getActiveDutiesForStation($stationCode),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/duties/index', $data);
    }

    public function create()
    {
        $stationCode = session()->get('station_code');
        
        $data = [
            'officers' => $this->officerModel->where('police_station', $stationCode)->findAll(),
            'points' => $this->pointModel->where('police_station', $stationCode)->findAll(),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/duties/create', $data);
    }

    public function store()
    {
        $stationCode = session()->get('station_code');
        
        $rules = [
            'officer_id' => 'required|integer',
            'point_id' => 'required|integer',
            'date' => 'required|valid_date',
            'start_time' => 'required',
            'end_time' => 'required'
        ];

        $messages = [
            'officer_id' => [
                'required' => 'अधिकारी निवडणे आवश्यक आहे',
                'integer' => 'योग्य अधिकारी निवडा'
            ],
            'point_id' => [
                'required' => 'पॉइंट निवडणे आवश्यक आहे',
                'integer' => 'योग्य पॉइंट निवडा'
            ],
            'date' => [
                'required' => 'तारीख आवश्यक आहे',
                'valid_date' => 'योग्य तारीख प्रविष्ट करा'
            ],
            'start_time' => [
                'required' => 'सुरुवातीची वेळ आवश्यक आहे'
            ],
            'end_time' => [
                'required' => 'समाप्तीची वेळ आवश्यक आहे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify officer and point belong to this station
        $officer = $this->officerModel->where('id', $this->request->getPost('officer_id'))
                                    ->where('police_station', $stationCode)
                                    ->first();
        $point = $this->pointModel->where('id', $this->request->getPost('point_id'))
                                 ->where('police_station', $stationCode)
                                 ->first();

        if (!$officer || !$point) {
            return redirect()->back()->withInput()->with('error', 'अधिकारी किंवा पॉइंट आपल्या स्टेशनमध्ये सापडला नाही');
        }

        // Check for time conflicts
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        $date = $this->request->getPost('date');

        if ($startTime >= $endTime) {
            return redirect()->back()->withInput()->with('error', 'समाप्तीची वेळ सुरुवातीच्या वेळेनंतर असावी');
        }

        // Check for existing duty conflicts for the officer
        $existingDuty = $this->dutyOfficerModel
            ->select('duty_officers.*, duties.date, duties.start_time, duties.end_time')
            ->join('duties', 'duties.duty_id = duty_officers.duty_id')
            ->where('duty_officers.officer_id', $officer['id'])
            ->where('duties.date', $date)
            ->where('(duties.start_time <= "' . $endTime . '" AND duties.end_time >= "' . $startTime . '")')
            ->first();

        if ($existingDuty) {
            return redirect()->back()->withInput()->with('error', 'या अधिकाऱ्याची या वेळेत आधीच ड्यूटी आहे');
        }

        // Create duty first
        $dutyData = [
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'shift' => (strtotime($startTime) < strtotime('18:00:00')) ? 'Day' : 'Night',
            'point_id' => $point['point_id']
        ];

        if ($this->dutyModel->save($dutyData)) {
            $dutyId = $this->dutyModel->getInsertID();
            
            // Create duty-officer relationship
            $dutyOfficerData = [
                'duty_id' => $dutyId,
                'officer_id' => $officer['id']
            ];
            
            if ($this->dutyOfficerModel->save($dutyOfficerData)) {
                return redirect()->to('/station/duties')->with('success', 'ड्यूटी यशस्वीरित्या वाटप केली गेली');
            }
        }
        
        return redirect()->back()->withInput()->with('error', 'ड्यूटी वाटप करताना त्रुटी झाली');
    }

    public function edit($id)
    {
        $stationCode = session()->get('station_code');
        
        $duty = $this->dutyModel->select('duties.*, officers.name as officer_name, points.name as point_name')
                               ->join('officers', 'officers.id = duties.officer_id')
                               ->join('points', 'points.id = duties.point_id')
                               ->where('duties.id', $id)
                               ->where('duties.police_station_id', $stationCode)
                               ->first();

        if (!$duty) {
            return redirect()->to('/station/duties')->with('error', 'ड्यूटी सापडली नाही');
        }

        $data = [
            'duty' => $duty,
            'officers' => $this->officerModel->where('police_station', $stationCode)->findAll(),
            'points' => $this->pointModel->where('police_station', $stationCode)->findAll(),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/duties/edit', $data);
    }

    public function update($id)
    {
        $stationCode = session()->get('station_code');
        
        $duty = $this->dutyModel->where('id', $id)
                               ->where('police_station_id', $stationCode)
                               ->first();

        if (!$duty) {
            return redirect()->to('/station/duties')->with('error', 'ड्यूटी सापडली नाही');
        }

        $rules = [
            'officer_id' => 'required|integer',
            'point_id' => 'required|integer',
            'date' => 'required|valid_date',
            'start_time' => 'required',
            'end_time' => 'required'
        ];

        $messages = [
            'officer_id' => [
                'required' => 'अधिकारी निवडणे आवश्यक आहे',
                'integer' => 'योग्य अधिकारी निवडा'
            ],
            'point_id' => [
                'required' => 'पॉइंट निवडणे आवश्यक आहे',
                'integer' => 'योग्य पॉइंट निवडा'
            ],
            'date' => [
                'required' => 'तारीख आवश्यक आहे',
                'valid_date' => 'योग्य तारीख प्रविष्ट करा'
            ],
            'start_time' => [
                'required' => 'सुरुवातीची वेळ आवश्यक आहे'
            ],
            'end_time' => [
                'required' => 'समाप्तीची वेळ आवश्यक आहे'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Verify officer and point belong to this station
        $officer = $this->officerModel->where('id', $this->request->getPost('officer_id'))
                                    ->where('police_station', $stationCode)
                                    ->first();
        $point = $this->pointModel->where('id', $this->request->getPost('point_id'))
                                 ->where('police_station', $stationCode)
                                 ->first();

        if (!$officer || !$point) {
            return redirect()->back()->withInput()->with('error', 'अधिकारी किंवा पॉइंट आपल्या स्टेशनमध्ये सापडला नाही');
        }

        // Check time validity
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        $date = $this->request->getPost('date');

        if ($startTime >= $endTime) {
            return redirect()->back()->withInput()->with('error', 'समाप्तीची वेळ सुरुवातीच्या वेळेनंतर असावी');
        }

        // Check for existing duty conflicts (excluding current duty)
        $existingDuty = $this->dutyModel->where('officer_id', $officer['id'])
                                       ->where('date', $date)
                                       ->where('id !=', $id)
                                       ->where('(start_time <= "' . $endTime . '" AND end_time >= "' . $startTime . '")')
                                       ->first();

        if ($existingDuty) {
            return redirect()->back()->withInput()->with('error', 'या अधिकाऱ्याची या वेळेत आधीच ड्यूटी आहे');
        }

        $data = [
            'officer_id' => $officer['id'],
            'point_id' => $point['id'],
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime
        ];

        if ($this->dutyModel->update($id, $data)) {
            return redirect()->to('/station/duties')->with('success', 'ड्यूटी यशस्वीरित्या अपडेट केली गेली');
        } else {
            return redirect()->back()->withInput()->with('error', 'ड्यूटी अपडेट करताना त्रुटी झाली');
        }
    }

    public function delete($id)
    {
        $stationCode = session()->get('station_code');
        
        $duty = $this->dutyModel->where('id', $id)
                               ->where('police_station_id', $stationCode)
                               ->first();

        if (!$duty) {
            return redirect()->to('/station/duties')->with('error', 'ड्यूटी सापडली नाही');
        }

        if ($this->dutyModel->delete($id)) {
            return redirect()->to('/station/duties')->with('success', 'ड्यूटी यशस्वीरित्या डिलीट केली गेली');
        } else {
            return redirect()->to('/station/duties')->with('error', 'ड्यूटी डिलीट करताना त्रुटी झाली');
        }
    }

    private function getDutiesForStation($stationCode)
    {
        return $this->dutyModel
            ->select('duties.*, points.name as point_name, officers.name as officer_name')
            ->join('points', 'points.point_id = duties.point_id')
            ->join('duty_officers', 'duty_officers.duty_id = duties.duty_id')
            ->join('officers', 'officers.id = duty_officers.officer_id')
            ->where('officers.police_station', $stationCode)
            ->orderBy('duties.date', 'DESC')
            ->orderBy('duties.start_time', 'DESC')
            ->findAll();
    }

    private function getActiveDutiesForStation($stationCode)
    {
        $today = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        return $this->dutyModel
            ->select('duties.*, points.name as point_name, officers.name as officer_name')
            ->join('points', 'points.point_id = duties.point_id')
            ->join('duty_officers', 'duty_officers.duty_id = duties.duty_id')
            ->join('officers', 'officers.id = duty_officers.officer_id')
            ->where('officers.police_station', $stationCode)
            ->where('duties.date', $today)
            ->where('duties.start_time <=', $currentTime)
            ->where('duties.end_time >=', $currentTime)
            ->findAll();
    }
}
