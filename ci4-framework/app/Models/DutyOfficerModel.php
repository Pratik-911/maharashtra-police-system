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
        log_message('info', 'Starting duty allocation for duty_id: ' . $duty_id . ' with officers: ' . json_encode($officer_ids));
        
        try {
            // Validate inputs
            if (empty($duty_id) || !is_numeric($duty_id)) {
                log_message('error', 'Invalid duty_id provided: ' . $duty_id);
                return false;
            }
            
            if (empty($officer_ids) || !is_array($officer_ids)) {
                log_message('warning', 'No officers provided for duty allocation. duty_id: ' . $duty_id);
                // Still proceed to remove existing assignments
            }
            
            // First, remove existing assignments
            log_message('info', 'Removing existing assignments for duty_id: ' . $duty_id);
            $deletedCount = $this->where('duty_id', $duty_id)->delete();
            log_message('info', 'Removed ' . $deletedCount . ' existing assignments for duty_id: ' . $duty_id);
            
            // Then add new assignments
            $data = [];
            if (!empty($officer_ids)) {
                foreach ($officer_ids as $officer_id) {
                    if (!is_numeric($officer_id)) {
                        log_message('warning', 'Invalid officer_id skipped: ' . $officer_id . ' for duty_id: ' . $duty_id);
                        continue;
                    }
                    $data[] = [
                        'duty_id' => $duty_id,
                        'officer_id' => $officer_id
                    ];
                }
            }
            
            if (!empty($data)) {
                log_message('info', 'Inserting ' . count($data) . ' new assignments for duty_id: ' . $duty_id);
                $result = $this->insertBatch($data);
                
                if ($result) {
                    log_message('info', 'Successfully assigned ' . count($data) . ' officers to duty_id: ' . $duty_id);
                    return $result;
                } else {
                    log_message('error', 'Failed to insert batch assignments for duty_id: ' . $duty_id . '. Errors: ' . json_encode($this->errors()));
                    return false;
                }
            } else {
                log_message('info', 'No valid officers to assign for duty_id: ' . $duty_id);
                return true;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Exception in assignOfficersToDuty for duty_id: ' . $duty_id . '. Error: ' . $e->getMessage());
            return false;
        }
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
