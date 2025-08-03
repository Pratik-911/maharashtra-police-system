<?php
require_once 'ci4-framework/vendor/autoload.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Load the ComplianceModel
$complianceModel = new \App\Models\ComplianceModel();

// Recalculate compliance for duty 37, officer 3
echo "Recalculating compliance for duty 37, officer 3...\n";
$result = $complianceModel->calculateEnhancedCompliance(3, 37);

if ($result) {
    echo "✅ Compliance recalculated successfully!\n";
} else {
    echo "❌ Failed to recalculate compliance.\n";
}

// Check the updated compliance
$db = \Config\Database::connect();
$query = $db->query("SELECT compliance_percent, tracking_off_minutes, total_duty_minutes FROM compliance WHERE duty_id = 37 AND officer_id = 3");
$compliance = $query->getRow();

if ($compliance) {
    echo "\n📊 Updated Compliance Data:\n";
    echo "Compliance Percent: {$compliance->compliance_percent}%\n";
    echo "Tracking Off Minutes: {$compliance->tracking_off_minutes}\n";
    echo "Total Duty Minutes: {$compliance->total_duty_minutes}\n";
    
    $trackedMinutes = $compliance->total_duty_minutes - $compliance->tracking_off_minutes;
    $trackingTimePercent = ($trackedMinutes / $compliance->total_duty_minutes) * 100;
    echo "Tracked Minutes: {$trackedMinutes}\n";
    echo "Tracking Time Percent: {$trackingTimePercent}%\n";
} else {
    echo "❌ No compliance data found.\n";
}
