<?php

namespace App\Models;

use CodeIgniter\Model;

class ComplianceModel extends Model
{
    protected $table = 'compliance';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'duty_id', 'officer_id', 'compliance_percent', 'total_logs', 'compliant_logs'
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'calculated_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'duty_id' => 'required|integer',
        'officer_id' => 'required|integer',
        'compliance_percent' => 'required|decimal|greater_than_equal_to[0]|less_than_equal_to[100]',
        'total_logs' => 'required|integer|greater_than_equal_to[0]',
        'compliant_logs' => 'required|integer|greater_than_equal_to[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    public function calculateCompliance($officer_id, $duty_id)
    {
        $locationModel = new LocationLogModel();
        $dutyModel = new DutyModel();
        $pointModel = new PointModel();
        
        // Get duty details
        $duty = $dutyModel->getDutyWithDetails($duty_id);
        if (!$duty) {
            return false;
        }
        
        // Get all location logs for this duty
        $logs = $locationModel->getLocationHistory($officer_id, $duty_id);
        
        if (empty($logs)) {
            return $this->updateOrCreateCompliance($duty_id, $officer_id, 0, 0, 0);
        }
        
        $totalLogs = count($logs);
        $compliantLogs = 0;
        
        // Check each log against the point radius
        foreach ($logs as $log) {
            if ($pointModel->isWithinRadius($duty['point_id'], $log['latitude'], $log['longitude'])) {
                $compliantLogs++;
            }
        }
        
        $compliancePercent = ($totalLogs > 0) ? ($compliantLogs / $totalLogs) * 100 : 0;
        
        return $this->updateOrCreateCompliance($duty_id, $officer_id, $compliancePercent, $totalLogs, $compliantLogs);
    }
    
    private function updateOrCreateCompliance($duty_id, $officer_id, $compliance_percent, $total_logs, $compliant_logs)
    {
        $existing = $this->where('duty_id', $duty_id)
                         ->where('officer_id', $officer_id)
                         ->first();
        
        $data = [
            'compliance_percent' => round($compliance_percent, 2),
            'total_logs' => $total_logs,
            'compliant_logs' => $compliant_logs
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['duty_id'] = $duty_id;
            $data['officer_id'] = $officer_id;
            return $this->insert($data);
        }
    }

    public function getComplianceByDuty($duty_id)
    {
        return $this->select('compliance.*, officers.name, officers.badge_no')
                    ->join('officers', 'officers.id = compliance.officer_id')
                    ->where('compliance.duty_id', $duty_id)
                    ->orderBy('compliance.compliance_percent', 'DESC')
                    ->findAll();
    }

    public function getComplianceByOfficer($officer_id, $limit = null)
    {
        $builder = $this->select('compliance.*, duties.date, duties.start_time, duties.end_time, points.name as point_name')
                        ->join('duties', 'duties.duty_id = compliance.duty_id')
                        ->join('points', 'points.point_id = duties.point_id')
                        ->where('compliance.officer_id', $officer_id)
                        ->orderBy('duties.date', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    public function getAverageCompliance($officer_id, $days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));
        
        return $this->select('AVG(compliance_percent) as avg_compliance')
                    ->join('duties', 'duties.duty_id = compliance.duty_id')
                    ->where('compliance.officer_id', $officer_id)
                    ->where('duties.date >=', $since)
                    ->first();
    }

    public function getComplianceSummary($start_date = null, $end_date = null)
    {
        $builder = $this->select('
                        officers.name, 
                        officers.badge_no,
                        AVG(compliance.compliance_percent) as avg_compliance,
                        COUNT(compliance.id) as total_duties,
                        SUM(CASE WHEN compliance.compliance_percent >= 80 THEN 1 ELSE 0 END) as good_duties
                    ')
                    ->join('officers', 'officers.id = compliance.officer_id')
                    ->join('duties', 'duties.duty_id = compliance.duty_id')
                    ->groupBy('compliance.officer_id');
        
        if ($start_date) {
            $builder->where('duties.date >=', $start_date);
        }
        
        if ($end_date) {
            $builder->where('duties.date <=', $end_date);
        }
        
        return $builder->orderBy('avg_compliance', 'DESC')->findAll();
    }

    public function getLowComplianceAlerts($threshold = 70)
    {
        return $this->select('compliance.*, officers.name, officers.badge_no, duties.date, points.name as point_name')
                    ->join('officers', 'officers.id = compliance.officer_id')
                    ->join('duties', 'duties.duty_id = compliance.duty_id')
                    ->join('points', 'points.point_id = duties.point_id')
                    ->where('compliance.compliance_percent <', $threshold)
                    ->where('duties.date >=', date('Y-m-d', strtotime('-7 days')))
                    ->orderBy('compliance.compliance_percent', 'ASC')
                    ->findAll();
    }

    public function recalculateAllCompliance()
    {
        $dutyOfficerModel = new DutyOfficerModel();
        $assignments = $dutyOfficerModel->findAll();
        
        $updated = 0;
        foreach ($assignments as $assignment) {
            if ($this->calculateCompliance($assignment['officer_id'], $assignment['duty_id'])) {
                $updated++;
            }
        }
        
        return $updated;
    }
}
