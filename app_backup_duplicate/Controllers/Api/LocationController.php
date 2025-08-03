<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LocationLogModel;
use App\Models\DutyModel;
use App\Models\ComplianceModel;
use App\Models\OfficerModel;

class LocationController extends BaseController
{
    protected $locationLogModel;
    protected $dutyModel;
    protected $complianceModel;
    protected $officerModel;

    public function __construct()
    {
        $this->locationLogModel = new LocationLogModel();
        $this->dutyModel = new DutyModel();
        $this->complianceModel = new ComplianceModel();
        $this->officerModel = new OfficerModel();
    }

    public function log()
    {
        // This endpoint can be called from mobile apps or web interface
        $json = $this->request->getJSON();
        
        if (!$json) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अवैध डेटा फॉर्मेट'
            ]);
        }

        $officer_id = $json->officer_id ?? null;
        $duty_id = $json->duty_id ?? null;
        $latitude = $json->latitude ?? null;
        $longitude = $json->longitude ?? null;
        $auth_token = $json->auth_token ?? null; // For API authentication

        if (!$officer_id || !$duty_id || !$latitude || !$longitude) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'आवश्यक डेटा गहाळ आहे'
            ]);
        }

        // Verify officer exists
        $officer = $this->officerModel->find($officer_id);
        if (!$officer) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अधिकारी सापडला नाही'
            ]);
        }

        // Check if duty is active and has location tracking enabled
        $duty = $this->dutyModel->getDutyWithDetails($duty_id);
        if (!$duty || !$duty['location_tracking_enabled']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ड्यूटी सक्रिय नाही किंवा स्थान ट्रॅकिंग सक्षम नाही'
            ]);
        }

        if (!$this->dutyModel->isDutyActive($duty_id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ड्यूटी संपली आहे'
            ]);
        }

        // Log the location
        $logged = $this->locationLogModel->logLocation($officer_id, $duty_id, $latitude, $longitude);

        if ($logged) {
            // Recalculate compliance in background
            $this->complianceModel->calculateCompliance($officer_id, $duty_id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'स्थान यशस्वीरित्या नोंदवले गेले',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'स्थान नोंदवण्यात त्रुटी'
            ]);
        }
    }

    public function status($duty_id)
    {
        $duty = $this->dutyModel->getDutyWithDetails($duty_id);
        
        if (!$duty) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ड्यूटी सापडली नाही'
            ]);
        }

        $compliance_data = $this->complianceModel->getComplianceByDuty($duty_id);
        $location_logs = $this->locationLogModel->getLocationsByDuty($duty_id);

        return $this->response->setJSON([
            'success' => true,
            'duty' => $duty,
            'compliance' => $compliance_data,
            'recent_locations' => array_slice($location_logs, 0, 10),
            'is_active' => $this->dutyModel->isDutyActive($duty_id)
        ]);
    }
}
