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
        $data = [
            'title' => 'अनुपालन ट्रॅकिंग',
            'compliance_summary' => $this->complianceModel->getComplianceSummary(),
            'low_compliance_alerts' => $this->complianceModel->getLowComplianceAlerts(70),
            'officers' => $this->officerModel->getAvailableOfficers()
        ];

        return view('admin/compliance/index', $data);
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
            'active_duties' => $this->dutyModel->getActiveDuties()
        ];

        return view('admin/compliance/live', $data);
    }
}
