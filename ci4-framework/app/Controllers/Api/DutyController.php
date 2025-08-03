<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\DutyModel;
use App\Models\OfficerModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class DutyController extends BaseController
{
    protected $dutyModel;
    protected $officerModel;

    public function __construct()
    {
        $this->dutyModel = new DutyModel();
        $this->officerModel = new OfficerModel();
    }

    public function getActiveDuty($officerId)
    {
        // Set response headers for CORS and JSON
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
                      ->setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
                      ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                      ->setContentType('application/json');
        
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        try {
            // Verify JWT token
            $officerData = $this->verifyJWTToken();
            if (!$officerData) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }
            
            // Ensure officer can only access their own duties
            if ($officerData['officer_id'] != $officerId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ]);
            }
            
            // Get active duty with debugging
            log_message('debug', 'Getting active duty for officer: ' . $officerId);
            $activeDuty = $this->dutyModel->getOfficerActiveDuty($officerId);
            log_message('debug', 'Active duty result: ' . json_encode($activeDuty));
            
            if ($activeDuty) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [
                        'id' => $activeDuty['id'],
                        'point_id' => $activeDuty['point_id'],
                        'point_name' => $activeDuty['point_name'],
                        'point_latitude' => $activeDuty['point_latitude'],
                        'point_longitude' => $activeDuty['point_longitude'],
                        'radius_meters' => $activeDuty['radius_meters'],
                        'start_time' => $activeDuty['start_time'],
                        'end_time' => $activeDuty['end_time'],
                        'status' => $activeDuty['status'],
                        'location_tracking_enabled' => (bool)$activeDuty['location_tracking_enabled'],
                        'created_at' => $activeDuty['created_at']
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => null,
                    'message' => 'No active duty found'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Get active duty error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    public function getOfficerDuties($officerId)
    {
        // Set response headers for CORS and JSON
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
                      ->setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS')
                      ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                      ->setContentType('application/json');
        
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        try {
            // Verify JWT token
            $officerData = $this->verifyJWTToken();
            if (!$officerData) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }
            
            // Ensure officer can only access their own duties
            if ($officerData['officer_id'] != $officerId) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'Access denied'
                ]);
            }
            
            // Get recent duties (last 30 days)
            $duties = $this->dutyModel->getOfficerDuties($officerId, 30);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $duties
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Get officer duties error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    public function startDuty()
    {
        // Set response headers for CORS and JSON
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
                      ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                      ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                      ->setContentType('application/json');
        
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        try {
            // Verify JWT token
            $officerData = $this->verifyJWTToken();
            if (!$officerData) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }
            
            // Get JSON data
            $json = $this->request->getJSON();
            if (!$json) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ]);
            }
            
            $dutyId = $json->duty_id ?? null;
            
            if (!$dutyId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Duty ID is required'
                ]);
            }
            
            // Check if duty exists and belongs to officer
            $duty = $this->dutyModel->find($dutyId);
            if (!$duty || $duty['officer_id'] != $officerData['officer_id']) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Duty not found or access denied'
                ]);
            }
            
            // Start the duty
            $result = $this->dutyModel->update($dutyId, [
                'status' => 'active',
                'actual_start_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Duty started successfully'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to start duty'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Start duty error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    public function endDuty()
    {
        // Set response headers for CORS and JSON
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
                      ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                      ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With')
                      ->setContentType('application/json');
        
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        try {
            // Verify JWT token
            $officerData = $this->verifyJWTToken();
            if (!$officerData) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ]);
            }
            
            // Get JSON data
            $json = $this->request->getJSON();
            if (!$json) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ]);
            }
            
            $dutyId = $json->duty_id ?? null;
            
            if (!$dutyId) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Duty ID is required'
                ]);
            }
            
            // Check if duty exists and belongs to officer
            $duty = $this->dutyModel->find($dutyId);
            if (!$duty || $duty['officer_id'] != $officerData['officer_id']) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Duty not found or access denied'
                ]);
            }
            
            // End the duty
            $result = $this->dutyModel->update($dutyId, [
                'status' => 'completed',
                'actual_end_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($result) {
                // Trigger compliance calculation
                $complianceModel = new \App\Models\ComplianceModel();
                $complianceModel->calculateEnhancedCompliance($officerData['officer_id'], $dutyId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Duty ended successfully'
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Failed to end duty'
                ]);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'End duty error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Server error'
            ]);
        }
    }
    
    private function verifyJWTToken()
    {
        try {
            $authHeader = $this->request->getHeader('Authorization');
            if (!$authHeader) {
                return false;
            }
            
            $token = str_replace('Bearer ', '', $authHeader->getValue());
            $jwtSecret = getenv('JWT_SECRET') ?: 'maharashtra_police_secret_key_2024';
            
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            
            return [
                'officer_id' => $decoded->officer_id,
                'badge_number' => $decoded->badge_number,
                'name' => $decoded->name
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'JWT verification error: ' . $e->getMessage());
            return false;
        }
    }
}
