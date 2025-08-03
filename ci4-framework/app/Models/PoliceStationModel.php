<?php

namespace App\Models;

use CodeIgniter\Model;

class PoliceStationModel extends Model
{
    protected $table = 'police_stations';
    protected $primaryKey = 'id';
    protected $allowedFields = ['station_id', 'station_name', 'password', 'address', 'phone'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Authenticate police station login
     */
    public function authenticate($stationId, $password)
    {
        $station = $this->where('station_id', $stationId)->first();
        
        if ($station && password_verify($password, $station['password'])) {
            return $station;
        }
        
        return false;
    }

    /**
     * Get station by station_id
     */
    public function getByStationId($stationId)
    {
        return $this->where('station_id', $stationId)->first();
    }

    /**
     * Get all stations for dropdown/selection
     */
    public function getAllStations()
    {
        return $this->select('id, station_id, station_name')
                    ->orderBy('station_name', 'ASC')
                    ->findAll();
    }

    /**
     * Update station password
     */
    public function updatePassword($stationId, $newPassword)
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->where('station_id', $stationId)
                    ->set(['password' => $hashedPassword])
                    ->update();
    }
}
