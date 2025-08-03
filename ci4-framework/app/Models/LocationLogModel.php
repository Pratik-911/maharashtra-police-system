<?php

namespace App\Models;

use CodeIgniter\Model;

class LocationLogModel extends Model
{
    protected $table = 'location_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['officer_id', 'duty_id', 'latitude', 'longitude'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'timestamp';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'officer_id' => 'required|integer',
        'duty_id' => 'required|integer',
        'latitude' => 'required|decimal',
        'longitude' => 'required|decimal'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    public function logLocation($officer_id, $duty_id, $latitude, $longitude)
    {
        $data = [
            'officer_id' => $officer_id,
            'duty_id' => $duty_id,
            'latitude' => $latitude,
            'longitude' => $longitude
        ];
        
        log_message('info', 'LocationLogModel: Attempting to insert data: ' . json_encode($data));
        
        try {
            $result = $this->insert($data);
            
            if ($result) {
                log_message('info', 'LocationLogModel: Insert successful, ID: ' . $result);
                return $result;
            } else {
                $errors = $this->errors();
                log_message('error', 'LocationLogModel: Insert failed - Validation errors: ' . json_encode($errors));
                log_message('error', 'LocationLogModel: Last query: ' . $this->db->getLastQuery());
                return false;
            }
        } catch (\Exception $e) {
            log_message('error', 'LocationLogModel: Exception during insert: ' . $e->getMessage());
            log_message('error', 'LocationLogModel: Exception trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    public function getLocationHistory($officer_id, $duty_id)
    {
        return $this->where('officer_id', $officer_id)
                    ->where('duty_id', $duty_id)
                    ->orderBy('timestamp', 'DESC')
                    ->findAll();
    }

    public function getLatestLocation($officer_id, $duty_id)
    {
        return $this->where('officer_id', $officer_id)
                    ->where('duty_id', $duty_id)
                    ->orderBy('timestamp', 'DESC')
                    ->first();
    }

    public function getLocationsByDuty($duty_id)
    {
        return $this->select('location_logs.*, officers.name, officers.badge_no')
                    ->join('officers', 'officers.id = location_logs.officer_id')
                    ->where('location_logs.duty_id', $duty_id)
                    ->orderBy('location_logs.timestamp', 'DESC')
                    ->findAll();
    }

    public function getRecentLocations($officer_id, $minutes = 60)
    {
        $since = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        
        return $this->where('officer_id', $officer_id)
                    ->where('timestamp >=', $since)
                    ->orderBy('timestamp', 'DESC')
                    ->findAll();
    }

    public function getLocationsBetween($officer_id, $duty_id, $start_time, $end_time)
    {
        return $this->where('officer_id', $officer_id)
                    ->where('duty_id', $duty_id)
                    ->where('timestamp >=', $start_time)
                    ->where('timestamp <=', $end_time)
                    ->orderBy('timestamp', 'ASC')
                    ->findAll();
    }

    public function deleteOldLogs($days = 30)
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('timestamp <', $cutoff)->delete();
    }

    public function getLocationCountForDuty($officer_id, $duty_id)
    {
        return $this->where('officer_id', $officer_id)
                    ->where('duty_id', $duty_id)
                    ->countAllResults();
    }
}
