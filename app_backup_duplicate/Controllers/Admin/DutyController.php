<?php

namespace App\Controllers\Admin;

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
        $data = [
            'title' => 'ड्यूटी वाटप व्यवस्थापन',
            'duties' => $this->dutyModel->getDutiesWithPoints()
        ];

        return view('admin/duties/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'नवी ड्यूटी वाटप करा',
            'officers' => $this->officerModel->getAvailableOfficers(),
            'points' => $this->pointModel->getAllPoints()
        ];

        return view('admin/duties/create', $data);
    }

    public function store()
    {
        // Debug: Log all incoming POST data - CORRECT FILE VERSION
        error_log('CORRECT FILE DEBUG - All POST data: ' . print_r($this->request->getPost(), true));
        error_log('CORRECT FILE DEBUG - Request method: ' . $this->request->getMethod());
        error_log('CORRECT FILE DEBUG - User agent: ' . $this->request->getUserAgent());
        
        // Also try file logging
        file_put_contents('/tmp/duty_debug.log', date('Y-m-d H:i:s') . ' - CORRECT FILE - POST data: ' . print_r($this->request->getPost(), true) . "\n", FILE_APPEND);
        
        $rules = [
            'date' => 'required|valid_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'shift' => 'required|in_list[Day,Night]',
            'point_id' => 'required|integer',
            'officers' => 'required'
        ];

        $messages = [
            'date' => [
                'required' => 'दिनांक आवश्यक आहे',
                'valid_date' => 'वैध दिनांक प्रविष्ट करा'
            ],
            'start_time' => [
                'required' => 'सुरुवातीची वेळ आवश्यक आहे'
            ],
            'end_time' => [
                'required' => 'समाप्तीची वेळ आवश्यक आहे'
            ],
            'shift' => [
                'required' => 'शिफ्ट निवडा',
                'in_list' => 'वैध शिफ्ट निवडा (दिवस/रात्र)'
            ],
            'point_id' => [
                'required' => 'ड्यूटी पॉइंट निवडा',
                'integer' => 'वैध पॉइंट निवडा'
            ],
            'officers' => [
                'required' => 'किमान एक अधिकारी निवडा'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            log_message('debug', 'FRONTEND DEBUG - Validation failed: ' . print_r($errors, true));
            log_message('debug', 'FRONTEND DEBUG - Input data that failed: ' . print_r($this->request->getPost(), true));
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Validate time logic
        $start_time = $this->request->getPost('start_time');
        $end_time = $this->request->getPost('end_time');
        $free_time_start = $this->request->getPost('free_time_start');
        $free_time_end = $this->request->getPost('free_time_end');

        log_message('debug', 'FRONTEND DEBUG - Time validation: start=' . $start_time . ', end=' . $end_time . ', free_start=' . $free_time_start . ', free_end=' . $free_time_end);

        if ($start_time >= $end_time) {
            log_message('debug', 'FRONTEND DEBUG - Time validation failed: start_time >= end_time');
            return redirect()->back()->withInput()->with('error', 'समाप्तीची वेळ सुरुवातीच्या वेळेपेक्षा जास्त असावी');
        }

        if ($free_time_start && $free_time_end && $free_time_start >= $free_time_end) {
            log_message('debug', 'FRONTEND DEBUG - Free time validation failed: free_time_start >= free_time_end');
            return redirect()->back()->withInput()->with('error', 'मोकळ्या वेळेची समाप्ती सुरुवातीपेक्षा जास्त असावी');
        }

        $dutyData = [
            'date' => $this->request->getPost('date'),
            'start_time' => $start_time,
            'end_time' => $end_time,
            'free_time_start' => $free_time_start ?: null,
            'free_time_end' => $free_time_end ?: null,
            'shift' => $this->request->getPost('shift'),
            'weekly_holiday' => $this->request->getPost('weekly_holiday') ?: null,
            'comment' => $this->request->getPost('comment') ?: null,
            'point_id' => $this->request->getPost('point_id'),
            'location_tracking_enabled' => $this->request->getPost('location_tracking_enabled') ? 1 : 0
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $duty_id = $this->dutyModel->insert($dutyData);
        
        if ($duty_id) {
            $officers = $this->request->getPost('officers');
            
            // Debug logging
            log_message('debug', 'DUTY ALLOCATION DEBUG - Duty ID: ' . $duty_id);
            log_message('debug', 'DUTY ALLOCATION DEBUG - Officers received: ' . print_r($officers, true));
            log_message('debug', 'DUTY ALLOCATION DEBUG - Officers type: ' . gettype($officers));
            log_message('debug', 'DUTY ALLOCATION DEBUG - Officers count: ' . (is_array($officers) ? count($officers) : 'not array'));
            
            $result = $this->dutyOfficerModel->assignOfficersToDuty($duty_id, $officers);
            log_message('debug', 'DUTY ALLOCATION DEBUG - Assignment result: ' . print_r($result, true));
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'ड्यूटी वाटप करण्यात त्रुटी');
        }

        return redirect()->to('/admin/duties')->with('success', 'ड्यूटी यशस्वीरित्या वाटप केली गेली');
    }

    public function edit($id)
    {
        $duty = $this->dutyModel->getDutyWithDetails($id);
        
        if (!$duty) {
            return redirect()->to('/admin/duties')->with('error', 'ड्यूटी सापडली नाही');
        }

        $assignedOfficers = $this->dutyOfficerModel->getOfficersByDuty($id);
        $assignedOfficerIds = array_column($assignedOfficers, 'id');

        $data = [
            'title' => 'ड्यूटी संपादित करा',
            'duty' => $duty,
            'officers' => $this->officerModel->getAvailableOfficers(),
            'points' => $this->pointModel->getAllPoints(),
            'assigned_officers' => $assignedOfficerIds
        ];

        return view('admin/duties/edit', $data);
    }

    public function update($id)
    {
        $duty = $this->dutyModel->find($id);
        
        if (!$duty) {
            return redirect()->to('/admin/duties')->with('error', 'ड्यूटी सापडली नाही');
        }

        $rules = [
            'date' => 'required|valid_date',
            'start_time' => 'required',
            'end_time' => 'required',
            'shift' => 'required|in_list[Day,Night]',
            'point_id' => 'required|integer',
            'officers' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dutyData = [
            'date' => $this->request->getPost('date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'free_time_start' => $this->request->getPost('free_time_start') ?: null,
            'free_time_end' => $this->request->getPost('free_time_end') ?: null,
            'shift' => $this->request->getPost('shift'),
            'weekly_holiday' => $this->request->getPost('weekly_holiday') ?: null,
            'comment' => $this->request->getPost('comment') ?: null,
            'point_id' => $this->request->getPost('point_id'),
            'location_tracking_enabled' => $this->request->getPost('location_tracking_enabled') ? 1 : 0
        ];

        $db = \Config\Database::connect();
        $db->transStart();

        $this->dutyModel->update($id, $dutyData);
        $officers = $this->request->getPost('officers');
        $this->dutyOfficerModel->assignOfficersToDuty($id, $officers);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'ड्यूटी अपडेट करण्यात त्रुटी');
        }

        return redirect()->to('/admin/duties')->with('success', 'ड्यूटी यशस्वीरित्या अपडेट केली गेली');
    }

    public function delete($id)
    {
        $duty = $this->dutyModel->find($id);
        
        if (!$duty) {
            return redirect()->to('/admin/duties')->with('error', 'ड्यूटी सापडली नाही');
        }

        if ($this->dutyModel->delete($id)) {
            return redirect()->to('/admin/duties')->with('success', 'ड्यूटी यशस्वीरित्या डिलीट केली गेली');
        } else {
            return redirect()->to('/admin/duties')->with('error', 'ड्यूटी डिलीट करण्यात त्रुटी');
        }
    }
}
