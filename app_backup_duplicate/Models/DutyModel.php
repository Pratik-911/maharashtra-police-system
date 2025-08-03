<?php

namespace App\Models;

use CodeIgniter\Model;

class DutyModel extends Model
{
    protected $table = 'duties';
    protected $primaryKey = 'duty_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'date', 'start_time', 'end_time', 'free_time_start', 'free_time_end', 
        'shift', 'weekly_holiday', 'comment', 'point_id', 'location_tracking_enabled'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'date' => 'required|valid_date',
        'start_time' => 'required',
        'end_time' => 'required',
        'shift' => 'required|in_list[Day,Night]',
        'point_id' => 'required|integer'
    ];

    protected $validationMessages = [
        'date' => [
            'required' => 'दिनांक आवश्यक आहे',
            'valid_date' => 'वैध दिनांक प्रविष्ट करा'
        ],
        'start_time' => [
            'required' => 'सुरुवातीची वेळ आवश्यक आहे'
        ],
        'end_time' => [
            'required' => 'समाप्तीची वेळ आवश्यक आहे'
        ],
        'shift' => [
            'required' => 'शिफ्ट निवडा',
            'in_list' => 'वैध शिफ्ट निवडा (दिवस/रात्र)'
        ],
        'point_id' => [
            'required' => 'ड्यूटी पॉइंट निवडा',
            'integer' => 'वैध पॉइंट निवडा'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    public function getDutiesWithPoints()
    {
        return $this->select('duties.*, points.name as point_name, points.latitude, points.longitude, points.radius')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->orderBy('duties.date', 'DESC')
                    ->orderBy('duties.start_time', 'DESC')
                    ->findAll();
    }

    public function getDutyWithDetails($duty_id)
    {
        return $this->select('duties.*, points.name as point_name, points.latitude, points.longitude, points.radius')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->where('duties.duty_id', $duty_id)
                    ->first();
    }

    public function getActiveDuties()
    {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        return $this->select('duties.*, points.name as point_name')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->where('duties.date', $currentDate)
                    ->where('duties.start_time <=', $currentTime)
                    ->where('duties.end_time >=', $currentTime)
                    ->findAll();
    }

    public function getOfficerActiveDuty($officer_id)
    {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        return $this->select('duties.*, points.name as point_name, points.latitude, points.longitude, points.radius')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->join('duty_officers', 'duty_officers.duty_id = duties.duty_id')
                    ->where('duty_officers.officer_id', $officer_id)
                    ->where('duties.date', $currentDate)
                    ->where('duties.start_time <=', $currentTime)
                    ->where('duties.end_time >=', $currentTime)
                    ->first();
    }

    public function getOfficerDuties($officer_id, $limit = null)
    {
        $builder = $this->select('duties.*, points.name as point_name')
                        ->join('points', 'points.point_id = duties.point_id')
                        ->join('duty_officers', 'duty_officers.duty_id = duties.duty_id')
                        ->where('duty_officers.officer_id', $officer_id)
                        ->orderBy('duties.date', 'DESC')
                        ->orderBy('duties.start_time', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    public function isDutyActive($duty_id)
    {
        $duty = $this->find($duty_id);
        if (!$duty) {
            return false;
        }

        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        
        return ($duty['date'] == $currentDate && 
                $duty['start_time'] <= $currentTime && 
                $duty['end_time'] >= $currentTime);
    }

    public function getTodaysDuties()
    {
        $currentDate = date('Y-m-d');
        
        return $this->select('duties.*, points.name as point_name')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->where('duties.date', $currentDate)
                    ->orderBy('duties.start_time', 'ASC')
                    ->findAll();
    }
}
