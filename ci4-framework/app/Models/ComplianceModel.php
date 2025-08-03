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
        'duty_id', 'officer_id', 'compliance_percent', 'total_logs', 'compliant_logs',
        'total_duty_minutes', 'compliant_minutes', 'non_compliant_minutes', 'tracking_off_minutes',
        'last_location_update', 'alert_count', 'last_alert_sent'
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
        log_message('info', 'ComplianceModel: Starting compliance calculation for officer ' . $officer_id . ', duty ' . $duty_id);
        
        $locationModel = new LocationLogModel();
        $dutyModel = new DutyModel();
        $pointModel = new PointModel();
        
        // Get duty details
        $duty = $dutyModel->getDutyWithDetails($duty_id);
        if (!$duty) {
            log_message('error', 'ComplianceModel: Duty not found: ' . $duty_id);
            return false;
        }
        
        log_message('info', 'ComplianceModel: Duty found: ' . json_encode($duty));
        
        // Get all location logs for this duty
        $logs = $locationModel->getLocationHistory($officer_id, $duty_id);
        
        log_message('info', 'ComplianceModel: Found ' . count($logs) . ' location logs');
        
        if (empty($logs)) {
            log_message('info', 'ComplianceModel: No logs found, setting 0% compliance');
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
        
        log_message('info', 'ComplianceModel: Calculated compliance - Total: ' . $totalLogs . ', Compliant: ' . $compliantLogs . ', Percent: ' . $compliancePercent);
        
        $result = $this->updateOrCreateCompliance($duty_id, $officer_id, $compliancePercent, $totalLogs, $compliantLogs);
        
        log_message('info', 'ComplianceModel: Update result: ' . ($result ? 'SUCCESS' : 'FAILED'));
        
        return $result;
    }
    
    private function updateOrCreateCompliance($duty_id, $officer_id, $compliance_percent, $total_logs, $compliant_logs)
    {
        $existing = $this->where('duty_id', $duty_id)
                         ->where('officer_id', $officer_id)
                         ->first();
        
        $data = [
            'compliance_percent' => round($compliance_percent, 2),
            'total_logs' => $total_logs,
            'compliant_logs' => $compliant_logs,
            'calculated_at' => date('Y-m-d H:i:s')
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
    
    public function getOfficerCompliance($officer_id, $duty_id = null)
    {
        $builder = $this->where('officer_id', $officer_id);
        
        if ($duty_id) {
            $builder->where('duty_id', $duty_id);
        }
        
        $result = $builder->orderBy('calculated_at', 'DESC')->first();
        
        return $result ? $result['compliance_percent'] : 0;
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

    /**
     * Enhanced compliance calculation with weighted formula
     * Formula: 50% tracking time + 50% location accuracy
     */
    public function calculateEnhancedCompliance($officer_id, $duty_id)
    {
        $locationModel = new LocationLogModel();
        $dutyModel = new DutyModel();
        $pointModel = new PointModel();
        
        // Get duty details
        $duty = $dutyModel->getDutyWithDetails($duty_id);
        if (!$duty) {
            return false;
        }
        
        // Calculate total duty duration in minutes
        $startTime = strtotime($duty['date'] . ' ' . $duty['start_time']);
        $endTime = strtotime($duty['date'] . ' ' . $duty['end_time']);
        $totalDutyMinutes = ($endTime - $startTime) / 60;
        
        // Get all location logs for this duty
        $logs = $locationModel->getLocationHistory($officer_id, $duty_id);
        
        $trackingOffMinutes = 0;
        $lastLocationUpdate = null;
        
        // Check if duty has location tracking enabled
        if (!$duty['location_tracking_enabled']) {
            // For duties without location tracking, set default compliance
            return $this->updateOrCreateEnhancedCompliance(
                $duty_id, 
                $officer_id, 
                100, // 100% compliance for non-tracked duties
                0, // No logs
                0, // No compliant logs count
                $totalDutyMinutes,
                $totalDutyMinutes, // All time considered compliant
                0, // No non-compliant time
                0, // No tracking off time
                null // No location updates
            );
        }
        
        if (!empty($logs)) {
            // Sort logs by timestamp
            usort($logs, function($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });
            
            $lastLocationUpdate = end($logs)['timestamp'];
            $firstLogTime = strtotime($logs[0]['timestamp']);
            $lastLogTime = strtotime(end($logs)['timestamp']);
            
            // Calculate tracking off time (gaps between consecutive logs > 40 seconds)
            // Note: We don't penalize time before first log or after last log
            // because having logs means tracking was active during those periods
            for ($i = 0; $i < count($logs) - 1; $i++) {
                $currentTime = strtotime($logs[$i]['timestamp']);
                $nextTime = strtotime($logs[$i + 1]['timestamp']);
                $gap = ($nextTime - $currentTime); // Gap in seconds
                
                if ($gap > 40) { // More than 40 seconds gap = tracking was off
                    $trackingOffMinutes += ($gap / 60); // Convert to minutes
                }
            }
        } else {
            // No location logs for duty with location tracking enabled
            $trackingOffMinutes = $totalDutyMinutes;
        }
        
        // Calculate compliance using weighted formula
        // Formula: 50% tracking time + 50% location accuracy
        
        $trackingTimePercent = 0;
        $locationAccuracyPercent = 0;
        
        if ($totalDutyMinutes > 0) {
            // Calculate tracking time percentage (time when tracking was active)
            $trackedMinutes = $totalDutyMinutes - $trackingOffMinutes;
            $trackingTimePercent = ($trackedMinutes / $totalDutyMinutes) * 100;
            
            // Calculate location accuracy percentage (pings inside vs total pings)
            if (count($logs) > 0) {
                $pingsInside = 0;
                foreach ($logs as $log) {
                    if ($pointModel->isWithinRadius($duty['point_id'], $log['latitude'], $log['longitude'])) {
                        $pingsInside++;
                    }
                }
                $locationAccuracyPercent = ($pingsInside / count($logs)) * 100;
            } else {
                // No pings = 0% location accuracy
                $locationAccuracyPercent = 0;
            }
        }
        
        // Weighted compliance: 50% tracking time + 50% location accuracy
        $compliancePercent = (0.5 * $trackingTimePercent) + (0.5 * $locationAccuracyPercent);
        
        // Update or create compliance record
        return $this->updateOrCreateEnhancedCompliance(
            $duty_id, 
            $officer_id, 
            $compliancePercent, 
            count($logs), 
            0, // Will be calculated separately
            $totalDutyMinutes,
            0, // Not using compliant_minutes in new formula
            0, // Not using non_compliant_minutes in new formula
            $trackingOffMinutes,
            $lastLocationUpdate
        );
    }
    
    private function updateOrCreateEnhancedCompliance($duty_id, $officer_id, $compliance_percent, $total_logs, $compliant_logs, $total_duty_minutes, $compliant_minutes, $non_compliant_minutes, $tracking_off_minutes, $last_location_update)
    {
        $existing = $this->where('duty_id', $duty_id)
                         ->where('officer_id', $officer_id)
                         ->first();
        
        $data = [
            'compliance_percent' => round($compliance_percent, 2),
            'total_logs' => $total_logs,
            'compliant_logs' => $compliant_logs,
            'total_duty_minutes' => $total_duty_minutes,
            'compliant_minutes' => $compliant_minutes,
            'non_compliant_minutes' => $non_compliant_minutes,
            'tracking_off_minutes' => $tracking_off_minutes,
            'last_location_update' => $last_location_update
        ];
        
        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            $data['duty_id'] = $duty_id;
            $data['officer_id'] = $officer_id;
            return $this->insert($data);
        }
    }
    
    /**
     * Check if officer needs location tracking alert
     */
    public function checkLocationTrackingAlert($officer_id, $duty_id)
    {
        $compliance = $this->where('duty_id', $duty_id)
                           ->where('officer_id', $officer_id)
                           ->first();
        
        if (!$compliance) {
            return ['needs_alert' => false, 'alert_type' => null];
        }
        
        $now = time();
        $lastUpdate = $compliance['last_location_update'] ? strtotime($compliance['last_location_update']) : 0;
        $lastAlert = $compliance['last_alert_sent'] ? strtotime($compliance['last_alert_sent']) : 0;
        $alertCount = $compliance['alert_count'] ?? 0;
        
        $timeSinceLastUpdate = $now - $lastUpdate;
        $timeSinceLastAlert = $now - $lastAlert;
        
        // First alert after 15 seconds
        if ($timeSinceLastUpdate >= 15 && $alertCount == 0 && $timeSinceLastAlert >= 15) {
            return ['needs_alert' => true, 'alert_type' => 'first_warning', 'message' => 'कृपया स्थान ट्रॅकिंग चालू करा (15 सेकंद)'];
        }
        
        // Second alert after 1 minute
        if ($timeSinceLastUpdate >= 60 && $alertCount == 1 && $timeSinceLastAlert >= 45) {
            return ['needs_alert' => true, 'alert_type' => 'second_warning', 'message' => 'तातडीने स्थान ट्रॅकिंग चालू करा (1 मिनिट)'];
        }
        
        // After 2 alerts, start counting as non-compliant time
        if ($timeSinceLastUpdate >= 60 && $alertCount >= 2) {
            return ['needs_alert' => false, 'alert_type' => 'non_compliant', 'message' => 'अनुपस्थित मानले जात आहे'];
        }
        
        return ['needs_alert' => false, 'alert_type' => null];
    }
    
    /**
     * Record alert sent to officer
     */
    public function recordAlertSent($officer_id, $duty_id, $alert_type)
    {
        $compliance = $this->where('duty_id', $duty_id)
                           ->where('officer_id', $officer_id)
                           ->first();
        
        if ($compliance) {
            $alertCount = ($compliance['alert_count'] ?? 0) + 1;
            return $this->update($compliance['id'], [
                'alert_count' => $alertCount,
                'last_alert_sent' => date('Y-m-d H:i:s')
            ]);
        }
        
        return false;
    }
    
    /**
     * Get compliance data for speedometer display
     */
    public function getComplianceForSpeedometer($officer_id, $duty_id)
    {
        $compliance = $this->where('duty_id', $duty_id)
                           ->where('officer_id', $officer_id)
                           ->first();
        
        if (!$compliance) {
            return [
                'compliance_percent' => 0,
                'status' => 'no_data',
                'color' => '#dc3545', // Red
                'message' => 'डेटा उपलब्ध नाही'
            ];
        }
        
        $percent = $compliance['compliance_percent'];
        
        // Determine color and status based on compliance percentage
        if ($percent >= 90) {
            $color = '#28a745'; // Green
            $status = 'excellent';
            $message = 'उत्कृष्ट अनुपालन';
        } elseif ($percent >= 80) {
            $color = '#20c997'; // Teal
            $status = 'good';
            $message = 'चांगले अनुपालन';
        } elseif ($percent >= 70) {
            $color = '#ffc107'; // Yellow
            $status = 'average';
            $message = 'सरासरी अनुपालन';
        } elseif ($percent >= 50) {
            $color = '#fd7e14'; // Orange
            $status = 'poor';
            $message = 'कमी अनुपालन';
        } else {
            $color = '#dc3545'; // Red
            $status = 'very_poor';
            $message = 'अत्यंत कमी अनुपालन';
        }
        
        return [
            'compliance_percent' => round($percent, 1),
            'status' => $status,
            'color' => $color,
            'message' => $message,
            'total_duty_minutes' => $compliance['total_duty_minutes'] ?? 0,
            'compliant_minutes' => $compliance['compliant_minutes'] ?? 0,
            'non_compliant_minutes' => $compliance['non_compliant_minutes'] ?? 0,
            'tracking_off_minutes' => $compliance['tracking_off_minutes'] ?? 0,
            'last_location_update' => $compliance['last_location_update']
        ];
    }

    /**
     * Calculate compliance for all completed duties that don't have compliance data
     */
    public function calculateComplianceForCompletedDuties()
    {
        $dutyModel = new DutyModel();
        $dutyOfficerModel = new \App\Models\DutyOfficerModel();
        
        // Get current time
        $currentTime = date('Y-m-d H:i:s');
        $currentDate = date('Y-m-d');
        $currentTimeOnly = date('H:i:s');
        
        // Find duties that should be completed (end time has passed)
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT d.duty_id, d.date, d.start_time, d.end_time, d.location_tracking_enabled,
                   do.officer_id
            FROM duties d
            JOIN duty_officers do ON d.duty_id = do.duty_id
            LEFT JOIN compliance c ON d.duty_id = c.duty_id AND do.officer_id = c.officer_id
            WHERE (
                (d.date < ? AND c.id IS NULL) OR
                (d.date = ? AND d.end_time <= ? AND c.id IS NULL)
            )
            ORDER BY d.duty_id DESC
        ", [$currentDate, $currentDate, $currentTimeOnly]);
        
        $completedDuties = $query->getResultArray();
        $processedCount = 0;
        
        foreach ($completedDuties as $duty) {
            try {
                $result = $this->calculateEnhancedCompliance($duty['officer_id'], $duty['duty_id']);
                if ($result) {
                    $processedCount++;
                    log_message('info', "Calculated compliance for duty {$duty['duty_id']}, officer {$duty['officer_id']}");
                }
            } catch (\Exception $e) {
                log_message('error', "Failed to calculate compliance for duty {$duty['duty_id']}: " . $e->getMessage());
            }
        }
        
        return [
            'total_duties' => count($completedDuties),
            'processed' => $processedCount
        ];
    }

    /**
     * Get compliance data for admin dashboard
     */
    public function getComplianceForAdmin($limit = 10)
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT c.*, d.date, d.start_time, d.end_time, 
                   o.name as officer_name, o.badge_no,
                   p.name as point_name
            FROM compliance c
            JOIN duties d ON c.duty_id = d.duty_id
            JOIN officers o ON c.officer_id = o.id
            JOIN duty_officers do ON d.duty_id = do.duty_id AND c.officer_id = do.officer_id
            JOIN points p ON d.point_id = p.point_id
            ORDER BY d.date DESC, d.start_time DESC
            LIMIT ?
        ", [$limit]);
        
        return $query->getResultArray();
    }
}
