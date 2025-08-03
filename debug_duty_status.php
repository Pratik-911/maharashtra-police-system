<?php
// Debug script to test duty status logic
require_once 'ci4-framework/app/Config/Database.php';

$db = \Config\Database::connect();

// Get current date and time
$currentDate = date('Y-m-d');
$currentTime = date('H:i:s');

echo "=== DUTY STATUS DEBUG ===\n";
echo "Current Date: $currentDate\n";
echo "Current Time: $currentTime\n\n";

// Check duty 19 details
$duty = $db->query("SELECT * FROM duties WHERE duty_id = 19")->getRowArray();
if ($duty) {
    echo "Duty 19 Details:\n";
    echo "Date: {$duty['date']}\n";
    echo "Start Time: {$duty['start_time']}\n";
    echo "End Time: {$duty['end_time']}\n";
    echo "Shift: {$duty['shift']}\n\n";
    
    // Test each condition
    echo "Condition Tests:\n";
    echo "Date Match: " . ($duty['date'] == $currentDate ? 'YES' : 'NO') . "\n";
    echo "Start Time Check: {$duty['start_time']} <= $currentTime = " . ($duty['start_time'] <= $currentTime ? 'YES' : 'NO') . "\n";
    echo "End Time Check: {$duty['end_time']} >= $currentTime = " . ($duty['end_time'] >= $currentTime ? 'YES' : 'NO') . "\n";
    
    $isActive = ($duty['date'] == $currentDate && 
                 $duty['start_time'] <= $currentTime && 
                 $duty['end_time'] >= $currentTime);
    echo "\nOverall Active Status: " . ($isActive ? 'ACTIVE' : 'NOT ACTIVE') . "\n\n";
}

// Test the actual model method for officer 1
echo "=== TESTING getOfficerActiveDuty for Officer 1 ===\n";
$activeDutyQuery = "
    SELECT duties.*, points.name as point_name, points.latitude, points.longitude, points.radius
    FROM duties
    JOIN points ON points.point_id = duties.point_id
    JOIN duty_officers ON duty_officers.duty_id = duties.duty_id
    WHERE duty_officers.officer_id = 1
    AND duties.date = '$currentDate'
    AND duties.start_time <= '$currentTime'
    AND duties.end_time >= '$currentTime'
";

echo "Query:\n$activeDutyQuery\n\n";

$activeDuty = $db->query($activeDutyQuery)->getRowArray();
if ($activeDuty) {
    echo "FOUND ACTIVE DUTY:\n";
    echo "Duty ID: {$activeDuty['duty_id']}\n";
    echo "Point: {$activeDuty['point_name']}\n";
    echo "Time: {$activeDuty['start_time']} - {$activeDuty['end_time']}\n";
} else {
    echo "NO ACTIVE DUTY FOUND\n";
    
    // Check if officer 1 is assigned to duty 19
    $assignment = $db->query("SELECT * FROM duty_officers WHERE officer_id = 1 AND duty_id = 19")->getRowArray();
    if ($assignment) {
        echo "Officer 1 IS assigned to duty 19\n";
    } else {
        echo "Officer 1 is NOT assigned to duty 19\n";
    }
}

echo "\n=== DEBUG COMPLETE ===\n";
?>
