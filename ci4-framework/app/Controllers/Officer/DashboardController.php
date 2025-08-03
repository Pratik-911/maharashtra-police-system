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
            'officer_name' => session()->get('officer_name') ?? 'अज्ञात अधिकारी',
            'officer_badge' => session()->get('officer_badge_no') ?? 'N/A',
            'officer_rank' => session()->get('officer_rank') ?? 'अज्ञात',
            'officer_station' => session()->get('officer_station') ?? 'अज्ञात स्टेशन',
            'active_duty' => $this->dutyModel->getOfficerActiveDuty($officer_id) ?? null,
            'recent_duties' => $this->dutyModel->getOfficerDuties($officer_id, 5) ?? [],
            'compliance_history' => $this->complianceModel->getComplianceByOfficer($officer_id, 10) ?? [],
            'average_compliance' => $this->complianceModel->getAverageCompliance($officer_id, 30) ?? null
        ];

        return view('officer/dashboard/index', $data);
    }
}
