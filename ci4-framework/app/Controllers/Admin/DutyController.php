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
        // Enhanced debugging for frontend form submission
        $postData = $this->request->getPost();
        $debugInfo = [
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $this->request->getMethod(),
            'user_agent' => $this->request->getUserAgent(),
            'post_data' => $postData,
            'officers_data' => $this->request->getPost('officers'),
            'officers_type' => gettype($this->request->getPost('officers')),
            'csrf_token' => $this->request->getPost('csrf_test_name')
        ];
        
        // Log to file for debugging
        file_put_contents('/tmp/duty_frontend_debug.log', json_encode($debugInfo, JSON_PRETTY_PRINT) . "\n\n", FILE_APPEND);
        
        // Check if this is coming from frontend or API
        $isFromBrowser = strpos($this->request->getUserAgent(), 'Mozilla') !== false;
        if ($isFromBrowser) {
            file_put_contents('/tmp/duty_frontend_debug.log', "FRONTEND BROWSER SUBMISSION DETECTED\n", FILE_APPEND);
        }

        // DEBUG: Log any attempt to reach store method
        log_message('info', 'DUTY STORE METHOD CALLED - Request method: ' . $this->request->getMethod() . ', POST data: ' . json_encode($this->request->getPost()));
        
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
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validate time logic
        $start_time = $this->request->getPost('start_time');
        $end_time = $this->request->getPost('end_time');
        $free_time_start = $this->request->getPost('free_time_start');
        $free_time_end = $this->request->getPost('free_time_end');

        if ($start_time >= $end_time) {
            return redirect()->back()->withInput()->with('error', 'समाप्तीची वेळ सुरुवातीच्या वेळेपेक्षा जास्त असावी');
        }

        if ($free_time_start && $free_time_end && $free_time_start >= $free_time_end) {
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

        log_message('info', 'Starting duty creation process with data: ' . json_encode($dutyData));
        
        $db = \Config\Database::connect();
        $db->transStart();

        $duty_id = $this->dutyModel->insert($dutyData);
        
        if ($duty_id) {
            log_message('info', 'Duty created successfully with ID: ' . $duty_id);
            $officers = $this->request->getPost('officers');
            log_message('info', 'Attempting to assign officers to duty_id: ' . $duty_id . '. Officers: ' . json_encode($officers));
            
            $allocationResult = $this->dutyOfficerModel->assignOfficersToDuty($duty_id, $officers);
            
            if ($allocationResult === false) {
                log_message('error', 'Officer allocation failed for duty_id: ' . $duty_id . '. Rolling back transaction.');
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'ड्यूटी वाटप करण्यात त्रुटी (अधिकारी वाटप अयशस्वी)');
            } else {
                log_message('info', 'Officers successfully allocated to duty_id: ' . $duty_id);
            }
        } else {
            log_message('error', 'Failed to create duty. Data: ' . json_encode($dutyData) . '. Errors: ' . json_encode($this->dutyModel->errors()));
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Database transaction failed for duty creation. duty_id: ' . ($duty_id ?: 'null'));
            return redirect()->back()->withInput()->with('error', 'ड्यूटी वाटप करण्यात त्रुटी');
        }

        log_message('info', 'Duty creation and allocation completed successfully for duty_id: ' . $duty_id);
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

        log_message('info', 'Starting duty update process for duty_id: ' . $id . ' with data: ' . json_encode($dutyData));
        
        $db = \Config\Database::connect();
        $db->transStart();

        $updateResult = $this->dutyModel->update($id, $dutyData);
        
        if ($updateResult) {
            log_message('info', 'Duty updated successfully for duty_id: ' . $id);
            $officers = $this->request->getPost('officers');
            log_message('info', 'Attempting to reassign officers to duty_id: ' . $id . '. Officers: ' . json_encode($officers));
            
            $allocationResult = $this->dutyOfficerModel->assignOfficersToDuty($id, $officers);
            
            if ($allocationResult === false) {
                log_message('error', 'Officer reallocation failed for duty_id: ' . $id . '. Rolling back transaction.');
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'ड्यूटी अपडेट करण्यात त्रुटी (अधिकारी वाटप अयशस्वी)');
            } else {
                log_message('info', 'Officers successfully reallocated to duty_id: ' . $id);
            }
        } else {
            log_message('error', 'Failed to update duty_id: ' . $id . '. Data: ' . json_encode($dutyData) . '. Errors: ' . json_encode($this->dutyModel->errors()));
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Database transaction failed for duty update. duty_id: ' . $id);
            return redirect()->back()->withInput()->with('error', 'ड्यूटी अपडेट करण्यात त्रुटी');
        }

        log_message('info', 'Duty update and reallocation completed successfully for duty_id: ' . $id);
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
