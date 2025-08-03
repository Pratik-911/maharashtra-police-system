<?php

namespace App\Controllers\Officer;

use App\Controllers\BaseController;
use App\Models\LocationLogModel;
use App\Models\DutyModel;
use App\Models\ComplianceModel;

class LocationController extends BaseController
{
    protected $locationLogModel;
    protected $dutyModel;
    protected $complianceModel;

    public function __construct()
    {
        $this->locationLogModel = new LocationLogModel();
        $this->dutyModel = new DutyModel();
        $this->complianceModel = new ComplianceModel();
    }

    public function consent()
    {
        $officer_id = session()->get('officer_id');
        $active_duty = $this->dutyModel->getOfficerActiveDuty($officer_id);

        if (!$active_duty || !$active_duty['location_tracking_enabled']) {
            return redirect()->to('/officer/dashboard')->with('error', 'सध्या कोणतीही सक्रिय ड्यूटी नाही किंवा स्थान ट्रॅकिंग सक्षम नाही');
        }

        $data = [
            'title' => 'स्थान ट्रॅकिंग परवानगी',
            'duty' => $active_duty
        ];

        return view('officer/location/consent', $data);
    }

    public function grantConsent()
    {
        $officer_id = session()->get('officer_id');
        $active_duty = $this->dutyModel->getOfficerActiveDuty($officer_id);

        if (!$active_duty || !$active_duty['location_tracking_enabled']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'सध्या कोणतीही सक्रिय ड्यूटी नाही'
            ]);
        }

        // Set consent in session
        session()->set('location_consent_granted', true);
        session()->set('tracking_duty_id', $active_duty['duty_id']);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'स्थान ट्रॅकिंग सुरू केले गेले',
            'duty_id' => $active_duty['duty_id']
        ]);
    }

    public function update()
    {
        if (!session()->get('location_consent_granted')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'स्थान ट्रॅकिंग परवानगी नाही'
            ]);
        }

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $officer_id = session()->get('officer_id');
        $duty_id = session()->get('tracking_duty_id');

        if (!$latitude || !$longitude) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'अवैध स्थान डेटा'
            ]);
        }

        // Check if duty is still active
        if (!$this->dutyModel->isDutyActive($duty_id)) {
            session()->remove('location_consent_granted');
            session()->remove('tracking_duty_id');
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ड्यूटी संपली आहे'
            ]);
        }

        // Log the location
        $logged = $this->locationLogModel->logLocation($officer_id, $duty_id, $latitude, $longitude);

        if ($logged) {
            // Recalculate compliance
            $this->complianceModel->calculateCompliance($officer_id, $duty_id);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'स्थान यशस्वीरित्या नोंदवले गेले'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'स्थान नोंदवण्यात त्रुटी'
            ]);
        }
    }
}
