<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ComplianceModel;
use App\Models\LocationLogModel;
use App\Models\DutyModel;
use App\Models\OfficerModel;

class ComplianceController extends BaseController
{
    protected $complianceModel;
    protected $locationLogModel;
    protected $dutyModel;
    protected $officerModel;

    public function __construct()
    {
        $this->complianceModel = new ComplianceModel();
        $this->locationLogModel = new LocationLogModel();
        $this->dutyModel = new DutyModel();
        $this->officerModel = new OfficerModel();
    }

    public function index()
    {
        // First, calculate compliance for any completed duties that don't have compliance data
        $this->complianceModel->calculateComplianceForCompletedDuties();
        
        // Get compliance data for admin dashboard
        $complianceData = $this->complianceModel->getComplianceForAdmin(50) ?? [];
        
        // Add missing fields and ensure proper field names
        foreach ($complianceData as &$record) {
            // Ensure compliance_percentage field exists (some views expect this name)
            if (!isset($record['compliance_percentage']) && isset($record['compliance_percent'])) {
                $record['compliance_percentage'] = $record['compliance_percent'];
            }
            
            // Ensure duty_date field exists
            if (!isset($record['duty_date']) && isset($record['date'])) {
                $record['duty_date'] = $record['date'];
            }
            
            // Set default values for missing fields
            $record['compliance_percentage'] = $record['compliance_percentage'] ?? 0;
            $record['officer_name'] = $record['officer_name'] ?? 'अज्ञात';
            $record['badge_no'] = $record['badge_no'] ?? 'N/A';
            $record['point_name'] = $record['point_name'] ?? 'अज्ञात';
            $record['duty_date'] = $record['duty_date'] ?? date('Y-m-d');
        }
        
        $data = [
            'title' => 'अनुपालन व्यवस्थापन',
            'compliance_data' => $complianceData,
            'total_duties' => count($complianceData),
            'high_compliance' => count(array_filter($complianceData, fn($item) => ($item['compliance_percentage'] ?? 0) >= 80)),
            'low_compliance' => count(array_filter($complianceData, fn($item) => ($item['compliance_percentage'] ?? 0) < 60)),
            'average_compliance' => $this->calculateAverageCompliance($complianceData)
        ];

        return view('admin/compliance/index', $data);
    }

    private function calculateAverageCompliance($data)
    {
        if (empty($data)) return 0;
        
        $total = array_sum(array_column($data, 'compliance_percentage'));
        return $total / count($data);
    }

    public function officer($officer_id)
    {
        $officer = $this->officerModel->find($officer_id);
        
        if (!$officer) {
            return redirect()->to('/admin/compliance')->with('error', 'अधिकारी सापडला नाही');
        }

        $data = [
            'title' => 'अधिकारी अनुपालन रिपोर्ट',
            'officer' => $officer,
            'compliance_history' => $this->complianceModel->getComplianceByOfficer($officer_id, 20),
            'average_compliance' => $this->complianceModel->getAverageCompliance($officer_id, 30),
            'recent_duties' => $this->dutyModel->getOfficerDuties($officer_id, 10)
        ];

        return view('admin/compliance/officer', $data);
    }

    public function duty($duty_id)
    {
        $duty = $this->dutyModel->getDutyWithDetails($duty_id);
        
        if (!$duty) {
            return redirect()->to('/admin/compliance')->with('error', 'ड्यूटी सापडली नाही');
        }

        $data = [
            'title' => 'ड्यूटी अनुपालन रिपोर्ट',
            'duty' => $duty,
            'compliance_data' => $this->complianceModel->getComplianceByDuty($duty_id),
            'location_logs' => $this->locationLogModel->getLocationsByDuty($duty_id)
        ];

        return view('admin/compliance/duty', $data);
    }

