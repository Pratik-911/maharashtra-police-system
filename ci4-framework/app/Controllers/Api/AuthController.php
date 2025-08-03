<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\OfficerModel;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends BaseController
{
    protected $officerModel;

    public function __construct()
    {
        $this->officerModel = new OfficerModel();
    }

    public function mobileLogin()
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
        
        // Log the incoming request for debugging
        log_message('info', 'Mobile login API called - Method: ' . $this->request->getMethod());
        
        try {
            // Get JSON data
            $json = $this->request->getJSON();
            if (!$json) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Invalid JSON data'
                ]);
            }
            
            $badgeNumber = $json->badge_number ?? null;
            $password = $json->password ?? null;
            $deviceInfo = $json->device_info ?? null;
            
            // Validate required fields
            if (!$badgeNumber || !$password) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Badge number and password are required'
                ]);
            }
            
            // Find officer by badge number
            $officer = $this->officerModel->where('badge_no', $badgeNumber)->first();
            
            if (!$officer) {
                log_message('warning', 'Mobile login failed - Officer not found: ' . $badgeNumber);
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'अवैध बॅज नंबर किंवा पासवर्ड'
                ]);
            }
            
            // Verify password
            if (!password_verify($password, $officer['password'])) {
                log_message('warning', 'Mobile login failed - Invalid password for: ' . $badgeNumber);
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'अवैध बॅज नंबर किंवा पासवर्ड'
                ]);
            }
            
            // Officer found and password verified - proceed with login
            
            // Generate JWT token
            $tokenPayload = [
                'officer_id' => $officer['id'],
                'badge_number' => $officer['badge_no'],
                'name' => $officer['name'],
                'iat' => time(),
                'exp' => time() + (24 * 60 * 60), // 24 hours
                'device_info' => $deviceInfo
            ];
            
            $jwtSecret = getenv('JWT_SECRET') ?: 'maharashtra_police_secret_key_2024';
            $token = JWT::encode($tokenPayload, $jwtSecret, 'HS256');
            
            // Log successful login
            log_message('info', 'Mobile login successful for officer: ' . $badgeNumber);
            
            // Return success response
            return $this->response->setJSON([
                'success' => true,
                'message' => 'लॉगिन यशस्वी',
                'data' => [
                    'id' => $officer['id'],
                    'badge_number' => $officer['badge_no'],
                    'name' => $officer['name'],
                    'rank' => $officer['rank'],
                    'police_station' => $officer['police_station'],
                    'mobile' => $officer['mobile'],
                    'token' => $token,
                    'expires_at' => date('Y-m-d H:i:s', time() + (24 * 60 * 60))
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Mobile login error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'सर्व्हर त्रुटी. कृपया पुन्हा प्रयत्न करा.'
            ]);
        }
    }
    
    public function verifyToken()
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
            $authHeader = $this->request->getHeader('Authorization');
            if (!$authHeader) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Authorization header missing'
                ]);
            }
            
            $token = str_replace('Bearer ', '', $authHeader->getValue());
            $jwtSecret = getenv('JWT_SECRET') ?: 'maharashtra_police_secret_key_2024';
            
            $decoded = JWT::decode($token, new Key($jwtSecret, 'HS256'));
            
            // Get officer data
            $officer = $this->officerModel->find($decoded->officer_id);
            if (!$officer) {
                return $this->response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Invalid or expired token'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'officer_id' => $officer['id'],
                    'badge_number' => $officer['badge_no'],
                    'name' => $officer['name'],
                    'valid' => true
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Token verification error: ' . $e->getMessage());
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Invalid or expired token'
            ]);
        }
    }
}
