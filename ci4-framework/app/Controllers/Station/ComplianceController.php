<?php

namespace App\Controllers\Station;

use App\Controllers\BaseController;
use App\Models\DutyModel;
use App\Models\OfficerModel;
use App\Models\LocationLogModel;

class ComplianceController extends BaseController
{
    protected $dutyModel;
    protected $officerModel;
    protected $locationLogModel;

    public function __construct()
    {
        $this->dutyModel = new DutyModel();
        $this->officerModel = new OfficerModel();
        $this->locationLogModel = new LocationLogModel();
    }

    public function index()
    {
        $stationCode = session()->get('station_code');
        
        // Get station officers
        $officers = $this->officerModel->where('police_station', $stationCode)->findAll();
        $officerIds = array_column($officers, 'id');
        
        // Get compliance data
        $data = [
            'officers' => $officers,
            'active_duties' => $this->dutyModel->getActiveDutiesForStation($stationCode),
            'recent_locations' => $this->getRecentLocationsForStation($officerIds),
            'compliance_stats' => $this->getComplianceStatsForStation($officerIds),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/compliance/index', $data);
    }

    public function live()
    {
        $stationCode = session()->get('station_code');
        
        // Get station officers
        $officers = $this->officerModel->where('police_station', $stationCode)->findAll();
        $officerIds = array_column($officers, 'id');
        
        $data = [
            'officers' => $officers,
            'active_duties' => $this->dutyModel->getActiveDutiesForStation($stationCode),
            'station_name' => session()->get('station_name'),
            'station_code' => $stationCode
        ];

        return view('station/compliance/live', $data);
    }

    public function liveData()
    {
        $stationCode = session()->get('station_code');
        
        // Get station officers
        $officers = $this->officerModel->where('police_station', $stationCode)->findAll();
        $officerIds = array_column($officers, 'id');
        
        if (empty($officerIds)) {
            return $this->response->setJSON([
                'officers' => [],
                'stats' => [
                    'total' => 0,
                    'online' => 0,
                    'compliant' => 0,
                    'non_compliant' => 0
                ]
            ]);
        }
        
        // Get recent location data for station officers
        $recentLocations = $this->locationLogModel
            ->select('location_logs.*, officers.name, officers.badge_no')
            ->join('officers', 'officers.id = location_logs.officer_id')
            ->whereIn('location_logs.officer_id', $officerIds)
            ->where('location_logs.timestamp >=', date('Y-m-d H:i:s', strtotime('-30 minutes')))
            ->orderBy('location_logs.timestamp', 'DESC')
            ->findAll();
        
        // Get active duties for station
        $activeDuties = $this->dutyModel->getActiveDutiesForStation($stationCode);
        
        // Process officer data with compliance status
        $officerData = [];
        $stats = [
            'total' => count($officers),
            'online' => 0,
            'compliant' => 0,
            'non_compliant' => 0
        ];
        
        foreach ($officers as $officer) {
            // Find recent location
            $recentLocation = null;
            foreach ($recentLocations as $location) {
                if ($location['officer_id'] == $officer['id']) {
                    $recentLocation = $location;
                    break;
                }
            }
            
            // Find active duty
            $activeDuty = null;
            foreach ($activeDuties as $duty) {
                if ($duty['officer_id'] == $officer['id']) {
                    $activeDuty = $duty;
                    break;
                }
            }
            
            $isOnline = $recentLocation && (strtotime($recentLocation['timestamp']) > strtotime('-15 minutes'));
            $isCompliant = false;
            
            if ($activeDuty && $recentLocation) {
                // Check if officer is near assigned point (within 100 meters)
                $distance = $this->calculateDistance(
                    $recentLocation['latitude'],
                    $recentLocation['longitude'],
                    $activeDuty['latitude'],
                    $activeDuty['longitude']
                );
                $isCompliant = $distance <= 0.1; // 100 meters
            }
            
            if ($isOnline) {
                $stats['online']++;
                if ($isCompliant) {
                    $stats['compliant']++;
                } else {
                    $stats['non_compliant']++;
                }
            }
            
            $officerData[] = [
                'id' => $officer['id'],
                'name' => $officer['name'],
                'badge_no' => $officer['badge_no'],
                'rank' => $officer['rank'],
                'is_online' => $isOnline,
                'is_compliant' => $isCompliant,
                'has_duty' => !empty($activeDuty),
                'location' => $recentLocation ? [
                    'latitude' => $recentLocation['latitude'],
                    'longitude' => $recentLocation['longitude'],
                    'timestamp' => $recentLocation['timestamp']
                ] : null,
                'duty' => $activeDuty ? [
                    'point_name' => $activeDuty['point_name'],
                    'latitude' => $activeDuty['latitude'],
                    'longitude' => $activeDuty['longitude']
                ] : null
            ];
        }
        
        return $this->response->setJSON([
            'officers' => $officerData,
            'stats' => $stats
        ]);
    }

    private function getRecentLocationsForStation($officerIds)
    {
        if (empty($officerIds)) {
            return [];
        }
        
        return $this->locationLogModel
            ->select('location_logs.*, officers.name, officers.badge_no')
            ->join('officers', 'officers.id = location_logs.officer_id')
            ->whereIn('location_logs.officer_id', $officerIds)
            ->where('location_logs.timestamp >=', date('Y-m-d H:i:s', strtotime('-2 hours')))
            ->orderBy('location_logs.timestamp', 'DESC')
            ->limit(50)
            ->findAll();
    }

    private function getComplianceStatsForStation($officerIds)
    {
        if (empty($officerIds)) {
            return [
                'total_officers' => 0,
                'online_officers' => 0,
                'compliant_officers' => 0,
                'compliance_rate' => 0
            ];
        }
        
        $totalOfficers = count($officerIds);
        
        // Count online officers (location update in last 15 minutes)
        $onlineOfficers = $this->locationLogModel
            ->whereIn('officer_id', $officerIds)
            ->where('timestamp >=', date('Y-m-d H:i:s', strtotime('-15 minutes')))
            ->countAllResults();
        
        // For compliance, we need to check if officers are at their assigned points
        $compliantOfficers = 0; // This would need more complex logic
        
        $complianceRate = $totalOfficers > 0 ? ($compliantOfficers / $totalOfficers) * 100 : 0;
        
        return [
            'total_officers' => $totalOfficers,
            'online_officers' => $onlineOfficers,
            'compliant_officers' => $compliantOfficers,
            'compliance_rate' => round($complianceRate, 1)
        ];
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c; // Distance in kilometers
    }
}