    public function live()
    {
        $data = [
            'title' => 'लाइव्ह अनुपालन ट्रॅकिंग',
            'active_duties' => $this->dutyModel->getActiveDuties() ?? [],
            'active_officers' => $this->getActiveOfficers(),
            'online_officers' => $this->getOnlineOfficers(),
            'low_compliance' => $this->complianceModel->getLowComplianceAlerts(70) ?? [],
            'total_updates' => $this->getTotalUpdatesCount(),
            'recent_updates' => $this->getRecentUpdates()
        ];

        return view('admin/compliance/live', $data);
    }
    
    public function liveData()
    {
        try {
            // Get fresh data for live tracking
            $activeDuties = $this->dutyModel->getActiveDuties();
            $activeOfficers = $this->getActiveOfficers();
            $onlineOfficers = $this->getOnlineOfficers();
            $lowComplianceAlerts = $this->complianceModel->getLowComplianceAlerts();
            $recentUpdates = $this->getRecentUpdates();
            $totalUpdates = $this->getTotalUpdates();
            
            $data = [
                'success' => true,
                'active_duties' => $activeDuties,
                'active_officers' => $activeOfficers,
                'online_officers' => $onlineOfficers,
                'low_compliance_alerts' => $lowComplianceAlerts,
                'recent_updates' => $recentUpdates,
                'total_updates' => $totalUpdates,
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            return $this->response->setJSON($data);
            
        } catch (Exception $e) {
            log_message('error', 'Live data fetch error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'डेटा मिळवताना त्रुटी झाली',
                'error' => $e->getMessage()
            ]);
        }
    }

    private function getActiveOfficers()
    {
        // Get officers who are currently on duty
        $activeDuties = $this->dutyModel->getActiveDuties() ?? [];
        $officers = [];
        
        foreach ($activeDuties as $duty) {
            $dutyOfficers = $this->dutyModel->getDutyOfficers($duty['duty_id']) ?? [];
            foreach ($dutyOfficers as $officer) {
                $officer['compliance'] = $this->complianceModel->getOfficerCompliance($officer['id'], $duty['duty_id']) ?? 0;
                $officer['last_update'] = date('Y-m-d H:i:s'); // Mock data
                $officers[] = $officer;
            }
        }
        
        return $officers;
    }

    private function getOnlineOfficers()
    {
        // Get officers who have sent location updates in the last 10 minutes
        $locationModel = new \App\Models\LocationLogModel();
        $officerModel = new \App\Models\OfficerModel();
        
        $recentOfficerIds = $locationModel->distinct()
                                         ->select('officer_id')
                                         ->where('timestamp >=', date('Y-m-d H:i:s', strtotime('-10 minutes')))
                                         ->findColumn('officer_id');
        
        if (empty($recentOfficerIds)) {
            return [];
        }
        
        return $officerModel->whereIn('id', $recentOfficerIds)->findAll();
    }

    private function getTotalUpdatesCount()
    {
        // Get real count of location updates for today
        $locationModel = new \App\Models\LocationLogModel();
        return $locationModel->where('DATE(timestamp)', date('Y-m-d'))->countAllResults();
    }

    private function getRecentUpdates()
    {
        // Get real recent location logs from database
        $locationModel = new \App\Models\LocationLogModel();
        $complianceModel = new \App\Models\ComplianceModel();
        
        $recentLogs = $locationModel->select('location_logs.*, officers.name as officer_name, officers.id as officer_id')
                                   ->join('officers', 'officers.id = location_logs.officer_id')
                                   ->where('location_logs.timestamp >=', date('Y-m-d H:i:s', strtotime('-2 hours')))
                                   ->orderBy('location_logs.timestamp', 'DESC')
                                   ->limit(20)
                                   ->findAll();
        
        $updates = [];
        foreach ($recentLogs as $log) {
            // Get compliance for this officer
            $compliance = $complianceModel->getOfficerCompliance($log['officer_id']);
            
            $updates[] = [
                'timestamp' => $log['timestamp'],
                'officer_name' => $log['officer_name'],
                'latitude' => $log['latitude'],
                'longitude' => $log['longitude'],
                'compliance' => $compliance
            ];
        }
        
        return $updates;
    }
}
