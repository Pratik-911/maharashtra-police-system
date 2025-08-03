<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ComplianceModel;
use App\Models\DutyModel;
use App\Models\OfficerModel;

class ComplianceController extends BaseController
{
    protected $complianceModel;
    protected $dutyModel;
    protected $officerModel;

    public function __construct()
    {
        $this->complianceModel = new ComplianceModel();
        $this->dutyModel = new DutyModel();
        $this->officerModel = new OfficerModel();
    }

    /**
     * Get compliance data for speedometer display
     */
    public function speedometer()
    {
        $officer_id = $this->request->getPost('officer_id');
        $duty_id = $this->request->getPost('duty_id');

        if (!$officer_id || !$duty_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अधिकारी ID आणि ड्यूटी ID आवश्यक आहे'
            ]);
        }

        try {
            // Calculate enhanced compliance
            $this->complianceModel->calculateEnhancedCompliance($officer_id, $duty_id);
            
            // Get compliance data for speedometer
            $complianceData = $this->complianceModel->getComplianceForSpeedometer($officer_id, $duty_id);

            return $this->response->setJSON([
                'success' => true,
                'data' => $complianceData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Compliance speedometer error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अनुपालन डेटा लोड करण्यात त्रुटी'
            ]);
        }
    }

    /**
     * Check if officer needs location tracking alert
     */
    public function checkAlert()
    {
        $officer_id = $this->request->getPost('officer_id');
        $duty_id = $this->request->getPost('duty_id');

        if (!$officer_id || !$duty_id) {
            return $this->response->setJSON([
                'needs_alert' => false,
                'message' => 'अधिकारी ID आणि ड्यूटी ID आवश्यक आहे'
            ]);
        }

        try {
            $alertInfo = $this->complianceModel->checkLocationTrackingAlert($officer_id, $duty_id);
            
            return $this->response->setJSON($alertInfo);

        } catch (\Exception $e) {
            log_message('error', 'Compliance alert check error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'needs_alert' => false,
                'message' => 'अलर्ट तपासण्यात त्रुटी'
            ]);
        }
    }

    /**
     * Record alert sent to officer
     */
    public function recordAlert()
    {
        $officer_id = $this->request->getPost('officer_id');
        $duty_id = $this->request->getPost('duty_id');
        $alert_type = $this->request->getPost('alert_type');

        if (!$officer_id || !$duty_id || !$alert_type) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'सर्व फील्ड आवश्यक आहेत'
            ]);
        }

        try {
            $result = $this->complianceModel->recordAlertSent($officer_id, $duty_id, $alert_type);
            
            return $this->response->setJSON([
                'success' => $result,
                'message' => $result ? 'अलर्ट रेकॉर्ड केला गेला' : 'अलर्ट रेकॉर्ड करण्यात त्रुटी'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Record alert error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अलर्ट रेकॉर्ड करण्यात त्रुटी'
            ]);
        }
    }

    /**
     * Get real-time compliance status for admin dashboard
     */
    public function adminStatus()
    {
        try {
            // Get all active duties with compliance data
            $activeDuties = $this->dutyModel->getActiveDuties();
            $complianceStatus = [];

            foreach ($activeDuties as $duty) {
                // Get officers assigned to this duty
                $officers = $this->dutyModel->getOfficersForDuty($duty['duty_id']);
                
                foreach ($officers as $officer) {
                    $complianceData = $this->complianceModel->getComplianceForSpeedometer($officer['id'], $duty['duty_id']);
                    
                    $complianceStatus[] = [
                        'duty_id' => $duty['duty_id'],
                        'officer_id' => $officer['id'],
                        'officer_name' => $officer['name'],
                        'point_name' => $duty['point_name'],
                        'compliance_percent' => $complianceData['compliance_percent'],
                        'status' => $complianceData['status'],
                        'color' => $complianceData['color'],
                        'last_location_update' => $complianceData['last_location_update']
                    ];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $complianceStatus
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Admin compliance status error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अनुपालन स्थिती लोड करण्यात त्रुटी'
            ]);
        }
    }

    /**
     * Recalculate compliance for all active duties (admin function)
     */
    public function recalculateAll()
    {
        // Check if user is admin
        if (!session()->get('is_admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'प्रशासक परवानगी आवश्यक आहे'
            ]);
        }

        try {
            $updated = $this->complianceModel->recalculateAllCompliance();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "$updated ड्यूटींचे अनुपालन पुन्हा मोजले गेले",
                'updated_count' => $updated
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Recalculate compliance error: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'सर्व अनुपालन डेटा पुन्हा मोजला गेला'
            ]);
        }
    }

    /**
     * Calculate compliance for completed duties
     */
    public function calculateCompleted()
    {
        try {
            $result = $this->complianceModel->calculateComplianceForCompletedDuties();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $result,
                'message' => "पूर्ण झालेल्या {$result['processed']} ड्यूट्यांसाठी अनुपालन मोजले गेले"
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Calculate completed duties compliance error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'पूर्ण झालेल्या ड्यूट्यांचे अनुपालन मोजण्यात त्रुटी'
            ]);
        }
    }

    /**
     * Get compliance data for admin dashboard
     */
    public function adminData()
    {
        try {
            $complianceData = $this->complianceModel->getComplianceForAdmin(20);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $complianceData,
                'count' => count($complianceData)
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get admin compliance data error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अनुपालन डेटा मिळवण्यात त्रुटी'
            ]);
        }
    }
}
