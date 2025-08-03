<?php

// Test script to verify duty allocation functionality and logging
require_once 'app/Config/Paths.php';
$paths = new Config\Paths();
require_once $paths->systemDirectory . '/Boot.php';

// Initialize CodeIgniter
$app = \CodeIgniter\Config\Services::codeigniter();
$app->initialize();

// Load models
$dutyModel = new \App\Models\DutyModel();
$dutyOfficerModel = new \App\Models\DutyOfficerModel();
$officerModel = new \App\Models\OfficerModel();
$pointModel = new \App\Models\PointModel();

echo "=== Testing Duty Allocation Functionality ===\n\n";

// Test 1: Check if we have officers and points in the database
echo "1. Checking available officers...\n";
$officers = $officerModel->findAll();
echo "Found " . count($officers) . " officers\n";
if (count($officers) > 0) {
    foreach ($officers as $officer) {
        echo "  - Officer ID: {$officer['id']}, Name: {$officer['name']}, Badge: {$officer['badge_no']}\n";
    }
}

echo "\n2. Checking available points...\n";
$points = $pointModel->findAll();
echo "Found " . count($points) . " points\n";
if (count($points) > 0) {
    foreach ($points as $point) {
        echo "  - Point ID: {$point['point_id']}, Name: {$point['name']}\n";
    }
}

// Test 2: Create a test duty if we have officers and points
if (count($officers) > 0 && count($points) > 0) {
    echo "\n3. Testing duty creation and officer allocation...\n";
    
    $testDutyData = [
        'date' => date('Y-m-d'),
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'shift' => 'Day',
        'point_id' => $points[0]['point_id'],
        'comment' => 'Test duty for allocation testing',
        'location_tracking_enabled' => 1
    ];
    
    echo "Creating test duty with data: " . json_encode($testDutyData) . "\n";
    
    $duty_id = $dutyModel->insert($testDutyData);
    
    if ($duty_id) {
        echo "✓ Duty created successfully with ID: $duty_id\n";
        
        // Test officer allocation
        $officerIds = array_slice(array_column($officers, 'id'), 0, 2); // Take first 2 officers
        echo "Attempting to allocate officers: " . json_encode($officerIds) . "\n";
        
        $allocationResult = $dutyOfficerModel->assignOfficersToDuty($duty_id, $officerIds);
        
        if ($allocationResult) {
            echo "✓ Officers allocated successfully\n";
            
            // Verify allocation
            $assignedOfficers = $dutyOfficerModel->getOfficersByDuty($duty_id);
            echo "Verified allocation - " . count($assignedOfficers) . " officers assigned:\n";
            foreach ($assignedOfficers as $officer) {
                echo "  - {$officer['name']} (Badge: {$officer['badge_no']})\n";
            }
        } else {
            echo "✗ Officer allocation failed\n";
            echo "Model errors: " . json_encode($dutyOfficerModel->errors()) . "\n";
        }
        
        // Clean up - delete test duty
        echo "\nCleaning up test duty...\n";
        $dutyOfficerModel->where('duty_id', $duty_id)->delete();
        $dutyModel->delete($duty_id);
        echo "✓ Test duty cleaned up\n";
        
    } else {
        echo "✗ Failed to create duty\n";
        echo "Model errors: " . json_encode($dutyModel->errors()) . "\n";
    }
} else {
    echo "\n3. Cannot test duty allocation - insufficient data in database\n";
    echo "   Need at least 1 officer and 1 point to test\n";
}

echo "\n=== Test Complete ===\n";
echo "Check the log file for detailed logging output:\n";
echo "tail -f /Users/pratik/Documents/Projects/trafficP2/ci4-framework/writable/logs/log-" . date('Y-m-d') . ".log\n";
