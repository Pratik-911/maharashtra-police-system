<?php
// Simple script to verify if officers are being assigned to duties
require_once 'ci4-framework/app/Config/Database.php';

// Database connection
$db = \Config\Database::connect();

echo "=== DUTY ALLOCATION VERIFICATION ===\n\n";

// Check recent duties
echo "Recent Duties:\n";
$duties = $db->query("SELECT duty_id, date, shift, point_id, created_at FROM duties ORDER BY created_at DESC LIMIT 5")->getResultArray();
foreach ($duties as $duty) {
    echo "Duty ID: {$duty['duty_id']}, Date: {$duty['date']}, Shift: {$duty['shift']}, Created: {$duty['created_at']}\n";
}

echo "\n";

// Check duty_officers assignments
echo "Duty-Officer Assignments:\n";
$assignments = $db->query("
    SELECT do.duty_id, do.officer_id, o.name, o.badge_no, d.date, d.shift
    FROM duty_officers do 
    JOIN officers o ON o.id = do.officer_id 
    JOIN duties d ON d.duty_id = do.duty_id 
    ORDER BY do.created_at DESC LIMIT 10
")->getResultArray();

if (empty($assignments)) {
    echo "NO OFFICER ASSIGNMENTS FOUND!\n";
} else {
    foreach ($assignments as $assignment) {
        echo "Duty {$assignment['duty_id']} ({$assignment['date']} {$assignment['shift']}) -> Officer {$assignment['officer_id']} ({$assignment['name']}, Badge: {$assignment['badge_no']})\n";
    }
}

echo "\n";

// Check for duties without officers
echo "Duties without Officer Assignments:\n";
$unassigned = $db->query("
    SELECT d.duty_id, d.date, d.shift, p.name as point_name
    FROM duties d 
    LEFT JOIN duty_officers do ON d.duty_id = do.duty_id 
    LEFT JOIN points p ON d.point_id = p.point_id
    WHERE do.duty_id IS NULL 
    ORDER BY d.created_at DESC
")->getResultArray();

if (empty($unassigned)) {
    echo "All duties have officer assignments.\n";
} else {
    foreach ($unassigned as $duty) {
        echo "Duty {$duty['duty_id']} ({$duty['date']} {$duty['shift']}) at {$duty['point_name']} - NO OFFICERS ASSIGNED\n";
    }
}

echo "\n=== VERIFICATION COMPLETE ===\n";
?>
