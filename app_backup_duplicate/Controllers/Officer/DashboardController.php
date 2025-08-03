<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\DutyModel;
use App\Models\ComplianceModel;
use App\Models\LocationLogModel;

class DashboardController extends BaseController
{
    protected $dutyModel;
    protected $complianceModel;
    protected $locationLogModel;

    public function __construct()
    {
        $this->dutyModel = new DutyModel();
        $this->complianceModel = new ComplianceModel();
        $this->locationLogModel = new LocationLogModel();
    }

    public function index()
    {
        $officer_id = session()->get('officer_id');
        
        $data = [
            'title' => 'अधिकारी डॅशबोर्ड',
            'officer_name' => session()->get('officer_name'),
            'officer_badge' => session()->get('officer_badge_no'),
            'officer_rank' => session()->get('officer_rank'),
            'active_duty' => $this->dutyModel->getOfficerActiveDuty($officer_id),
            'recent_duties' => $this->dutyModel->getOfficerDuties($officer_id, 5),
            'compliance_history' => $this->complianceModel->getComplianceByOfficer($officer_id, 10),
            'average_compliance' => $this->complianceModel->getAverageCompliance($officer_id, 30)
        ];

        return view('officer/dashboard/index', $data);
    }
}
