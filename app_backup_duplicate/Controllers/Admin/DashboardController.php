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
        $data = [
            'title' => 'प्रशासक डॅशबोर्ड',
            'total_officers' => $this->officerModel->countAll(),
            'total_points' => $this->pointModel->countAll(),
            'total_duties' => $this->dutyModel->countAll(),
            'active_duties' => count($this->dutyModel->getActiveDuties()),
            'todays_duties' => $this->dutyModel->getTodaysDuties(),
            'low_compliance_alerts' => $this->complianceModel->getLowComplianceAlerts(70),
            'compliance_summary' => $this->complianceModel->getComplianceSummary(date('Y-m-d', strtotime('-7 days')), date('Y-m-d'))
        ];

        return view('admin/dashboard/index', $data);
    }
}
