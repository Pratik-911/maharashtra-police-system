<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\LocationLogModel;
use App\Models\DutyModel;
use App\Models\ComplianceModel;
use App\Models\OfficerModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        // Set response headers for CORS and JSON
        $this->response->setHeader('Access-Control-Allow-Origin', '*')
                      ->setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS')
                      ->setHeader('Access-Control-Allow-Headers', 'Content-Type, X-Requested-With')
                      ->setContentType('application/json');
        
        // Handle preflight OPTIONS request
        if ($this->request->getMethod() === 'options') {
            return $this->response->setStatusCode(200);
        }
        
        // Verify JWT token first
        $officerData = $this->verifyJWTToken();
        if (!$officerData) {
            log_message('error', 'JWT verification failed for location log API');
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'अधिकृतता सत्यापन अयशस्वी',
                'error_code' => 'UNAUTHORIZED'
            ]);
        }
        
        // Log the incoming request for debugging
        log_message('info', 'Location log API called - Method: ' . $this->request->getMethod());
        log_message('info', 'Request data: ' . json_encode($this->request->getPost()));
        log_message('info', 'JWT verified for officer: ' . $officerData['badge_number']);
        
        // This endpoint can be called from mobile apps (JSON) or web interface (form data)
        $officer_id = null;
        $duty_id = null;
        $latitude = null;
        $longitude = null;
        $auth_token = null;
        
        // Try to get JSON data first (for mobile apps)
        try {
            $json = $this->request->getJSON();
            if ($json) {
                $officer_id = $json->officer_id ?? null;
                $duty_id = $json->duty_id ?? null;
                $latitude = $json->latitude ?? null;
                $longitude = $json->longitude ?? null;
                $auth_token = $json->auth_token ?? null;
                log_message('info', 'Using JSON data for location log');
            }
        } catch (\Exception $e) {
            // JSON parsing failed, ignore and try form data
            log_message('error', 'JSON parsing failed: ' . $e->getMessage());
            $json = null;
        }
        
        // If no JSON data, try form data (for web interface)
        if (!$json) {
            $officer_id = $this->request->getPost('officer_id');
            $duty_id = $this->request->getPost('duty_id');
            $latitude = $this->request->getPost('latitude');
            $longitude = $this->request->getPost('longitude');
            $auth_token = $this->request->getPost('auth_token');
            log_message('info', 'Using form data for location log');
        }

        // Validate required fields
        $missing_fields = [];
        if (!$officer_id) $missing_fields[] = 'officer_id';
        if (!$duty_id) $missing_fields[] = 'duty_id';
        if (!$latitude) $missing_fields[] = 'latitude';
        if (!$longitude) $missing_fields[] = 'longitude';
        
        if (!empty($missing_fields)) {
            log_message('error', 'Missing required fields: ' . implode(', ', $missing_fields));
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'आवश्यक डेटा गहाळ आहे: ' . implode(', ', $missing_fields),
                'missing_fields' => $missing_fields,
                'received_data' => [
                    'officer_id' => $officer_id,
                    'duty_id' => $duty_id,
                    'has_latitude' => !empty($latitude),
                    'has_longitude' => !empty($longitude)
                ]
            ]);
        }
        
        // Validate coordinate format
        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            log_message('error', 'Invalid coordinate format - lat: ' . $latitude . ', lng: ' . $longitude);
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'अवैध निर्देशांक स्वरूप'
            ]);
        }

        // Verify officer exists
        try {
            $officer = $this->officerModel->find($officer_id);
            if (!$officer) {
                log_message('error', 'Officer not found: ' . $officer_id);
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'अधिकारी सापडला नाही',
                    'error_code' => 'OFFICER_NOT_FOUND'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Database error while finding officer: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'डेटाबेस त्रुटी',
                'error_code' => 'DATABASE_ERROR'
            ]);
        }

        // Check if duty is active and has location tracking enabled
        try {
            $duty = $this->dutyModel->getDutyWithDetails($duty_id);
            if (!$duty) {
                log_message('error', 'Duty not found: ' . $duty_id);
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'ड्यूटी सापडली नाही',
                    'error_code' => 'DUTY_NOT_FOUND'
                ]);
            }
            
            if (!isset($duty['location_tracking_enabled']) || !$duty['location_tracking_enabled']) {
                log_message('warning', 'Location tracking not enabled for duty: ' . $duty_id);
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'या ड्यूटीसाठी स्थान ट्रॅकिंग सक्षम नाही',
                    'error_code' => 'TRACKING_DISABLED'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Database error while finding duty: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'ड्यूटी माहिती मिळवण्यात त्रुटी',
                'error_code' => 'DATABASE_ERROR'
            ]);
        }

        // Check if duty is currently active
        try {
            if (!$this->dutyModel->isDutyActive($duty_id)) {
                log_message('warning', 'Duty not active: ' . $duty_id);
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'ड्यूटी सध्या सक्रिय नाही',
                    'error_code' => 'DUTY_INACTIVE',
                    'duty_info' => [
                        'date' => $duty['date'] ?? null,
                        'start_time' => $duty['start_time'] ?? null,
                        'end_time' => $duty['end_time'] ?? null
                    ]
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error checking duty active status: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'ड्यूटी स्थिती तपासण्यात त्रुटी',
                'error_code' => 'STATUS_CHECK_ERROR'
            ]);
        }

        // Log the location
        try {
            log_message('info', 'LocationController: Attempting to log location for officer ' . $officer_id . ', duty ' . $duty_id);
            log_message('info', 'LocationController: Coordinates - lat: ' . $latitude . ', lng: ' . $longitude);
            
            $logged = $this->locationLogModel->logLocation($officer_id, $duty_id, $latitude, $longitude);
            
            log_message('info', 'LocationController: logLocation returned: ' . ($logged ? 'SUCCESS (ID: ' . $logged . ')' : 'FAILED'));

            if ($logged) {
                log_message('info', 'LocationController: Location logged successfully with ID: ' . $logged);
                
                // Recalculate compliance in background
                try {
                    $this->complianceModel->calculateCompliance($officer_id, $duty_id);
                    log_message('info', 'Compliance recalculated successfully');
                } catch (\Exception $e) {
                    log_message('error', 'Error recalculating compliance: ' . $e->getMessage());
                    // Don't fail the request if compliance calculation fails
                }
                
                return $this->response->setStatusCode(200)->setJSON([
                    'success' => true,
                    'message' => 'स्थान यशस्वीरित्या नोंदवले गेले',
                    'timestamp' => date('Y-m-d H:i:s'),
                    'coordinates' => [
                        'latitude' => floatval($latitude),
                        'longitude' => floatval($longitude)
                    ]
                ]);
            } else {
                log_message('error', 'Failed to log location - database insert failed');
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'स्थान नोंदवण्यात डेटाबेस त्रुटी',
                    'error_code' => 'LOG_FAILED'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Exception while logging location: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'स्थान नोंदवण्यात त्रुटी: ' . $e->getMessage(),
                'error_code' => 'EXCEPTION_ERROR'
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

    /**
     * Check if officer is inside or outside duty point radius
     */
    public function checkRadius()
    {
        $officer_id = $this->request->getPost('officer_id');
        $duty_id = $this->request->getPost('duty_id');
        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');

        if (!$officer_id || !$duty_id || !$latitude || !$longitude) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'आवश्यक डेटा गहाळ आहे'
            ]);
        }

        try {
            // Get duty with point details
            $duty = $this->dutyModel->getDutyWithDetails($duty_id);
            if (!$duty) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'ड्यूटी सापडली नाही'
                ]);
            }

            // Calculate distance from duty point
            $pointLat = $duty['latitude'];
            $pointLng = $duty['longitude'];
            $radius = $duty['radius'];

            // Calculate distance using Haversine formula
            $distance = $this->calculateDistance($latitude, $longitude, $pointLat, $pointLng);
            $insideRadius = $distance <= $radius;

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'inside_radius' => $insideRadius,
                    'distance' => round($distance, 2),
                    'radius' => $radius,
                    'point_name' => $duty['point_name'],
                    'status' => $insideRadius ? 'inside' : 'outside'
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Location radius check error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'स्थान तपासण्यात त्रुटी'
            ]);
        }
    }

    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // Distance in meters
    }

    /**
     * Verify JWT token from Authorization header
     */
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
