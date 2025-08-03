<?php

namespace App\Models;

use CodeIgniter\Model;

class DutyOfficerModel extends Model
{
    protected $table = 'duty_officers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['duty_id', 'officer_id'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'duty_id' => 'required|integer',
        'officer_id' => 'required|integer'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    public function assignOfficersToDuty($duty_id, $officer_ids)
    {
        // First, remove existing assignments
        $this->where('duty_id', $duty_id)->delete();
        
        // Then add new assignments
        $data = [];
        foreach ($officer_ids as $officer_id) {
            $data[] = [
                'duty_id' => $duty_id,
                'officer_id' => $officer_id
            ];
        }
        
        if (!empty($data)) {
            return $this->insertBatch($data);
        }
        
        return true;
    }

    public function getOfficersByDuty($duty_id)
    {
        return $this->select('officers.*')
                    ->join('officers', 'officers.id = duty_officers.officer_id')
                    ->where('duty_officers.duty_id', $duty_id)
                    ->findAll();
    }

    public function getDutiesByOfficer($officer_id)
    {
        return $this->select('duties.*, points.name as point_name')
                    ->join('duties', 'duties.duty_id = duty_officers.duty_id')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->where('duty_officers.officer_id', $officer_id)
                    ->orderBy('duties.date', 'DESC')
                    ->findAll();
    }

    public function removeOfficerFromDuty($duty_id, $officer_id)
    {
        return $this->where('duty_id', $duty_id)
                    ->where('officer_id', $officer_id)
                    ->delete();
    }

    public function isOfficerAssigned($duty_id, $officer_id)
    {
        return $this->where('duty_id', $duty_id)
                    ->where('officer_id', $officer_id)
                    ->first() !== null;
    }
}
