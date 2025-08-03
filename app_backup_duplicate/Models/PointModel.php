<?php

namespace App\Models;

use CodeIgniter\Model;

class PointModel extends Model
{
    protected $table = 'points';
    protected $primaryKey = 'point_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'zone_id', 'police_station_id', 'name', 'latitude', 'longitude', 'radius'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'timestamp';
    protected $updatedField = 'last_updated';

    // Validation
    protected $validationRules = [
        'zone_id' => 'required|max_length[50]',
        'police_station_id' => 'required|max_length[50]',
        'name' => 'required|min_length[2]|max_length[255]',
        'latitude' => 'required|decimal|greater_than[-90]|less_than[90]',
        'longitude' => 'required|decimal|greater_than[-180]|less_than[180]',
        'radius' => 'required|integer|greater_than[0]|less_than_equal_to[5000]'
    ];

    protected $validationMessages = [
        'zone_id' => [
            'required' => 'झोन आयडी आवश्यक आहे'
        ],
        'police_station_id' => [
            'required' => 'पोलीस स्टेशन आयडी आवश्यक आहे'
        ],
        'name' => [
            'required' => 'पॉइंटचे नाव आवश्यक आहे',
            'min_length' => 'नाव किमान 2 अक्षरांचे असावे'
        ],
        'latitude' => [
            'required' => 'अक्षांश आवश्यक आहे',
            'decimal' => 'वैध अक्षांश प्रविष्ट करा',
            'greater_than' => 'अक्षांश -90 पेक्षा जास्त असावा',
            'less_than' => 'अक्षांश 90 पेक्षा कमी असावा'
        ],
        'longitude' => [
            'required' => 'रेखांश आवश्यक आहे',
            'decimal' => 'वैध रेखांश प्रविष्ट करा',
            'greater_than' => 'रेखांश -180 पेक्षा जास्त असावा',
            'less_than' => 'रेखांश 180 पेक्षा कमी असावा'
        ],
        'radius' => [
            'required' => 'त्रिज्या आवश्यक आहे',
            'integer' => 'त्रिज्या पूर्ण संख्या असावी',
            'greater_than' => 'त्रिज्या 0 पेक्षा जास्त असावी',
            'less_than_equal_to' => 'त्रिज्या 5000 मीटरपेक्षा कमी असावी'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    public function getAllPoints()
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    public function getPointsByZone($zone_id)
    {
        return $this->where('zone_id', $zone_id)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function getPointsByStation($police_station_id)
    {
        return $this->where('police_station_id', $police_station_id)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Earth radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c; // Distance in meters
    }

    public function isWithinRadius($pointId, $latitude, $longitude)
    {
        $point = $this->find($pointId);
        if (!$point) {
            return false;
        }

        $distance = $this->calculateDistance(
            $point['latitude'], 
            $point['longitude'], 
            $latitude, 
            $longitude
        );

        return $distance <= $point['radius'];
    }
}
