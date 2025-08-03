<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DutyModel;
use App\Models\OfficerModel;
use App\Models\PointModel;
use App\Models\ComplianceModel;

class DashboardController extends BaseController
{
    protected $dutyModel;
    protected $officerModel;
    protected $pointModel;
    protected $complianceModel;

    public function __construct()
    {
        $this->dutyModel = new DutyModel();
        $this->officerModel = new OfficerModel();
        $this->pointModel = new PointModel();
        $this->complianceModel = new ComplianceModel();
    }

    public function index()
    {
        log_message('info', 'Dashboard: Loading admin dashboard data');
        
        try {
            $totalOfficers = $this->officerModel->countAll() ?? 0;
            $totalPoints = $this->pointModel->countAll() ?? 0;
            $totalDuties = $this->dutyModel->countAll() ?? 0;
            $activeDuties = $this->dutyModel->getActiveDuties() ?? [];
            $todaysDuties = $this->dutyModel->getTodaysDuties() ?? [];
            $lowComplianceAlerts = $this->complianceModel->getLowComplianceAlerts(70) ?? [];
            $complianceSummary = $this->complianceModel->getComplianceSummary(date('Y-m-d', strtotime('-7 days')), date('Y-m-d')) ?? [];
            
            log_message('info', 'Dashboard: Retrieved data - Officers: ' . $totalOfficers . ', Points: ' . $totalPoints . ', Total Duties: ' . $totalDuties . ', Active Duties: ' . count($activeDuties) . ', Today\'s Duties: ' . count($todaysDuties));
            
            $data = [
                'title' => 'प्रशासक डॅशबोर्ड',
                'total_officers' => $totalOfficers,
                'total_points' => $totalPoints,
                'total_duties' => $totalDuties,
                'active_duties' => count($activeDuties),
                'todays_duties' => $todaysDuties,
                'low_compliance_alerts' => $lowComplianceAlerts,
                'compliance_summary' => $complianceSummary
            ];
            
            log_message('info', 'Dashboard: Successfully loaded dashboard with ' . count($todaysDuties) . ' today\'s duties and ' . count($lowComplianceAlerts) . ' low compliance alerts');
            
            return view('admin/dashboard/index', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Dashboard: Error loading dashboard data - ' . $e->getMessage());
            
            // Return dashboard with empty data in case of error
            $data = [
                'title' => 'प्रशासक डॅशबोर्ड',
                'total_officers' => 0,
                'total_points' => 0,
                'total_duties' => 0,
                'active_duties' => 0,
                'todays_duties' => [],
                'low_compliance_alerts' => [],
                'compliance_summary' => []
            ];
            
            return view('admin/dashboard/index', $data);
        }
    }
}
